<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class CreditRepairCloud
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl;
    protected ?string $assignedTo;
    protected ?string $agreement;

    public function __construct()
    {
        $this->apiKey     = (string) config('services.crc.api_key');
        $this->secretKey  = (string) config('services.crc.secret_key');
        $this->baseUrl    = rtrim((string) config('services.crc.base_url'), '/');
        $this->assignedTo = config('services.crc.assigned_to');
        $this->agreement  = config('services.crc.agreement');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '' && $this->secretKey !== '';
    }

    /**
     * Insert a new Client into Credit Repair Cloud.
     *
     * @param array $data Normalized client data
     * @return array{ok: bool, status: int, body: string, parsed: ?array, message: ?string}
     */
    public function insertClient(array $data): array
    {
        $xml = new SimpleXMLElement('<crcloud/>');
        $lead = $xml->addChild('lead');

        $this->addChildIfPresent($lead, 'type', $data['type'] ?? 'Client');
        $this->addChildIfPresent($lead, 'firstname',  $data['firstname']  ?? null);
        $this->addChildIfPresent($lead, 'lastname',   $data['lastname']   ?? null);
        $this->addChildIfPresent($lead, 'middlename', $data['middlename'] ?? null);
        $this->addChildIfPresent($lead, 'suffix',     $data['suffix']     ?? null);
        $this->addChildIfPresent($lead, 'email',      $data['email']      ?? null);

        // Phone numbers — strip to digits, send as mobile
        if (!empty($data['phone'])) {
            $digits = preg_replace('/\D+/', '', $data['phone']);
            $this->addChildIfPresent($lead, 'phone_mobile', $digits);
        }

        $this->addChildIfPresent($lead, 'street_address', $data['street_address'] ?? null);
        $this->addChildIfPresent($lead, 'city',           $data['city']           ?? null);
        $this->addChildIfPresent($lead, 'state',          isset($data['state']) ? strtoupper($data['state']) : null);
        $this->addChildIfPresent($lead, 'zip',            $data['zip']            ?? null);

        if (!empty($data['ssn'])) {
            $this->addChildIfPresent($lead, 'ssno', preg_replace('/\D+/', '', $data['ssn']));
        }

        $this->addChildIfPresent($lead, 'birth_date', $data['birth_date'] ?? null);
        $this->addChildIfPresent($lead, 'memo',       $data['memo']       ?? null);

        if ($this->assignedTo) {
            $lead->addChild('client_assigned_to', $this->assignedTo);
        }

        // Portal access is only allowed when type === "Client" per CRC error 4412.
        // For Leads we skip it entirely.
        $type = $data['type'] ?? 'Client';
        if ($type === 'Client' && !empty($data['email'])) {
            $lead->addChild('client_portal_access', 'on');
            $lead->addChild('client_userid', htmlspecialchars($data['email'], ENT_XML1 | ENT_QUOTES, 'UTF-8'));
            if ($this->agreement) {
                $lead->addChild('client_agreement', $this->agreement);
            }
            $lead->addChild('send_setup_password_info_via_email', 'yes');
        }

        return $this->postXml('/api/lead/insertRecord', $xml);
    }

    /**
     * Low-level: post XML to a CRC endpoint and parse the response.
     */
    public function postXml(string $path, SimpleXMLElement $xml): array
    {
        $url = $this->baseUrl . $path;
        $body = $xml->asXML();

        $response = Http::asForm()
            ->timeout(30)
            ->post($url, [
                'apiauthkey' => $this->apiKey,
                'secretkey'  => $this->secretKey,
                'xmlData'    => $body,
            ]);

        $rawBody = $response->body();

        $parsed = null;
        try {
            $parsed = $rawBody !== '' ? json_decode(json_encode(simplexml_load_string($rawBody)), true) : null;
        } catch (\Throwable $e) {
            $parsed = null;
        }

        // Start from HTTP success — then narrow based on the XML body.
        $ok = $response->successful();
        $message = null;
        $errorNo = null;

        if ($parsed) {
            // Common <success>True|False</success> envelope
            $successVal = $parsed['success'] ?? $parsed['Success'] ?? null;
            if (is_array($successVal)) $successVal = (string) reset($successVal);
            if (is_string($successVal) && strtolower(trim($successVal)) === 'false') {
                $ok = false;
            }

            // Pull nested <result><errors><error_no/error_message></errors></result>
            $errors = $parsed['result']['errors'] ?? $parsed['Result']['errors'] ?? null;
            if (is_array($errors)) {
                $errorNo = $errors['error_no']      ?? $errors['ErrorNo']      ?? null;
                $message = $errors['error_message'] ?? $errors['ErrorMessage'] ?? null;
                if (is_array($errorNo)) $errorNo = (string) reset($errorNo);
                if (is_array($message)) $message = (string) reset($message);
                if ($errorNo) $ok = false;
            }

            // Fallback locations
            $message = $message
                ?? ($parsed['message'] ?? null)
                ?? ($parsed['Message'] ?? null)
                ?? ($parsed['result']['message'] ?? null);
            if (is_array($message)) $message = (string) reset($message);
        }

        // Defensive: any "error" marker in the raw body should fail us
        if (stripos($rawBody, '<error_no>') !== false
            || stripos($rawBody, '<errorcode>') !== false) {
            $ok = false;
        }

        Log::channel('stack')->info('CRC request', [
            'url'    => $url,
            'status' => $response->status(),
            'sent'   => $body,
            'body'   => $rawBody,
            'ok'     => $ok,
        ]);

        return [
            'ok'      => $ok,
            'status'  => $response->status(),
            'body'    => $rawBody,
            'parsed'  => $parsed,
            'message' => $message,
        ];
    }

    protected function addChildIfPresent(SimpleXMLElement $parent, string $name, $value): void
    {
        if ($value === null || $value === '') return;
        // Escape special chars for safe XML
        $parent->addChild($name, htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
    }
}
