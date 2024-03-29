<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Type;
use App\Models\Manufacturer;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'year', 'manufacturer_id', 'type_id'];
    // protected $guarded = ['type_id'];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function modelos()
    {
        return $this->hasMany(Car::class, 'modelos_id', 'id');
    }
}
