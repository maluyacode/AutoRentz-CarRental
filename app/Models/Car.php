<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Accessorie;
use App\Models\Modelo;
use App\Models\Transmission;
use App\Models\Fuel;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'platenumber',
        'price_per_day',
        'seats',
        'description',
        'image_path',
        'cost_price',
        'modelos_id',
        'transmission_id',
        'fuel_id',
        'car_status'
    ];

    public function modelo() :BelongsTo{
        return $this->belongsTo(Modelo::class, 'modelos_id');
    }

    public function transmission(){
        return $this->belongsTo(Transmission::class);
    }

    public function fuel(){
        return $this->belongsTo(Fuel::class);
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessorie::class);
    }

    public function bookings(){
        return $this->hasMany(Booking::class);
    }
}
