<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportItem extends Model
{
    use HasFactory;
    public function forwardeditem(){
        return $this->hasMany(Forwarded_item::class);
    }
    public function report(){
        return $this->hasOne(Report::class);
    }
}
