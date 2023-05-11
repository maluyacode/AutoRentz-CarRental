<?php

namespace App\Http\Controllers\Car;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use App\DataTables\FuelDataTable;
use App\Models\Fuel;

class FuelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FuelDataTable $dataTable)
    {
        // $fuels = Fuel::all();
        // return View::make('car.fuel.index', compact('fuels'));
        return $dataTable->render('car.fuel.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('car.fuel.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:5',
        ];
        $messages = [
            'name.required' => 'Fuel name required',
            'name.min' => 'Atleast more than 5 characters',
        ];

        Validator::make($request->all(), $rules, $messages)->validate();

        Fuel::create([
            "name" => $request->name,
        ]);
        return redirect()->route('fuel.index')->with("created", "New fuel type sucessfully created!");
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
        $fuel = Fuel::find($id);
        return View::make('car.fuel.edit', compact('fuel'));
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
        $rules = [
            'name' => 'required|min:5',
        ];
        $messages = [
            'name.required' => 'Fuel name required',
            'name.min' => 'Atleast more than 5 characters',
        ];
        Validator::make($request->all(), $rules, $messages)->validate();

        Fuel::find($id)->update([
            "name" => $request->name,
        ]);
        // if (array_key_exists(user_id, id) && array_key_exists(prod_id, id))
        // Cart::where(user_id,id)->where(prod_id, id)->update([
        //     "quantity"
        // ]);
        // else{

        // }
        return redirect()->route('fuel.index')->with("update", "Updated Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Fuel::destroy($id);
        return redirect()->route('fuel.index')->with("deleted", "Deleted Successfully!");
    }
}
