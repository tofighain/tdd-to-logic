<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelSpot extends Model
{
    use HasFactory;
    protected $fillable = ['travel_id', 'position', 'latitude', 'longitude'];

    protected $table = "travels_spots";

    public function travel() {
        return $this->belongsTo(Travel::class);
    }
}
