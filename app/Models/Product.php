<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shopify_id',
        'title',
        'handle',
        'body_html',
        'product_type',
        'vendor',
        'tags',
        'is_popular',
        'price',
        'compare_at_price',
        'discount_type',
        'discount_value',
        'discount_starts_at',
        'discount_ends_at',
        'available',
        'source_created_at',
        'source_updated_at',
    ];

    protected $casts = [
        'available' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
        'source_created_at' => 'datetime',
        'source_updated_at' => 'datetime',
    ];

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function hasActiveDiscount(): bool
    {
        if (!$this->discount_type || !$this->discount_value) {
            return false;
        }

        $now = now();
        if ($this->discount_starts_at && $now->lt($this->discount_starts_at)) {
            return false;
        }

        if ($this->discount_ends_at && $now->gt($this->discount_ends_at)) {
            return false;
        }

        return true;
    }

    public function discountedPrice(): ?float
    {
        if (!$this->price || !$this->hasActiveDiscount()) {
            return null;
        }

        $price = (float) $this->price;
        $value = (float) $this->discount_value;

        if ($this->discount_type === 'percent') {
            $percent = max(min($value, 100), 0);
            return round($price * (1 - ($percent / 100)), 2);
        }

        if ($this->discount_type === 'fixed') {
            return round(max($price - $value, 0), 2);
        }

        return null;
    }

    public function effectivePrice(): ?float
    {
        return $this->discountedPrice() ?? ($this->price ? (float) $this->price : null);
    }
}
