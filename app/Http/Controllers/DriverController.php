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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DriverDataTable $dataTable)
    {
        // dd($dataTable.destroy());
        return $dataTable->render('drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('drivers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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

        return response()->json(["created" => "Driver Added!", "code" => 200]);
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
        $driver = Driver::find($id);
        // return View::make('drivers.edit', compact('driver'));
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
        // dd($request->image_path);
        Validator::make(
            $request->all(),
            [
                'fname' => 'required|min:2',
                'lname' => 'required|min:4',
                'licensed_no' => 'required|min:4',
                'description' => 'required|min:10|max:400',
                'image_path.*' => '|mimes:jpeg,png,jpg',
                'address' => 'required|min:5',
            ],
            ['image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.']
        )->validate();

        $driver = Driver::find($id);
        $driver->fname = $request->fname;
        $driver->lname = $request->lname;
        $driver->licensed_no = $request->licensed_no;
        $driver->description = $request->description;
        $driver->address = $request->address;
        // dd($filenames);
        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                // dd($fileName);
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
            $driver->image_path = $image_path;
        }
        $driver->save();
        return redirect()->route('drivers.index')->with('update', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Driver::destroy($id);
        return redirect()->route('drivers.index')->with('deleted', 'Deleted successfully');
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
        // dd($searchValue);
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

        return redirect()->route('drivers.index')->with('success', 'Imported na ang data');
    }
}
