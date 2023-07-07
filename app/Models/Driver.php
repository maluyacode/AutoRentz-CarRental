<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Driver extends Model implements Searchable
{
    use HasFactory;
    protected $fillable = ["fname", "lname", "licensed_no", "description", "address", "image_path", "driver_status"];

    public function getSearchResult(): SearchResult
    {
        $url = route('driver.details', $this->id);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->fname . ' ' . $this->lname,
            $url
        );
    }
}
