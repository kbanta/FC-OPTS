<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function purchaseorder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function orderitem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
