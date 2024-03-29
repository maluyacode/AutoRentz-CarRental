<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\DriverDataTable;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DriversImport;
use Barryvdh\Debugbar\Facades\Debugbar;

class DriverController extends Controller
{

    public function index(DriverDataTable $dataTable)
    {
        $drivers = Driver::with(['media'])->whereKeyNot(1)->get();
        return response()->json($drivers);
    }


    public function create()
    {
        return view('drivers.create');
    }


    public function viewMedia($id)
    {
        $driver = Driver::find($id);
        $driver->getMedia('images');
        return response()->json($driver);
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path("drivers/images");
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

    public function deleteMedia(Request $request, $id)
    {
        $driverID = $request->id;
        DB::table('media')->where('id', $id)->delete();

        $driver = Driver::find($driverID);
        $driver->getMedia('images');

        return response()->json($driver);
    }

    public function store(Request $request)
    {
        Debugbar::info($request);
        Validator::make(
            $request->all(),
            [
                'firstname' => 'required|min:2',
                'lastname' => 'required|min:4',
                'licensed_no' => 'required|min:4',
                'description' => 'required|min:10|max:400',
                'address' => 'required|min:5',
            ],
        )->validate();

        $driver = new Driver;
        $driver->fname = $request->firstname;
        $driver->lname = $request->lastname;
        $driver->licensed_no = $request->licensed_no;
        $driver->description = $request->description;
        $driver->address = $request->address;

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $driver->addMedia(storage_path("drivers/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $driver->save();
        $driver->getMedia('images');
        return response()->json($driver);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $driver = Driver::find($id);
        $driver->getMedia('images');
        // return View::make('drivers.edit', compact('driver'));
        return response()->json($driver);
    }


    public function update(Request $request, $id)
    {
        Debugbar::info($id);

        Validator::make(
            $request->all(),
            [
                'firstname' => 'required|min:2',
                'lastname' => 'required|min:4',
                'licensed_no' => 'required|min:4',
                'description' => 'required|min:10|max:400',
                'address' => 'required|min:5',
            ],
        )->validate();

        $driver = Driver::find($id);
        $driver->fname = $request->firstname;
        $driver->lname = $request->lastname;
        $driver->licensed_no = $request->licensed_no;
        $driver->description = $request->description;
        $driver->address = $request->address;

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $driver->addMedia(storage_path("drivers/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $driver->save();
        $driver->getMedia('images');

        return response()->json($driver);
    }


    public function destroy($id)
    {
        Driver::destroy($id);
        return response()->json([], 200, [], 0);
    }

    public function driverlists(Request $request, $data = 'all') // user view
    {
        $searchValue = '';
        if ($data == 'all') {
            $seachValue = null;
        } else if ($request->searchInput) {
            $searchValue = $request->searchInput;
        } else {
            $searchValue = $data && false;
        }

        $drivers = DB::table('drivers')
            ->whereNotIn('id', [1])
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('fname', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lname', 'LIKE', "%{$searchValue}%")
                    ->orWhere('address', 'LIKE', "%{$searchValue}%");
            })
            ->get();
        return View::make('drivers', compact('drivers'));
    }

    public function driverDetails($id)
    {
        $driver = Driver::find($id);
        return view('driver-details', compact('driver'));
    }

    public function import(Request $request)
    {
        Excel::import(new DriversImport, $request->excel);

        return response()->json([]);
    }
}
