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

    public function index(ManufacturerDataTable $dataTable)
    {
        // $manufacturers = Manufacturer::all();
        // return View::make('car.manufacturer.index', compact('manufacturers'));
        return $dataTable->render('car.manufacturer.index');
    }


    public function create()
    {
        return view('car.manufacturer.create');
    }

    public function store(Request $request)
    {
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


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $manufacturer = Manufacturer::find($id);
        return View::make('car.manufacturer.edit', compact('manufacturer'));
    }

    public function update(Request $request, $id)
    {
        $manufacturer = Manufacturer::find($id);
        $manufacturer->name = $request->name;
        $manufacturer->save();

        return redirect()->route('manufacturers.index')->with('update', 'Updated Successfully!');
    }

    public function destroy($id)
    {
        Manufacturer::destroy($id);
        return redirect()->route('manufacturers.index')->with('deleted', 'Deleted Successfully!');
    }
}
