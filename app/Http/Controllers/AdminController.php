<?php

namespace App\Http\Controllers;

use App\AccessInformation;
use App\CustomerClass;
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

class AdminController extends Controller
{
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

        return view('admin.dashboard', compact('totalPrice', 'pendings', 'confirmed', 'finished', 'cancelled', 'cars', 'drivers', 'users', 'locations'));
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
                return back()->with('deleted', 'Error Occured!');
            }
            $booking->save();
            $driver->save();
            $car->save();
            DB::commit();
            $this->mail($request->booking_id);
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
            $booking = Booking::find($id);
            $customerTable = Customer::find($booking->customer_id);
            $mail = new MailToUser($id);
            $mailmessage = $mail->build($booking->id);
            $mailmessage->from('autorentz24@gmail.com', 'AutoRentz');
            Mail::to($customerTable->email)->send($mailmessage);
        } catch (\Exception $e) {
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

    public function report(Request $request)
    {
        if (($request->start_date && $request->end_date)) {
            $bookings = DB::table('users as us')
                ->select(
                    'bo.*',
                    'mo.name as modelName',
                    'ca.price_per_day as price_per_day',
                    'mo.year',
                    'cu.name as customer_name',
                    'cu.email as customer_email',
                    'cu.phone as customer_phone',
                    'ca.platenumber',
                    DB::raw('CONCAT(lo1.street, ", ", lo1.baranggay, ", ", lo1.city) as pickuplocation'),
                    DB::raw('CONCAT(lo2.street, ", ", lo2.baranggay, ", ", lo2.city) as returnlocation'),
                )
                ->join('customers as cu', 'us.id', 'cu.user_id')
                ->join('bookings as bo', 'cu.id', 'bo.customer_id')
                ->join('cars as ca', 'ca.id', 'bo.car_id')
                ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
                ->leftJoin('locations as lo1', 'lo1.id', 'bo.pickup_location_id')
                ->leftJoin('locations as lo2', 'lo2.id', 'bo.return_location_id')
                ->whereBetween('bo.created_at', [$request->start_date, $request->end_date])
                ->where('bo.status', 'finished')
                ->get();
        } else {
            $bookings = $this->refresh();
        }
        $info = new AccessInformation();
        $compute = new CustomerClass();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $totalPrice = 0;
        foreach ($bookings as $booking) {
            $totalPrice += $compute->computationDisplay($booking->start_date, $booking->end_date, $booking->price_per_day, $accessory, $booking->car_id);
        }
        return View::make('admin.bookings.report', compact('bookings', 'info', 'compute', 'accessory', 'totalPrice'));
    }

    public function refresh()
    {
        $bookings = DB::table('users as us')
            ->select(
                'bo.*',
                'mo.name as modelName',
                'ca.price_per_day as price_per_day',
                'mo.year',
                'cu.name as customer_name',
                'cu.email as customer_email',
                'cu.phone as customer_phone',
                'ca.platenumber',
                DB::raw('CONCAT(lo1.street, ", ", lo1.baranggay, ", ", lo1.city) as pickuplocation'),
                DB::raw('CONCAT(lo2.street, ", ", lo2.baranggay, ", ", lo2.city) as returnlocation'),
                DB::raw('(DATEDIFF(bo.end_date, bo.start_date)) + 1 as days'),
                DB::raw("CASE
                    WHEN bo.driver_id IS NOT NULL THEN 'with driver'
                    WHEN bo.driver_id IS NULL THEN 'self drive'
                    END as drivetype")
            )
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->leftJoin('locations as lo1', 'lo1.id', 'bo.pickup_location_id')
            ->leftJoin('locations as lo2', 'lo2.id', 'bo.return_location_id')
            ->orWhere('bo.status', 'finished')
            ->get();
        return $bookings;
    }
}
