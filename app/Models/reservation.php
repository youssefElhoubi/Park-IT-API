<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\parking;

class reservation extends Model
{
    protected $table = 'reservations';
    protected $fillable = [
        "user_id",
        "parking_id",
        "start_time",
        "end_time",
    ];
    public function Parcking(){
        return $this->belongsTo(Parking::class,'parking_id');
    }
}
