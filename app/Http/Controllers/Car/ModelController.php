<?php

namespace App\Http\Controllers\Car;

use App\DataTables\ModeloDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modelo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Manufacturer;
use App\Models\Type;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ModeloDataTable $dataTable)
    {
        // $modelos = DB::table('manufacturers as ma')
        //     ->join('modelos as mo', 'ma.id', 'mo.manufacturer_id')
        //     ->join('types as ty', 'ty.id', 'mo.type_id')
        //     ->select('mo.*', 'ty.name as typename', 'ma.name as manuname')
        //     ->get();
        // return View::make('car.model.index', compact('modelos'));

        return $dataTable->render('car.model.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $manufacturers = Manufacturer::all();
        $types = Type::all();
        return View::make('car.model.create', compact('manufacturers', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->type);
        $rules = [
            'name' => 'required|min:2',
            'modelyear' => 'required|min:2|numeric'
        ];
        $messages = [
            'name.required' => 'Model name required',
            'name.min' => 'Atleast more than 8 characters',
            'modelyear.required' => 'Model year required',
        ];
        Validator::make($request->all(), $rules, $messages)->validate();
        Modelo::create([
            "name" => $request->name,
            "year" => $request->modelyear,
            "manufacturer_id" => $request->manufacturer,
            "type_id" => $request->type,
        ]);
        return redirect()->route('model.index');
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
        $model =  DB::table('manufacturers as ma')
            ->join('modelos as mo', 'ma.id', 'mo.manufacturer_id')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->select('mo.*', 'ty.name as typename', 'ma.name as manuname')
            ->where('mo.id', $id)->first();
        $manufacturers = Manufacturer::whereNotIn('id', [$model->manufacturer_id])->get();
        $types = Type::whereNotIn('id', [$model->type_id])->get();
        // dd($model);
        return View::make('car.model.edit', compact('model', 'manufacturers', 'types'));
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
        // dd($request);
        $rules = [
            'name' => 'required|min:2',
            'modelyear' => 'required|min:2|numeric'
        ];
        $messages = [
            'name.required' => 'Model name required',
            'name.min' => 'Atleast more than 8 characters',
            'modelyear.required' => 'Model year required',
        ];
        Validator::make($request->all(), $rules, $messages)->validate();
        $model = Modelo::find($id);
        $model->name = $request->name;
        $model->year = $request->modelyear;
        $model->manufacturer_id = $request->manufacturer;
        $model->type_id = $request->type;
        $model->save();
        return redirect()->route('model.index')->with("update", "Updated Succesfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Modelo::destroy($id);
        return redirect()->route('model.index')->with("deleted", "Deleted Succesfully!");
    }
}
