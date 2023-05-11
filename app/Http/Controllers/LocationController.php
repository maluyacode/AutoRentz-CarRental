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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LocationDataTable $dataTable)
    {
        return $dataTable->render('drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $location = Location::find($id);
        return View::make('location.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Location::destroy($id);
        return redirect()->route('location.index')->with('deleted', 'Deleted successfully');
    }

    public function locationlists(Request $request, $data){
        $searchValue = '';
        if($data == 'all'){
            $seachValue = null;
        }else{
            $searchValue = $request->searchInput;
        }
        $locations = DB::table('locations')
        ->where('street', 'LIKE', "%{$searchValue}%")
        ->orWhere('baranggay', 'LIKE', "%{$searchValue}%")
        ->orWhere('city', 'LIKE', "%{$searchValue}%")->get();
        return View::make('locations', compact('locations'));
    }
}
