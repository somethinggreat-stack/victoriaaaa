<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurgundyClientIdentifier extends Model
{
    protected $fillable = ['burgundy_client_id', 'type', 'value'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(BurgundyClient::class, 'burgundy_client_id');
    }
}
