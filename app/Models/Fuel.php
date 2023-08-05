<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function cars()
    {
        $this->hasMany(Car::class, 'fuel_id', 'id');
    }
}
