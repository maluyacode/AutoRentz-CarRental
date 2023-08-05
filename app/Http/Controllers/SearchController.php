<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Location;
use Spatie\Searchable\Search;
use Spatie\Searchable\ModelSearchAspect;
use Illuminate\Support\Facades\View;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchResults = (new Search())
            ->registerModel(Driver::class, ['fname', 'lname'])
            ->registerModel(Location::class, ['street', 'baranggay', 'city'])
            ->registerModel(Car::class, ['platenumber'])
            ->search(trim($request->search));

        return View::make('search-output', compact('searchResults'));
    }

    public function dataSearch()
    {
        $drivers = Driver::all();
        $cars = Car::with(['modelo'])->get();
        $locations = Location::all();
        return response()->json(['cars' => $cars, 'drivers' => $drivers, 'locations' => $locations]);
    }
}
