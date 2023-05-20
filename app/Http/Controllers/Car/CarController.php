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

use App\Models\Car;
use App\Models\Customer;
use App\Models\Fuel;
use App\Models\Transmission;
use GuzzleHttp\Promise\Create;
use Faker\Factory as Faker;

class CarController extends Controller
{

    public function index(CarDataTable $dataTable)
    {
        return $dataTable->render('car.index');
    }
    public function create()
    {
        $models = DB::table('modelos as mo')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
            ->select(
                'mo.*',
                'ty.name as typename',
                'ma.name as manufacturername'
            )->get();
        $fuels = Fuel::all();
        $transmissions = Transmission::all();
        $accessories = Accessorie::all();
        return View::make('car.create', compact('models', 'fuels', 'transmissions', 'accessories'));
    }
    public function store(Request $request)
    {

        Validator::make(
            $request->all(),
            [
                'platenumber' => 'required|min:6',
                'price_per_day' => 'required|min:2|numeric',
                'seats' => 'required|min:1|numeric',
                'description' => 'required|min:10|max:600',
                'image_path.*' => 'mimes:jpeg,png,jpg',
                'image_path' => 'required',
                'cost_price' => 'required|min:2|numeric',
                'model_id' => 'required',
                'transmission_id' => 'required',
                'fuel_id' => 'required',
                'accessories_id.*' => '',
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
        }
        Car::create([
            "platenumber" => $request->platenumber,
            "price_per_day" => $request->price_per_day,
            "seats" => $request->seats,
            "description" => $request->description,
            "image_path" => $image_path,
            "cost_price" => $request->cost_price,
            "modelos_id" => $request->model_id,
            "transmission_id" => $request->transmission_id,
            "fuel_id" => $request->fuel_id,
        ]);
        $newcar = Car::max('id');
        foreach ($request->accessories_id as $key => $data) {
            DB::table('accessorie_car')
                ->insert([
                    "car_id" => $newcar,
                    "accessorie_id" => $data
                ]);
        }
        return redirect()->route('car.index')->with('created', 'New car added successfully!');
    }

    public function edit($id)
    {
        $accessInfo = new AccessInformation();
        $car = $accessInfo->carJoined()
            ->where('ca.id', $id)->first();
        // $car = DB::table('cars as ca')
        //     ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
        //     ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
        //     ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
        //     ->join('types as ty', 'ty.id', 'mo.type_id')
        //     ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
        //     ->select(
        //         'ca.*',
        //         'fu.name as fuelname',
        //         'ta.name as transmissionname',
        //         'mo.name as modelname',
        //         'mo.year as modelyear',
        //         'ty.name as typename',
        //         'ma.name as manufacturername',
        //         'fu.id as fuelID',
        //         'ta.id as transID',
        //         'mo.id as modelID',
        //         'ty.id as typeID',
        //         'ma.id as manuID'
        //     )
        //     ->where('ca.id', $id)->first();
        $models = DB::table('modelos as mo')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
            ->select(
                'mo.*',
                'ty.name as typename',
                'ma.name as manufacturername'
            )->whereNotIn('mo.id', [$car->modelID])->get();
        $fuels = Fuel::whereNotIn('id', [$car->fuel_id])->get();
        $transmissions = Transmission::whereNotIn('id', [$car->transmission_id])->get();
        $carAccessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac.id', 'ac_ca.accessorie_id')
            ->where('ca.id', [$id])
            ->pluck('ac.name as name', 'ac.id as id')
            ->toArray();
        $accessories = Accessorie::all();
        return View::make('car.edit', compact('car', 'models', 'fuels', 'transmissions', 'carAccessory', 'accessories'));
    }

    public function update(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            [
                'platenumber' => 'required|min:6',
                'price_per_day' => 'required|min:2|numeric',
                'seats' => 'required|min:1|numeric',
                'description' => 'required|min:10|max:600',
                'image_path.*' => 'mimes:jpeg,png,jpg',
                'cost_price' => 'required|min:2|numeric',
            ],
            [
                'image_path.*.mimes' => 'The image must be a file of type: jpeg, png, jpg.'
            ]
        )->validate();

        if ($request->file()) {
            foreach ($request->image_path as $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                $path = Storage::putFileAs('public/images', $images, $fileName);
                $filenames[] = $fileName;
            }
            $image_path = implode("=", $filenames);
            Car::whereId($id)->update([
                "image_path" => $image_path,
            ]);
        }
        Car::whereId($id)->update([
            "platenumber" => $request->platenumber,
            "price_per_day" => $request->price_per_day,
            "seats" => $request->seats,
            "description" => $request->description,
            "cost_price" => $request->cost_price,
            "modelos_id" => $request->model_id,
            "transmission_id" => $request->transmission_id,
            "fuel_id" => $request->fuel_id,
        ]);
        if ($request->accessories_id) {
            DB::table('accessorie_car')->where('car_id', $id)->delete();
            foreach ($request->accessories_id as $key => $data) {
                DB::table('accessorie_car')
                    ->insert([
                        "car_id" => $id,
                        "accessorie_id" => $data
                    ]);
            }
        } else {
            DB::table('accessorie_car')->where('car_id', $id)->delete();
        }
        return redirect()->route('car.index')->with('update', 'Updated successfully!');
    }

