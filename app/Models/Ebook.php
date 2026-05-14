<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'price',
        'cover_image',
        'drive_link',
        'features',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'features'   => 'array',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(EbookOrder::class);
    }

    public function paidOrders(): HasMany
    {
        return $this->hasMany(EbookOrder::class)->where('status', 'paid');
    }

    public function isReady(): bool
    {
        return $this->is_active && !empty($this->drive_link);
    }
}
