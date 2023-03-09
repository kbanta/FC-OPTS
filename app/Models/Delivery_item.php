<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_item extends Model
{
    use HasFactory;
    public function purchaseorder(){
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function deliveryno(){
        return $this->belongsTo(Delivery::class);
    }
    public function staff(){
        return $this->hasOne(Staff::class);
    }
}
