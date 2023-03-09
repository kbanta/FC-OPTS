<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemQuantity extends Model
{
    use HasFactory;
    public function orderitem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
