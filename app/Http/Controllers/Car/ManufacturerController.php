<?php

namespace App\Http\Controllers\Car;

use App\DataTables\ManufacturerDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Manufacturer;
use Barryvdh\Debugbar\Facades\Debugbar;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManufacturerDataTable $dataTable)
    {
        // $manufacturers = Manufacturer::all();
        // return View::make('car.manufacturer.index', compact('manufacturers'));
        return $dataTable->render('car.manufacturer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('car.manufacturer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->file());
        // $manufacturer = Manufacturer::create([
        //     'name' => $request->name,
        // ])->save();
        $manufacturer = new Manufacturer;
        $manufacturer->name = $request->name;
        $manufacturer->save();

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $manufacturer->addMedia(storage_path("manufacturer/images/" . $file))->toMediaCollection("images");
            }
        }

        return redirect()->route('manufacturers.index')->with('created', 'Created Successly!');
    }

    public function storeMedia(Request $request)
    {
        Debugbar::info($request->file());
        $path = storage_path("manufacturer/images");
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
        $manufacturer = Manufacturer::find($id);
        return View::make('car.manufacturer.edit', compact('manufacturer'));
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
        $manufacturer = Manufacturer::find($id);
        $manufacturer->name = $request->name;
        $manufacturer->save();

        return redirect()->route('manufacturers.index')->with('update', 'Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Manufacturer::destroy($id);
        return redirect()->route('manufacturers.index')->with('deleted', 'Deleted Successfully!');
    }
}
