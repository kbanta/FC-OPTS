<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forwarded_item extends Model
{
    use HasFactory;
    public function deliveryno(){
        return $this->belongsTo(Delivery::class);
    }
}
