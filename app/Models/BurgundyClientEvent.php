<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurgundyClientEvent extends Model
{
    protected $fillable = ['burgundy_client_id', 'event_type', 'note', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(BurgundyClient::class, 'burgundy_client_id');
    }
}
