<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use App\Models\Car;

class Accessorie extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'fee', 'image_path'];

    public function access(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }
    public function accessory($id)
    {
        // dd($id);
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->where('ca.id', $id)
            ->get();
        return $accessory;
    }
}
