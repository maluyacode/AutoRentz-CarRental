<?php

namespace App\Http\Controllers\Car;

use App\AccessInformation;
use App\CustomerClass;
use App\DataTables\CarDataTable;
use App\Http\Controllers\Controller;
use App\Models\Accessorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Fuel;
use App\Models\Modelo;
use App\Models\Transmission;
use Barryvdh\Debugbar\Facades\Debugbar;
use GuzzleHttp\Promise\Create;
use Faker\Factory as Faker;
use App\Imports\CarImport;
use Maatwebsite\Excel\Facades\Excel;

class CarController extends Controller
{

    public function index()
    {
        $cars = Car::with([
            'accessories' => function ($query) {
                return $query->select('id', 'name', 'fee');
            },
            'modelo' => function ($query) {
                return $query->select('id', 'type_id', 'manufacturer_id',  'name', 'year');
            },
            'modelo.manufacturer' => function ($query) {
                return $query->select('id', 'name');
            },
            'modelo.type' => function ($query) {
                return $query->select('id', 'name');
            },
            'media'
        ])->get();

        return response()->json($cars, 200, [], 0);
    }

    public function create()
    {
        $models = Modelo::with('manufacturer', 'type')->get();
        $fuels = Fuel::all();
        $transmissions = Transmission::all();
        $accessories = Accessorie::all();
        return response()->json(["models" => $models, "fuels" => $fuels, "transmissions" => $transmissions, "accessories" => $accessories]);
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path("cars/images");
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
        // Validator::make(
        //     $request->all(),
        //     [
        //         'platenumber' => 'required|min:6',
        //         'price_per_day' => 'required|min:2|numeric',
        //         'seats' => 'required|min:1|numeric',
        //         'description' => 'required|min:10|max:600',
        //         'image_path.*' => 'mimes:jpeg,png,jpg',
        //         'image_path' => 'required',
        //         'cost_price' => 'required|min:2|numeric',
        //         'model_id' => 'required',
        //         'transmission_id' => 'required',
        //         'fuel_id' => 'required',
        //         'accessories_id.*' => '',
        //     ],
        //     [
        //         'image_path.*.mimes' => 'The image(s) must be a file of type: jpeg, png, jpg.',
        //     ]
        // )->validate();
        $newcar = Car::create([
            "platenumber" => $request->platenumber,
            "price_per_day" => $request->price_per_day,
            "seats" => $request->seats,
            "description" => $request->description,
            "image_path" => 'Switched to media',
            "cost_price" => $request->cost_price,
            "modelos_id" => $request->model_id,
            "transmission_id" => $request->transmission_id,
            "fuel_id" => $request->fuel_id,
        ]);

        $newcar->accessories()->attach(array_values($request->accessories_id));

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $newcar->addMedia(storage_path("cars/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $car =  Car::with([
            'accessories' => function ($query) {
                return $query->select('id', 'name', 'fee');
            },
            'modelo' => function ($query) {
                return $query->select('id', 'type_id', 'manufacturer_id',  'name', 'year');
            },
            'modelo.manufacturer' => function ($query) {
                return $query->select('id', 'name');
            },
            'modelo.type' => function ($query) {
                return $query->select('id', 'name');
            },
            'media'
        ])->where('id', $newcar->id)->first();

        return response()->json($car);
    }
    public function show($id)
    {
        return Car::find($id);
    }
    public function edit($id)
    {
        $car = Car::with([
            'accessories' => function ($query) {
                return $query->select('id', 'name', 'fee');
            },
            'fuel' => function ($query) {
                return $query->select('id', 'name');
            },
            'transmission' => function ($query) {
                return $query->select('id', 'name');
            },
            'modelo' => function ($query) {
                return $query->select('id', 'type_id', 'manufacturer_id',  'name', 'year');
            },
            'modelo.manufacturer' => function ($query) {
                return $query->select('id', 'name');
            },
            'modelo.type' => function ($query) {
                return $query->select('id', 'name');
            },
            'media'
        ])->find($id);

        $accessories_id = $car->accessories->pluck('id');
        $accessories = Accessorie::whereNotIn('id', $accessories_id)->get();
        $models = Modelo::whereNotIn('id', [$car->modelos_id])->get();
        $fuels = Fuel::whereNotIn('id', [$car->fuel_id])->get();
        $transmissions = Transmission::whereNotIn('id', [$car->transmission_id])->get();

        return response()->json([
            "car" => $car,
            "models" => $models,
            "fuels" => $fuels,
            "transmissions" => $transmissions,
            "accessories" => $accessories
        ]);
    }

    public function viewMedia($id)
    {
        $car = Car::find($id);
        $car->getMedia('images');
        return response()->json($car);
    }

    public function deleteMedia(Request $request, $id)
    {
        $modelID = $request->id;
        DB::table('media')->where('id', $id)->delete();

        $model = Car::find($modelID);
        $model->getMedia('images');

        return response()->json($model);
    }