    public function destroy($id)
    {
        Car::destroy($id);
        return redirect()->route('car.index')->with('deleted', 'Deleted successfully!');
    }

    public function cardetails($id)
    {
        $accessInfo = new AccessInformation();
        $car = $accessInfo->carJoined()
            ->where('ca.id', $id)
            ->first();
        // $car = DB::table('cars as ca')
        //     ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
        //     ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
        //     ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
        //     ->join('types as ty', 'ty.id', 'mo.type_id')
        //     ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
        //     ->select(
        //         'ca.*',
        //         'fu.name as fuelname',
        //         'ta.name as transmissionname',
        //         'mo.name as modelname',
        //         'mo.year as modelyear',
        //         'ty.name as typename',
        //         'ma.name as manufacturername',
        //         'fu.id as fuelID',
        //         'ta.id as transID',
        //         'mo.id as modelID',
        //         'ty.id as typeID',
        //         'ma.id as manuID'
        //     )
        //     ->where('ca.id', $id)
        //     ->first();
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
        // $cars = $accessInfo->carJoined()->get();
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
                // DB::raw('(SELECT SUM(fee) FROM accessories
                // INNER JOIN accessorie_car ON accessories.id = accessorie_car.accessorie_id
                // INNER JOIN cars ON cars.id = accessorie_car.car_id ) as accessoryFee')
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
        // $cars = DB::table('cars as ca')
        //     ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
        //     ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
        //     ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
        //     ->join('types as ty', 'ty.id', 'mo.type_id')
        //     ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
        //     ->select(
        //         'ca.*',
        //         'fu.name as fuelname',
        //         'ta.name as transmissionname',
        //         'mo.name as modelname',
        //         'mo.year as modelyear',
        //         'ty.name as typename',
        //         'ma.name as manufacturername',
        //         'fu.id as fuelID',
        //         'ta.id as transID',
        //         'mo.id as modelID',
        //         'ty.id as typeID',
        //         'ma.id as manuID'
        //     )
        //     ->where('mo.name', 'LIKE', "%$search%")
        //     ->orWhere('ma.name', 'LIKE', "%$search%")
        //     ->orWhere('ty.name', 'LIKE', "%$search%")
        //     ->orWhere('ta.name', 'LIKE', "%$search%")
        //     ->orWhere('ca.seats', 'LIKE', "%$search%")
        //     ->get();
        $data = json_encode($cars);
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $customerClass = new CustomerClass();
        return View::make('fleet.car-listing', compact('cars', 'search', 'accessory', 'customerClass', 'data'));
    }
}
