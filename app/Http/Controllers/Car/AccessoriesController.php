<?php

namespace App\Http\Controllers\Car;

use App\DataTables\AccessorieDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Imports\AccessoriesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

use App\Models\Accessorie;

class AccessoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accessories = Accessorie::with(['media'])->get();
        return response()->json($accessories);
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

    public function storeMedia(Request $request)
    {
        $path = storage_path("accessories/images");
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

    public function viewMedia($id)
    {
        $accessory = Accessorie::find($id);
        $accessory->getMedia('images');
        return response()->json($accessory);
    }

    public function store(Request $request)
    {
        // Validator::make(
        //     $request->all(),
        //     [
        //         'name' => 'required|min:5',
        //         'fee' => 'required|numeric',
        //         'image_path' => 'required',
        //         'image_path.*' => '|mimes:jpeg,png,jpg',
        //     ],
        //     [
        //         'image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.',
        //         'image_path.required' => 'The image(s) field is required.'
        //     ]
        // )->validate();
        // if ($request->file()) {
        //     foreach ($request->image_path as $images) {
        //         $fileName = time() . '_' . $images->getClientOriginalName();
        //         // dd($fileName);
        //         $path = Storage::putFileAs('public/images', $images, $fileName);
        //         $filenames[] = $fileName;
        //     }
        //     $image_path = implode("=", $filenames);
        // }
        $accessory = Accessorie::create([
            "name" => $request->name,
            "fee" => $request->fee,
            "image_path" => 'Switched in media',
        ]);

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $accessory->addMedia(storage_path("accessories/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $newAccessory = Accessorie::with(['media'])->where('id', $accessory->id)->first();

        return response()->json($newAccessory);
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

    public function deleteMedia(Request $request, $id)
    {
        $modelID = $request->id;
        DB::table('media')->where('id', $id)->delete();

        $model = Accessorie::find($modelID);
        $model->getMedia('images');

        return response()->json($model);
    }

    public function edit($id)
    {
        $accessory = Accessorie::with('media')->find($id);
        return response()->json($accessory);
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
        // Validator::make(
        //     $request->all(),
        //     [
        //         'name' => 'required|min:5',
        //         'fee' => 'required|numeric',
        //         'image_path.*' => '|mimes:jpeg,png,jpg',
        //     ],
        //     [
        //         'image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.',
        //     ]
        // )->validate();
        // if ($request->file()) {
        //     foreach ($request->image_path as $images) {
        //         $fileName = time() . '_' . $images->getClientOriginalName();
        //         // dd($fileName);
        //         $path = Storage::putFileAs('public/images', $images, $fileName);
        //         $filenames[] = $fileName;
        //     }
        //     $image_path = implode("=", $filenames);
        //     Accessorie::whereId($id)->update([
        //         "image_path" => $image_path,
        //     ]);
        // }
        $accessory = Accessorie::find($id)->update([
            "name" => $request->name,
            "fee" => $request->fee
        ]);

        $accessory = Accessorie::find($id);
        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $accessory->addMedia(storage_path("accessories/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $updatedAccessory = Accessorie::with(['media'])->where('id', $accessory->id)->first();
        return response()->json($updatedAccessory);
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
        DB::table('media')->where('model_id', $id)->where('model_type', 'App\Models\Accessorie')->delete();
        return response()->json([]);
    }

    public function import(Request $request)
    {
        Excel::import(new AccessoriesImport, $request->excel);
        return redirect()->route('accessories.index')->with('success', 'All goods na!');
    }
}
