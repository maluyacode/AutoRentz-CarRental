<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Car;
use App\Models\Driver;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $searchable = [
        'id',
        'car.platenumber',
        'customer.name',
        'customer.email',
        'customer.phone',
        'pickuplocation.name',
        'returnlocation.name',
        'start_date', // added
        'end_date', // added
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function driver(){
        return $this->belongsTo(Driver::class);
    }

    public function picklocation(){
        return $this->belongsTo(Location::class, 'pickup_location_id', 'id');
    }
    public function returnlocation(){
        return $this->belongsTo(Location::class, 'return_location_id', 'id');
    }

    public function car(){
        return $this->belongsTo(Car::class);
    }
}
