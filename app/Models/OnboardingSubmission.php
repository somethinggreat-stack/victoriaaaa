<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OnboardingSubmission extends Model
{
    protected $fillable = [
        'firstname', 'lastname', 'middlename', 'suffix',
        'email', 'phone',
        'street_address', 'city', 'state', 'zip',
        'ssn',                            // virtual — mutator splits into ssn_encrypted + ssn_last4
        'ssn_encrypted', 'ssn_last4',
        'birth_date',
        'crc_status', 'crc_id', 'crc_response',
        'status',
        'ip', 'user_agent',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Virtual `ssn` attribute that transparently encrypts/decrypts. Never persisted directly.
     */
    protected function ssn(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->ssn_encrypted) return null;
                try { return Crypt::decryptString($this->ssn_encrypted); }
                catch (\Throwable $e) { return null; }
            },
            set: function ($value) {
                $digits = preg_replace('/\D+/', '', (string) $value);
                return [
                    'ssn_encrypted' => Crypt::encryptString($digits),
                    'ssn_last4'     => substr($digits, -4),
                ];
            },
        );
    }

    public function getMaskedSsnAttribute(): string
    {
        return $this->ssn_last4 ? '•••-••-' . $this->ssn_last4 : '—';
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->firstname, $this->middlename, $this->lastname, $this->suffix])));
    }
}
