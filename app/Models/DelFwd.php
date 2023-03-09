<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelFwd extends Model
{
    use HasFactory;
    public function delivery(){
        return $this->belongsTo(Deliveries::class);
    }
    public function forward(){
        return $this->belongsTo(Forwarded::class);
    }

}
