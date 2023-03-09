<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeniedRequest extends Model
{
    use HasFactory;
    public function purchaserequest(){
        return $this->belongsTo(PurchaseRequest::class);
    }
}
