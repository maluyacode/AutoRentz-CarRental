<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
