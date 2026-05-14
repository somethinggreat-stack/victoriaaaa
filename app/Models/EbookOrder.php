<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbookOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ebook_id',
        'ebook_slug',
        'ebook_title',
        'amount',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'invoice_number',
        'transaction_id',
        'auth_code',
        'status',
        'marketing_opt_in',
        'ip_address',
        'user_agent',
        'charged_at',
        'downloaded_at',
        'download_count',
        'raw_payload',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'marketing_opt_in' => 'boolean',
        'charged_at'       => 'datetime',
        'downloaded_at'    => 'datetime',
        'raw_payload'      => 'array',
        'download_count'   => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
