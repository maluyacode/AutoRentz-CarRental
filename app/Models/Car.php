<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Accessorie;

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
    public function accessories(): BelongsToMany
    {
        return $this->belongsToMany(Accessorie::class);
    }
}
