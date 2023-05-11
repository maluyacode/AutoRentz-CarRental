<?php

namespace App\Http\Controllers\Car;

use App\DataTables\AccessorieDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Accessorie;

class AccessoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AccessorieDataTable $dataTable)
    {
        $accessories = new Accessorie();
        return $dataTable->render('car.accessories.index', compact('accessories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('car.accessories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        Validator::make(
            $request->all(),
            [
                'name' => 'required|min:5',
                'fee' => 'required|numeric',
                'image_path' => 'required',
                'image_path.*' => '|mimes:jpeg,png,jpg',
            ],
            [
                'image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.',
                'image_path.required' => 'The image(s) field is required.'
            ]
        )->validate();
        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                // dd($fileName);
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
        }
        Accessorie::create([
            "name" => $request->name,
            "fee" => $request->fee,
            "image_path" => $image_path
        ]);
        return redirect()->route('accessories.index')->with("created", "New accessory created successfully");
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
        $accessory = Accessorie::find($id);
        return View::make('car.accessories.edit', compact('accessory'));
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
                'name' => 'required|min:5',
                'fee' => 'required|numeric',
                'image_path.*' => '|mimes:jpeg,png,jpg',
            ],
            [
                'image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.',
            ]
        )->validate();
        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                // dd($fileName);
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
            Accessorie::whereId($id)->update([
                "image_path" => $image_path,
            ]);
        }
        Accessorie::find($id)->update([
            "name" => $request->name,
            "fee" => $request->fee
        ]);
        return redirect()->route('accessories.index')->with("update", "Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Accessorie::destroy($id);
        return redirect()->route('accessories.index')->with("deleted", "Deleted Successfully");
    }
}
