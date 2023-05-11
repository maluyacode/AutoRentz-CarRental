<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public function accessLocation($id)
    {
        return $this->where('id', $id)->first();
    }

    public function hasLocation()
    {
        return $this->hasMany(Booking::class);
    }
}
