<?php

namespace App\Http\Controllers;

use App\AccessInformation;
use App\Events\BookConfirmEvent;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\View;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\TotalRentPrice;
use Barryvdh\Debugbar\Facades\Debugbar;
use DateTime;

class AdminController extends Controller
{
    public function chartsData()
    {
        // charts whole year income
        $bookings = Booking::with(['car', 'car.accessories'])
            ->where('status', '=', 'finished')
            ->withTrashed()
            ->orderBy('end_date')
            ->get();

        $currentYear = date('Y');
        $startDate = new DateTime("$currentYear-01-01");
        $endDate = new DateTime("$currentYear-12-31");

        $dates = array();
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $dates[$currentDate->format('Y-m-d')] = 0;
            $currentDate->modify('+1 day');
        }

        foreach ($bookings as $booking) {
            $days = date_diff(
                date_create($booking->end_date),
                date_create($booking->start_date)
            )->format('%a') + 1;
            $accessoriesFee = $booking->car->accessories->map(function ($accessory) {
                return $accessory->fee;
            })->sum();

            $income = [
                "month" => date_create($booking->created_at)->format("Y-m-d"),
                "rent" => ($booking->car->price_per_day + $accessoriesFee) * $days
            ];

            if (array_key_exists($income["month"], $dates)) {
                $dates[$income["month"]] += $income["rent"];
            }
        }
        // charts whole year income

        $carData = Car::with(['bookings', 'accessories', 'modelo', 'modelo.type', 'modelo.manufacturer'])->get();

        $registered = Customer::with(['bookings', 'bookings.car'])->get();


        return response()->json([
            'monthlyIncome' => $dates,
            'rentCountPerCar' => $carData,
            'registered' => $registered
        ]);
    }

    public function AdminDashboard()
    {

        $bookings = Booking::with(['car', 'car.accessories'])->get();

        $totalIncome = $bookings->map(function ($booking) {

            $days = date_diff(
                date_create($booking->end_date),
                date_create($booking->start_date)
            )->format('%a') + 1;

            $accessoriesFee = $booking->car->accessories->map(function ($accessory) {
                return $accessory->fee;
            })->sum();

            return ($booking->car->price_per_day + $accessoriesFee) * $days;
        })->sum();

        $pendings = Booking::where('status', 'pending')->withTrashed()->get()->count();
        $confirmed = Booking::where('status', 'confirmed')->withTrashed()->get()->count();
        $finished = Booking::where('status', 'finished')->withTrashed()->get()->count();
        $cancelled = Booking::where('status', 'cancelled')->withTrashed()->get()->count();
        $cars = Car::all()->count();
        $users = User::all()->count();
        $drivers = Driver::whereNotIn('id', [1])->get()->count();
        $locations = Location::all()->count();

        return view(
            'admin.dashboard',
            compact(
                'totalIncome',
                'pendings',
                'confirmed',
                'finished',
                'cancelled',
                'cars',
                'drivers',
                'users',
                'locations',
            )
        );
    }

    public function confirmBooking(Request $request, $id)
    {
        Debugbar::info($request, $id);
        if (is_numeric($id)) {
            DB::beginTransaction();
            try {

                $booking = Booking::find($request->booking_id);
                $booking->status = 'confirmed';
                $booking->driver_id = $id;

                $car = Car::find($booking->car_id);
                $car->car_status = 'taken';

                $driver = Driver::find($id);
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
            // dd($booking->id);
            // return back()->with('update', 'Book confirmed 🤑');
            return response()->json($booking);
        } else {
            DB::beginTransaction();
            try {
                $booking = Booking::find($request->booking_id);
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
            $this->mail($booking->id);
            // return back()->with('update', 'Book confirmed 🤑');
            return response()->json($booking);
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
                'car.transmission',
                'car.fuel',
                'picklocation',
                'returnlocation',
                'driver',
            ])->find($id);
            BookConfirmEvent::dispatch($book);
        } catch (\Exception $e) {
            Debugbar::info($e);
            return back()->with('update', 'Book confirmed, without email due to connection problem');
        }
    }

    public function cancellBooking($id)
    {
        $booking = Booking::find($id);
        // dd($booking);
        if ($booking) {
            $booking->status = "cancelled";
            $booking->save();
            // return back()->with("deleted", "Book cancelled! 👉🥺👈");
            return response()->json($booking);
        }
        return "Error";
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
        // return back()->with('update', 'Transaction finished! 🤑');
        return response()->json($booking);
    }

    public function createBooking()
    {
        $allLocation = Location::all();
        $allcustomer = Customer::all();
        $accessInfo = new AccessInformation();
        $allCar = $accessInfo->carJoined()->get();

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
        // return back()->with('deleted', 'Deleted successfully 💔');
        return response()->json([]);
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
        ])->whereBetween('start_date', [$request->start, $request->end])->get();

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
