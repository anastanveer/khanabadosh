<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'shopify_id',
        'title',
        'sku',
        'option1',
        'option2',
        'option3',
        'price',
        'compare_at_price',
        'inventory_quantity',
        'position',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'inventory_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
