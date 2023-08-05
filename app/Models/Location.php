<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Location extends Model implements Searchable, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public $fillable = ["street", "baranggay", "city", "image_path"];

    public function accessLocation($id)
    {
        return $this->where('id', $id)->first();
    }

    public function hasLocation()
    {
        return $this->hasMany(Booking::class);
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('locations.show', $this->id);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->street . ' ' . $this->baranggay . ' ' . $this->city,
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
