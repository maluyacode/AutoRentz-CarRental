<?php

namespace App\Http\Controllers\Car;

use App\DataTables\TypeDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Type;


class TypeController extends Controller
{

    public function index(TypeDataTable $dataTable)
    {
        return $dataTable->render('car.type.index');
    }

    public function create()
    {
        return view('car.type.create');
    }

    public function store(Request $request)
    {
        Type::create([
            'name' => $request->name,
        ])->save();
        return redirect()->route('types.index')->with('created', 'Created Successly!');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $type = Type::find($id);
        return View::make('car.type.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = Type::find($id);
        $type->name = $request->name;
        $type->save();
        return redirect()->route('types.index')->with('update', 'Updated Successfully!');
    }

    public function destroy($id)
    {
        Type::destroy($id);
        return redirect()->route('types.index')->with('deleted', 'Deleted Successfully!');
    }
}
