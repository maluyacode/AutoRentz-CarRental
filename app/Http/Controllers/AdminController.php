<?php

namespace App\Http\Controllers;

use App\AccessInformation;
use App\Charts\CarRentChart;
use App\Charts\CustomerRegisterChart;
use App\Charts\MonthlyIncomeChart;
use App\CustomerClass;
use App\Events\BookConfirmEvent;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\Bool_;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailToUser;
use App\TotalRentPrice;
use Barryvdh\Debugbar\Facades\Debugbar;

class AdminController extends Controller
{
    private $color;
    public function __construct()
    {
        return $this->color = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
        ];
    }

    public function chartsData()
    {
        $bookings = Booking::with(['car', 'car.accessories'])
            ->where('status', '=', 'finished')
            ->withTrashed()
            ->orderBy('created_at')
            ->get();

        foreach ($bookings as $booking) {
            $days = date_diff(
                date_create($booking->end_date),
                date_create($booking->start_date)
            )->format('%a') + 1;
            $accessoriesFee = $booking->car->accessories->map(function ($accessory) {
                return $accessory->fee;
            })->sum();

            $monthly[] = [
                "month" => date_create($booking->created_at)->format("F"),
                "rent" => ($booking->car->price_per_day + $accessoriesFee) * $days
            ];
        }

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[date('F', mktime(0, 0, 0, $i, 10))] = 0;
        }

        foreach ($monthly as $key => $income) {
            if (array_key_exists($income["month"], $monthlyIncome)) {
                $monthlyIncome[$income["month"]] += $income["rent"];
            }
        }

        $carData = DB::table('cars')
            ->leftJoin('bookings', function ($join) {
                $join->on('cars.id', '=', 'bookings.car_id');
                $join->where('bookings.status', '=', 'finished');
            })
            ->join('modelos', 'modelos.id', '=', 'cars.modelos_id')
            ->selectRaw('count(cars.id) as count, CONCAT(cars.platenumber, " ", modelos.name) as car_info')
            ->groupBy('cars.platenumber', 'modelos.name')
            ->orderBy('count', 'DESC')
            ->pluck('count', 'car_info')
            ->all();

        $customer = DB::table('customers')
            ->groupBy('month')
            ->select(
                DB::raw('count(monthname(created_at)) as count'),
                DB::raw('monthname(created_at) as month'),
            )
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            $registersPerMonth[date('F', mktime(0, 0, 0, $i, 10))] = 0;
        }

        foreach ($customer as $key => $customerJoin) {
            if (array_key_exists($customerJoin->month, $registersPerMonth)) {
                $registersPerMonth[$customerJoin->month] = $customerJoin->count;
            }
        }


        return response()->json([
            'monthlyIncome' => $monthlyIncome,
            'rentCountPerCar' => $carData,
            'registeredPerMonth' => $registersPerMonth
        ]);
    }

    public function AdminDashboard()
    {
        $allBooking = DB::table('bookings')
            ->select('bookings.*', 'cars.id as car_id', 'cars.price_per_day as price_per_day')
            ->join('cars', 'cars.id', 'bookings.car_id')
            ->where('status', 'finished')
            ->get();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $compute = new CustomerClass();
        $totalPrice = 0;
        foreach ($allBooking as $booking) {
            $totalPrice += $compute->computationDisplay($booking->start_date, $booking->end_date, $booking->price_per_day, $accessory, $booking->car_id);
        }
        $pendings = Booking::where('status', 'pending')->withTrashed()->get()->count();
        $confirmed = Booking::where('status', 'confirmed')->withTrashed()->get()->count();
        $finished = Booking::where('status', 'finished')->withTrashed()->get()->count();
        $cancelled = Booking::where('status', 'cancelled')->withTrashed()->get()->count();
        $cars = Car::all()->count();
        $users = User::all()->count();
        $drivers = Driver::whereNotIn('id', [1])->get()->count();
        $locations = Location::all()->count();


        // Monthly Income Chart
        $bookings = Booking::with(['car', 'car.accessories'])
            ->where('status', '=', 'finished')
            ->withTrashed()
            ->orderBy('created_at')
            ->get();

        foreach ($bookings as $booking) {
            $days = date_diff(
                date_create($booking->end_date),
                date_create($booking->start_date)
            )->format('%a') + 1;
            $accessoriesFee = $booking->car->accessories->map(function ($accessory) {
                return $accessory->fee;
            })->sum();

            $monthly[] = [
                "month" => date_create($booking->created_at)->format("F"),
                "rent" => ($booking->car->price_per_day + $accessoriesFee) * $days
            ];
        }

        for ($i = 1; $i <= 12; $i++) {
            $months[date('F', mktime(0, 0, 0, $i, 10))] = 0;
        }

        foreach ($monthly as $key => $income) {
            if (array_key_exists($income["month"], $months)) {
                $months[$income["month"]] += $income["rent"];
            }
        }

        $monthlyIncome = new MonthlyIncomeChart;
        $monthlyIncome->labels(array_keys($months));
        $monthlyIncome->dataset("Monthly Income 2023", 'line', array_values($months))->options([
            "backgroundColor" => $this->color,
            "borderColor" => 'rgba(0, 0, 0, 0.1)',
            "tension" => 0.2,
            "fill" => true,
            "minBarLength" => 2,
        ]);

        $monthlyIncome->displayLegend(false);
        $monthlyIncome->title("Monthly Income 2023", 20, '#666', true, "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif");


        // Car Rent Chart
        $carData = DB::table('cars')
            ->leftJoin('bookings', function ($join) {
                $join->on('cars.id', 'bookings.car_id');
                $join->where('bookings.status', '=', 'finished');
            })
            ->groupBy('platenumber')
            ->pluck(
                DB::raw('count(car_id)'),
                'platenumber'
            )->all();

        $carRentChart = new CarRentChart;
        $carRentChart->labels(array_keys($carData));
        $carRentChart->dataset(false, "bar", array_values($carData))->options([
            "backgroundColor" => $this->color,
            "borderColor" => $this->color,
            "minBarLength" => 2,
            "hoverBackgroundColor" => [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
            ],
        ]);
        $carRentChart->displayLegend(false);
        $carRentChart->title("Car Rents", 20, '#666', true, "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif");


        // Registered Customers Chart
        $customer = DB::table('customers')
            ->groupBy('month')
            ->select(
                DB::raw('count(monthname(created_at)) as count'),
                DB::raw('monthname(created_at) as month'),
            )
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            $months[date('F', mktime(0, 0, 0, $i, 10))] = 0;
        }

        foreach ($customer as $key => $customerJoin) {
            if (array_key_exists($customerJoin->month, $months)) {
                $months[$customerJoin->month] = $customerJoin->count;
            }
        }

        $customerRegister = new CustomerRegisterChart;
        $customerRegister->labels(array_keys($months));
        $customerRegister->dataset('Registered Customers 2023', "line", array_values($months))->options([
            "backgroundColor" => $this->color,
            "borderColor" => 'rgb(75, 192, 192)',
            "minBarLength" => 2,
            "fill" => false,
        ]);
        $customerRegister->displayLegend(false);
        $customerRegister->title("Registered Customers 2023", 20, '#666', true, "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif");


        return view(
            'admin.dashboard',
            compact(
                'totalPrice',
                'pendings',
                'confirmed',
                'finished',
                'cancelled',
                'cars',
                'drivers',
                'users',
                'locations',
                'carRentChart',
                'customerRegister',
                'monthlyIncome'
            )
        );
    }


    public function color()
    {
        return [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
        ];
    }

    public function confirmBooking(Request $request, $id)
    {
        if ($id == 0) {
            DB::beginTransaction();
            try {

                $booking = Booking::find($request->booking_id);
                $booking->status = 'confirmed';
                $booking->driver_id = $request->driver_id;

                $car = Car::find($booking->car_id);
                $car->car_status = 'taken';

                $driver = Driver::find($request->driver_id);
                $driver->driver_status = 'taken';
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                return back()->with('deleted', 'Error Occured!');
            }

            $booking->save();
            $driver->save();
            $car->save();

            DB::commit();
            $this->mail($booking->id);
            dd($booking->id);
            return back()->with('update', 'Book confirmed 🤑');
        } else {
            DB::beginTransaction();
            try {
                $booking = Booking::find($id);
                $booking->status = 'confirmed';

                $car = Car::find($booking->car_id);
                $car->car_status = 'taken';
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                return back()->with('deleted', 'Error Occured!');
            }
            $car->save();
            $booking->save();
            DB::commit();
            $this->mail($id);
            return back()->with('update', 'Book confirmed 🤑');
        }
    }

    public function mail($id)
    {
        try {
            $book = Booking::with([
                'customer',
                'customer.user',
                'car',
                'car.accessories',
                'car.modelo',
                'car.modelo.type',
                'car.modelo.manufacturer',
                'picklocation',
                'returnlocation',
                'driver',
            ])->find($id);
            BookConfirmEvent::dispatch($book);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('update', 'Book confirmed, without email due to connection problem');
        }
    }

    public function finishedBooking($id)
    {

        DB::beginTransaction();
        try {
            $booking = Booking::find($id);
            if ($booking->driver_id) {
                $driver = Driver::find($booking->driver_id);
                $car = Car::find($booking->car_id);

                $booking->status = 'finished';
                $driver->driver_status = 'available';
                $car->car_status = 'available';
                $booking->save();
                $driver->save();
                $car->save();
            } else {
                $booking->status = 'finished';
                $car = Car::find($booking->car_id);
                $booking->status = 'finished';
                $car->car_status = 'available';
                $booking->save();
                $car->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('deleted', 'Error Occured!');
        }
        DB::commit();
        return back()->with('update', 'Transaction finished! 🤑');
    }

    public function createBooking()
    {
        $allLocation = Location::all();
        $allcustomer = Customer::all();
        $accessInfo = new AccessInformation();
        $allCar = $accessInfo->carJoined()->get();
        // $allCar = DB::table('cars as ca')
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
        //     ->get();
        return View::make('admin.bookings.create', compact('allLocation', 'allcustomer', 'allCar'));
    }

    public function storeBooking(Request $request)
    {
        if ($request->typeget == "delivery") {
            $address = 'required';
            $pick = '';
            $return = '';
            $addressData = $request->address;
            $pickData = null;
            $returnData = null;
        } else {
            $address = '';
            $pick = 'required';
            $return = 'required';
            $addressData = null;
            $pickData = $request->pick_id;
            $returnData = $request->return_id;
        }
        Validator::make(
            $request->all(),
            [
                'customer_id' => 'required|numeric',
                'start_date' => 'required',
                'end_date' => 'required',
                'address' => "$address|min:10",
                'pick_id' => "$pick|numeric",
                'return_id' => "$return|numeric",
                'drivetype' => 'required|numeric',
                'car_id' => 'required|numeric',
                'typeget' => 'required'
            ],
            [
                'car_id.numeric' => 'The :attribute field is required',
                'customer_id.numeric' => 'The :attribute field is required',
                'pick_id.numeric' => 'The :attribute field is required',
                'return_id.numeric' => 'The :attribute field is required',
                'drivetype.numeric' => 'The :attribute field is required'
            ]
        )->validate();
        try {
            DB::beginTransaction();
            $booking = new Booking;
            $booking->start_date = $request->start_date;
            $booking->end_date = $request->end_date;
            $booking->pickup_location_id = $pickData;
            $booking->return_location_id = $returnData;
            $booking->address = $addressData;
            $booking->status = 'pending';
            $booking->customer_id = $request->customer_id;
            $booking->car_id = $request->car_id;
            if ($request->drivetype == 1) {
                $booking->driver_id = 1;
            } else {
                $booking->driver_id = null;
            }
            $booking->save();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
        DB::commit();
        return redirect()->route('bookings.index')->with('created', 'Booked car successfully');
    }

    public function editBooking($id)
    {
        $booking = Booking::find($id);
        $allLocation = Location::all();
        $allcustomer = Customer::all();
        $accessInfo = new AccessInformation();
        $allCar = $accessInfo->carJoined()->get();
        return view('admin.bookings.edit', compact('allLocation', 'allcustomer', 'allCar', 'booking'));
    }

    public function updateBooking(Request $request, $id)
    {
        if ($request->typeget == "delivery") {
            $address = 'required';
            $pick = '';
            $return = '';
            $addressData = $request->address;
            $pickData = null;
            $returnData = null;
        } else {
            $address = '';
            $pick = 'required';
            $return = 'required';
            $addressData = null;
            $pickData = $request->pick_id;
            $returnData = $request->return_id;
        }
        Validator::make(
            $request->all(),
            [
                'customer_id' => 'required|numeric',
                'start_date' => 'required',
                'end_date' => 'required',
                'address' => "$address|min:10",
                'pick_id' => "$pick|numeric",
                'return_id' => "$return|numeric",
                'drivetype' => 'required|numeric',
                'car_id' => 'required|numeric',
                'typeget' => 'required'
            ],
            [
                'car_id.numeric' => 'The :attribute field is required',
                'customer_id.numeric' => 'The :attribute field is required',
                'pick_id.numeric' => 'The :attribute field is required',
                'return_id.numeric' => 'The :attribute field is required',
                'drivetype.numeric' => 'The :attribute field is required'
            ]
        )->validate();
        try {
            DB::beginTransaction();
            $booking = Booking::find($id);
            $booking->start_date = $request->start_date;
            $booking->end_date = $request->end_date;
            $booking->pickup_location_id = $pickData;
            $booking->return_location_id = $returnData;
            $booking->address = $addressData;
            $booking->status = 'pending';
            $booking->customer_id = $request->customer_id;
            $booking->car_id = $request->car_id;
            if ($request->drivetype == 1) {
                $booking->driver_id = 1;
            } else {
                $booking->driver_id = null;
            }
            $booking->save();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
        DB::commit();
        return redirect()->route('bookings.index')->with('update', 'Updated successfully 🤑');
    }

    public function deleteBooking($id)
    {
        Booking::withTrashed()->find($id)->forceDelete();
        return back()->with('deleted', 'Deleted successfully 💔');
    }

    public function salesReport()
    {
        // $bookingData = '';
        $bookings = Booking::with([
            'car:id,modelos_id,platenumber,price_per_day', // selecting specific column
            'customer:id,name',
            'picklocation:id,street,baranggay,city',
            'returnlocation:id,street,baranggay,city',
            'driver:fname,lname',
            'car.accessories:id,fee',
            'car.modelo:id,manufacturer_id,type_id,name,year',
            'car.modelo.manufacturer:id,name',
            'car.modelo.type:id,name',
        ])
            ->where('status', 'finished')
            ->get();

        foreach ($bookings as $key => $booking) {

            $total_rent_price = new TotalRentPrice($booking);

            $bookData[$key] = [
                "id" => $booking->id,
                "start_date" => $booking->start_date,
                "end_date" => $booking->end_date,
                "mode_of_transac" => $booking->address ? "Deliver" : "Pickup",
                "locations" => $booking->address ? $booking->address : [
                    "pick" => $booking->picklocation->street . " " . $booking->picklocation->baranggay . " " . $booking->picklocation->city,
                    "return" => $booking->returnlocation->street . " " . $booking->returnlocation->baranggay . " " . $booking->returnlocation->city
                ],
                "customer" => $booking->customer->name,
                "car" => $booking->car->platenumber,
                "total" => number_format($total_rent_price->compute(), 2),

            ];
        }
        return response()->json(["booking" => $bookData, "status" => 200]);
    }

    public function reportSearch(Request $request)
    {
        $bookings = Booking::with([
            'car:id,modelos_id,platenumber,price_per_day', // selecting specific column
            'customer:id,name',
            'picklocation:id,street,baranggay,city',
            'returnlocation:id,street,baranggay,city',
            'driver:fname,lname',
            'car.accessories:id,fee',
            'car.modelo:id,manufacturer_id,type_id,name,year',
            'car.modelo.manufacturer:id,name',
            'car.modelo.type:id,name',
        ])
            ->whereBetween('start_date', [$request->start, $request->end])
            ->get();

        foreach ($bookings as $key => $booking) {

            $total_rent_price = new TotalRentPrice($booking);

            $bookData[$key] = [
                "id" => $booking->id,
                "start_date" => $booking->start_date,
                "end_date" => $booking->end_date,
                "mode_of_transac" => $booking->address ? "Deliver" : "Pickup",
                "locations" => $booking->address ? $booking->address : [
                    "pick" => $booking->picklocation->street . " " . $booking->picklocation->baranggay . " " . $booking->picklocation->city,
                    "return" => $booking->returnlocation->street . " " . $booking->returnlocation->baranggay . " " . $booking->returnlocation->city
                ],
                "customer" => $booking->customer->name,
                "car" => $booking->car->platenumber,
                "total" => number_format($total_rent_price->compute(), 2),

            ];
        }
        Debugbar::info($bookData);
        return response()->json(["booking" => $bookData, "status" => 200]);
    }
}
