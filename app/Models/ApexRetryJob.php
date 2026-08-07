<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ApexRetryJob extends Model
{
    /** Private disk the documents live on. */
    public const DISK = 'local';

    protected $fillable = [
        'onboarding_submission_id', 'client_name', 'email',
        'payload_encrypted',
        'drivers_license_path', 'proof_of_address_path', 'ssn_card_path',
        'status', 'attempts', 'last_error', 'last_attempt_at', 'succeeded_at', 'apex_id',
    ];

    protected $casts = [
        // Laravel encrypts/decrypts transparently at rest.
        'payload_encrypted' => 'encrypted',
        'last_attempt_at'   => 'datetime',
        'succeeded_at'      => 'datetime',
    ];

    /** Decoded funnel payload: ['v' => [...funnel fields...], 'dob' => 'YYYY-MM-DD']. */
    public function payload(): array
    {
        return json_decode($this->payload_encrypted ?? '{}', true) ?: [];
    }

    /** Open read streams for the stored documents, keyed by Apex field name. */
    public function fileStreams(): array
    {
        $map = [
            'drivers_license'  => $this->drivers_license_path,
            'proof_of_address' => $this->proof_of_address_path,
            'ssn_card'         => $this->ssn_card_path,
        ];

        $streams = [];
        foreach ($map as $apexName => $path) {
            if ($path && Storage::disk(self::DISK)->exists($path)) {
                $streams[$apexName] = [
                    'stream'   => Storage::disk(self::DISK)->readStream($path),
                    'filename' => basename($path),
                ];
            }
        }

        return $streams;
    }

    /** Delete the stored documents from disk (after a successful retry). */
    public function purgeFiles(): void
    {
        foreach ([$this->drivers_license_path, $this->proof_of_address_path, $this->ssn_card_path] as $path) {
            if ($path && Storage::disk(self::DISK)->exists($path)) {
                Storage::disk(self::DISK)->delete($path);
            }
        }
    }
}
