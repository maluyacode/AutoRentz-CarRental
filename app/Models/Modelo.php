<?php

namespace App\Models;
use App\Models\Type;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'year', 'manufacturer_id', 'type_id'];
    // protected $guarded = ['type_id'];

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class);
    }
}
