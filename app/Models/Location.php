<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Location extends Model implements Searchable
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

    public function getSearchResult(): SearchResult
    {
        $url = route('locations', $this->id);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->street . ' ' . $this->baranggay . ' ' . $this->city,
            $url
        );
    }
}
