<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'name', 'email', 'phone',
        'address_line1', 'address_line2', 'city', 'state', 'postal_code',
        'country', 'notes', 'payment_method', 'payment_status', 'status',
        'subtotal', 'total',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}