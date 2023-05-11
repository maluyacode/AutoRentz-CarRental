<?php

namespace App\Http\Controllers\Car;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use App\DataTables\TransmissionDataTable;

use App\Models\Transmission;

class TransmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransmissionDataTable $dataTable)
    {
        // $transmissions = Transmission::all();
        // return View::make('car.transmission.index', compact('transmissions'));
        return $dataTable->render('car.transmission.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('car.transmission.create');
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
            'name.required' => 'Transmission name required',
            'name.min' => 'Atleast more than 5 characters',
        ];

        Validator::make($request->all(), $rules, $messages)->validate();

        Transmission::create([
            "name" => $request->name,
        ]);
        return redirect()->route('transmission.index')->with("created", "New transmission type sucessfully created!");
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
        $transmission = Transmission::find($id);
        return View::make('car.transmission.edit', compact('transmission'));
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
            'name.required' => 'Transmission name required',
            'name.min' => 'Atleast more than 5 characters',
        ];
        Validator::make($request->all(), $rules, $messages)->validate();

        Transmission::find($id)->update([
            "name" => $request->name,
        ]);
        return redirect()->route('transmission.index')->with("update", "Updated Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Transmission::destroy($id);
        return redirect()->route('transmission.index')->with("deleted", "Deleted Successfully!");
    }
}
