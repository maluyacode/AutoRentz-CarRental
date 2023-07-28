<?php

namespace App\Http\Controllers;

use App\DataTables\LocationDataTable;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
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
                'image_path' => 'required',
                'image_path.*' => '|mimes:jpeg,png,jpg',
            ],
            ['image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.']
        )->validate();

        $location = new Location;
        $location->street = $request->street;
        $location->baranggay = $request->baranggay;
        $location->city = $request->city;

        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                // dd($fileName);
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
            $location->image_path = $image_path;
        }
        $location->save();
        return redirect()->route('location.index')->with('created', 'Created successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $location = Location::find($id);
        return View::make('location.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            [
                'street' => 'required|min:2',
                'baranggay' => 'required|min:4',
                'city' => 'required|min:4',
                'image_path.*' => '|mimes:jpeg,png,jpg',
            ],
            ['image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.']
        )->validate();

        $location = Location::find($id);
        $location->street = $request->street;
        $location->baranggay = $request->baranggay;
        $location->city = $request->city;

        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                // dd($fileName);
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
            $location->image_path = $image_path;
        }
        $location->save();
        return redirect()->route('location.index')->with('update', 'Updated successfully');
    }

    public function destroy($id)
    {
        Location::destroy($id);
        return redirect()->route('location.index')->with('deleted', 'Deleted successfully');
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
