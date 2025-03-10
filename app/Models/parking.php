<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\reservation;

class parking extends Model
{
    protected $table = "parking";
    protected $fillable = [
        "name",
        "total_spots",
        "available_spots"
    ];
    public function Resrvations(){
        return $this->hasMany(reservation::class,'parking_id');
    }
}
