<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use App\Models\Accessorie;
use App\Models\Modelo;
use App\Models\Transmission;
use App\Models\Fuel;
use App\Models\Booking;

class Car extends Model implements Searchable, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'modelos_id');
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessorie::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('cardetails', $this->id);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->platenumber,
            $url
        );
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }
}
