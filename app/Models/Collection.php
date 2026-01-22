<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'shopify_id',
        'title',
        'handle',
        'source_updated_at',
    ];

    protected $casts = [
        'source_updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
