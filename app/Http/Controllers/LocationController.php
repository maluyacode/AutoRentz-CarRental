<?php

namespace App\Http\Controllers;

use App\DataTables\LocationDataTable;
use App\Models\Location;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('media')->get();
        return response()->json($locations);
    }

    public function create()
    {
        return view('location.create');
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path("locations/images");
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file("file");
        $name = uniqid() . "_" . trim($file->getClientOriginalName());
        $file->move($path, $name);

        return response()->json([
            "name" => $name,
            "original_name" => $file->getClientOriginalName(),
        ]);
        // unlink($path);
    }

    public function store(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                'street' => 'required|min:2',
                'baranggay' => 'required|min:4',
                'city' => 'required|min:4',
            ],
        )->validate();

        $location = new Location;
        $location->street = $request->street;
        $location->baranggay = $request->baranggay;
        $location->city = $request->city;
        $location->image_path = 'in media table';
        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $location->addMedia(storage_path("locations/images/" . $file))->toMediaCollection("images");
            }
        }
        $location->save();
        $location->getMedia('images');
        // unlink(storage_path("locations/images/"));
        return response()->json($location);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $location = Location::find($id);
        $location->getMedia('images');
        return response()->json($location);
    }

    public function deleteMedia(Request $request, $id)
    {
        $modelID = $request->id;
        DB::table('media')->where('id', $id)->delete();

        $model = Location::find($modelID);
        $model->getMedia('images');

        return response()->json($model);
    }


    public function update(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            [
                'street' => 'required|min:2',
                'baranggay' => 'required|min:4',
                'city' => 'required|min:4',
            ],
        )->validate();

        $location = Location::find($id);
        $location->street = $request->street;
        $location->baranggay = $request->baranggay;
        $location->city = $request->city;

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $location->addMedia(storage_path("locations/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }
        $location->save();
        $location->getMedia('images');
        return response()->json($location);
    }

    public function destroy($id)
    {
        Location::destroy($id);
        return response()->json([]);
    }

    public function multidestroy(Request $request)
    {
        Location::destroy($request->multipleID);
        return response()->json($request);
    }

    public function locationlists(Request $request, $data = 'all')
    {
        $searchValue = '';
        if ($data == 'all') {
            $seachValue = null;
        } else {
            $searchValue = $request->searchInput;
        }
        $locations = DB::table('locations')
            ->where('street', 'LIKE', "%{$searchValue}%")
            ->orWhere('baranggay', 'LIKE', "%{$searchValue}%")
            ->orWhere('city', 'LIKE', "%{$searchValue}%")->get();
        return View::make('locations', compact('locations'));
    }
}
