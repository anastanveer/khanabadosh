<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'status',
        'customer_name',
        'email',
        'phone',
        'city',
        'address',
        'postal_code',
        'delivery_method',
        'payment_method',
        'payment_details',
        'payment_proof_path',
        'subtotal',
        'total',
        'currency',
        'items_count',
        'notes',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
