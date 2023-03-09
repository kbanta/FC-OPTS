<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierItem extends Model
{
    use HasFactory;
    public function item(){
        return $this->belongsTo(Item::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
