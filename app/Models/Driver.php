<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Driver extends Model implements Searchable
{
    use HasFactory;

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
