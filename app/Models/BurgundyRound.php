<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurgundyRound extends Model
{
    protected $fillable = [
        'burgundy_client_id', 'round_number', 'processed_at', 'cost', 'note',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'processed_at' => 'datetime',
        'cost'         => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(BurgundyClient::class, 'burgundy_client_id');
    }
}
