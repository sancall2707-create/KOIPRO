<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'table_number',
        'order_type', 'status', 'notes', 'total_price',
    ];

    protected $casts = ['total_price' => 'float'];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