    public function update(Request $request, $id)
    {
        // Validator::make(
        //     $request->all(),
        //     [
        //         'platenumber' => 'required|min:6',
        //         'price_per_day' => 'required|min:2|numeric',
        //         'seats' => 'required|min:1|numeric',
        //         'description' => 'required|min:10|max:600',
        //         'image_path.*' => 'mimes:jpeg,png,jpg',
        //         'cost_price' => 'required|min:2|numeric',
        //     ],
        //     [
        //         'image_path.*.mimes' => 'The image must be a file of type: jpeg, png, jpg.'
        //     ]
        // )->validate();

        // if ($request->file()) {
        //     foreach ($request->image_path as $images) {
        //         $fileName = time() . '_' . $images->getClientOriginalName();
        //         $path = Storage::putFileAs('public/images', $images, $fileName);
        //         $filenames[] = $fileName;
        //     }
        //     $image_path = implode("=", $filenames);
        //     Car::whereId($id)->update([
        //         "image_path" => $image_path,
        //     ]);
        // }
        // Debugbar::info($request);

        $updatedCar = Car::find($id)->update([
            "platenumber" => $request->platenumber,
            "price_per_day" => $request->price_per_day,
            "seats" => $request->seats,
            "description" => $request->description,
            "cost_price" => $request->cost_price,
            "modelos_id" => $request->model_id,
            "transmission_id" => $request->transmission_id,
            "fuel_id" => $request->fuel_id,
        ]);

        $car = Car::with(['accessories'])->find($id);
        $prevAcessories = $car->accessories->map(function ($accessory) {
            return $accessory->id;
        });

        $car->accessories()->detach($prevAcessories);
        $car->accessories()->attach(array_values($request->accessories_id));

        if ($request->document !== null) {
            foreach ($request->input("document", []) as $file) {
                $car->addMedia(storage_path("cars/images/" . $file))->toMediaCollection("images");
                // unlink(storage_path("drivers/images/" . $file));
            }
        }

        $car =  Car::with([
            'accessories' => function ($query) {
                return $query->select('id', 'name', 'fee');
            },
            'modelo' => function ($query) {
                return $query->select('id', 'type_id', 'manufacturer_id',  'name', 'year');
            },
            'modelo.manufacturer' => function ($query) {
                return $query->select('id', 'name');
            },
            'modelo.type' => function ($query) {
                return $query->select('id', 'name');
            },
            'media'
        ])->where('id', $id)->first();

        // if ($request->accessories_id) {
        //     DB::table('accessorie_car')->where('car_id', $id)->delete();
        //     foreach ($request->accessories_id as $key => $data) {
        //         DB::table('accessorie_car')
        //             ->insert([
        //                 "car_id" => $id,
        //                 "accessorie_id" => $data
        //             ]);
        //     }
        // } else {
        //     DB::table('accessorie_car')->where('car_id', $id)->delete();
        // }
        return response()->json($car);
    }

    public function destroy($id)
    {
        Car::destroy($id);
        DB::table('media')->where('model_id', $id)->where('model_type', 'App\Models\Car')->delete();
        return response()->json([]);
    }

    public function cardetails($id)
    {
        $accessInfo = new AccessInformation();
        $car = $accessInfo->carJoined()
            ->where('ca.id', $id)
            ->first();
        $customerClass = new CustomerClass();
        $accessory = Car::find($id)->accessories()->get();
        $accessoryfee = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        return View::make('fleet.car-details', compact('car', 'accessory', 'customerClass', 'accessoryfee'));
    }

    public function carlists()
    {
        $accessInfo = new AccessInformation();
        $cars = DB::table('cars as ca')
            ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
            ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
            ->select(
                'ca.*',
                'fu.name as fuelname',
                'ta.name as transmissionname',
                'mo.name as modelname',
                'mo.year as modelyear',
                'ty.name as typename',
                'ma.name as manufacturername',
                'fu.id as fuelID',
                'ta.id as transID',
                'mo.id as modelID',
                'ty.id as typeID',
                'ma.id as manuID',
            )
            ->get();
        $data = json_encode($cars);
        $accessory = DB::table('cars as ca')
            ->select('ac.fee', 'ca.id')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $accessories = json_encode($accessory);
        $customerClass = new CustomerClass();
        return View::make('fleet.car-listing', compact('cars', 'accessory', 'customerClass', 'data', 'accessories'));
    }

    public function carsearch(Request $request)
    {
        $search = $request->input('search');
        $accessInfo = new AccessInformation();
        $cars = $accessInfo->carJoined()
            ->where('mo.name', 'LIKE', "%$search%")
            ->orWhere('ma.name', 'LIKE', "%$search%")
            ->orWhere('ty.name', 'LIKE', "%$search%")
            ->orWhere('ta.name', 'LIKE', "%$search%")
            ->orWhere('ca.seats', 'LIKE', "%$search%")
            ->get();

        $data = json_encode($cars);
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $customerClass = new CustomerClass();
        return View::make('fleet.car-listing', compact('cars', 'search', 'accessory', 'customerClass', 'data'));
    }

    public function import(Request $request)
    {
        Excel::import(new CarImport(), $request->excel);
        return response()->json([]);
    }
}
