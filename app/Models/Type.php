<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function modelos()
    {
        $this->hasMany(Modelo::class, 'type_id', 'id');
    }
}
