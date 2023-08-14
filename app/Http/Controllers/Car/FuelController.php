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

    public function index(FuelDataTable $dataTable)
    {
        // $fuels = Fuel::all();
        // return View::make('car.fuel.index', compact('fuels'));
        return $dataTable->render('car.fuel.index');
    }

    public function create()
    {
        return view('car.fuel.create');
    }


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


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $fuel = Fuel::find($id);
        return View::make('car.fuel.edit', compact('fuel'));
    }


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

        return redirect()->route('fuel.index')->with("update", "Updated Successfully!");
    }

    public function destroy($id)
    {
        Fuel::destroy($id);
        return redirect()->route('fuel.index')->with("deleted", "Deleted Successfully!");
    }
}
