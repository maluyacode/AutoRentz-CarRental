<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Driver extends Model implements Searchable, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ["fname", "lname", "licensed_no", "description", "address", "image_path", "driver_status"];

    public function cars()
    {
        $this->hasMany(Car::class, 'driver_id', 'id');
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('driver.details', $this->id);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->fname . ' ' . $this->lname,
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
