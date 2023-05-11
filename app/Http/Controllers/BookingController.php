<?php

namespace App\Http\Controllers;

use App\AccessInformation;
use App\CustomerClass;
use App\Models\Booking;
use App\Models\Location;
use App\DataTables\BookingDataTable;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\DataTables\PendingDataTable;
use App\DataTables\ConfirmDataTable;
use App\DataTables\FinishDataTable;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function pendings() // user/customer access
    {
        $pendings = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->where('us.id', Auth::user()->id)
            ->where('bo.status', 'pending')
            ->orderBy('bo.id', 'DESC')
            ->select('bo.*', 'ca.price_per_day as price_per_day')
            ->get();
        $accessInfo = new AccessInformation();
        $customerClass = new CustomerClass();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        return View::make("user.bookings.pendings", compact('pendings', 'accessInfo', 'customerClass', 'accessory'));
    }

    public function confirmed()
    {
        $confirms = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->where('us.id', Auth::user()->id)
            ->where('bo.status', 'confirmed')
            ->orderBy('bo.id', 'DESC')
            ->select('bo.*', 'ca.price_per_day as price_per_day')
            ->get();
        $accessInfo = new AccessInformation();
        $customerClass = new CustomerClass();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        return View::make("user.bookings.confirmed", compact('confirms', 'accessInfo', 'customerClass', 'accessory'));
    }

    public function editbooking($id) // user/customer access
    {
        $editBook = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->where('us.id', Auth::user()->id)
            ->where('bo.status', 'pending')
            ->where('bo.id', $id)
            ->select('bo.*', 'ca.price_per_day as price_per_day')
            ->first();
        // dd($editBook);
        $accessInfo = new AccessInformation();
        $customerClass = new CustomerClass();
        $pickLocations = Location::whereNotIn('id', [$editBook->pickup_location_id])->get();
        $returnLocations = Location::whereNotIn('id', [$editBook->return_location_id])->get();
        $allLocation = Location::all();
        // dd($returnLocations);
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();

        return View::make('user.bookings.edit', compact('editBook', 'accessInfo', 'customerClass', 'accessory', 'returnLocations', 'pickLocations', 'allLocation'));
    }

    public function savechanges(Request $request, $id) // user/customer access
    {
        if ($request->typeget == "delivery") {
            $rules = [
                'address' => 'required|min:10',
            ];
            $messages = [
                'address.required' => 'Please indicate if you choose delivery',
                'address.min' => 'Please be specific to your address'
            ];
            Validator::make($request->all(), $rules, $messages)->validate();
        }
        if ($request) {
            DB::beginTransaction();
            try {

                $booking = Booking::find($id);
                $booking->start_date = $request->start_date;
                $booking->end_date = $request->end_date;

                if ($request->typeget == "pickup") {
                    $booking->pickup_location_id = $request->pick_id;
                    $booking->return_location_id = $request->return_id;
                    $booking->address = null;
                } else {
                    $booking->pickup_location_id = null;
                    $booking->return_location_id = null;
                    $booking->address = $request->address;
                }

                if ($request->drivetype == 1) {
                    $booking->driver_id = 1;
                } else {
                    $booking->driver_id = null;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return back() - with("error", "Sorry for the inconvenience there is an error occur in the system, please try again later.");
            }
            DB::commit();
            $booking->save();
            return back()->with("update", "Updated Successfully!");
        }
    }

    public function cancel($id)
    { // user/customer access
        $booking = Booking::find($id);
        // dd($booking);
        if ($booking) {
            $booking->status = "cancelled";
            $booking->save();
            return back()->with("deleted", "Book cancelled! 👉🥺👈");
        }
        return "Error";
    }

    public function displaycancelled() // user/customer access
    { // user/customer access
        $cancelled = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->where('us.id', Auth::user()->id)
            ->where('bo.status', 'cancelled')
            ->orderBy('bo.id', 'DESC')
            ->whereNull('deleted_at')
            ->select('bo.*', 'ca.price_per_day as price_per_day')
            ->get();
        $accessInfo = new AccessInformation();
        $customerClass = new CustomerClass();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        return View::make("user.bookings.cancelled", compact('cancelled', 'accessInfo', 'customerClass', 'accessory'));
    }
    public function finished() // user/customer access
    { // user/customer access
        $finished = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->where('us.id', Auth::user()->id)
            ->where('bo.status', 'finished')
            ->orderBy('bo.id', 'DESC')
            ->whereNull('deleted_at')
            ->select('bo.*', 'ca.price_per_day as price_per_day')
            ->get();
        $accessInfo = new AccessInformation();
        $customerClass = new CustomerClass();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        return View::make("user.bookings.finished", compact('finished', 'accessInfo', 'customerClass', 'accessory'));
    }


    public function removecancelled($id) //user/customer access
    { // user/customer access
        $book = Booking::where('id', $id)->delete();
        return back()->with("deleted", "Deleted Successfuly!");
    }

    public function index(BookingDataTable $dataTable) //admin access
    { // admin access
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
                    WHEN bo.driver_id = 1 THEN 'with driver'
                    WHEN bo.driver_id IS NULL THEN 'self drive'
                    END as drivetype")
            )
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->leftJoin('locations as lo1', 'lo1.id', 'bo.pickup_location_id')
            ->leftJoin('locations as lo2', 'lo2.id', 'bo.return_location_id')
            ->get();
        // dd($bookings);
        $header = "Bookings";
        $drivers = Driver::all()->skip(1);
        return $dataTable->render('admin.bookings.index', compact('drivers', 'header'));
    }

    public function adminPendings(PendingDataTable $dataTable) //admin access
    {
        $drivers = Driver::all()->skip(1);
        $header = "Pendings";
        return $dataTable->render('admin.bookings.index', compact('drivers', 'header'));
    }
    public function adminConfirms(ConfirmDataTable $dataTable) //admin access
    {
        $drivers = Driver::all()->skip(1);
        $header = "Confirmed";
        return $dataTable->render('admin.bookings.index', compact('drivers', 'header'));
    }
    public function adminFinish(FinishDataTable $dataTable) //admin access
    {
        $drivers = Driver::all()->skip(1);
        $header = "Finished";
        return $dataTable->render('admin.bookings.index', compact('drivers', 'header'));
    }

    public function print($id)
    {
        $book = Booking::find($id);
        $customer = Customer::find($book->customer_id);
        $accessInfo = new AccessInformation();
        if ($book->driver_id) {
            $driver = Driver::find($book->driver_id);
        } else {
            $driver = null;
        }

        $car = DB::table('cars as ca')
            ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
            ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
            ->select(
                'ca.*',
                'fu.name as fuelname',
                'ta.name as transname',
                'mo.name as modelname',
                'mo.year as modelyear',
                'ty.name as typename',
                'ma.name as manufacturername',
                'fu.id as fuelID',
                'ta.id as transID',
                'mo.id as modelID',
                'ty.id as typeID',
                'ma.id as manuID'
            )->where('ca.id', $book->car_id)
            ->first();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $customerClass = new CustomerClass();
        $totalPrice = $customerClass->computationDisplay($book->start_date, $book->end_date, $car->price_per_day, $accessory, $car->id);

        $data = [
            'book' => $book,
            'customer' => $customer,
            'car' => $car,
            'accessInfo' => $accessInfo,
            'driver' => $driver,
            'totalPrice' => $totalPrice
        ];
        $date = $accessInfo->formatDate(now());
        $pdf = Pdf::loadView('print.transaction', $data);
        return $pdf->download('autorentzinvoice_'.$id.'_'.$date.'.pdf');
        // return View::make('print.transaction', compact('book', 'customer', 'car', 'accessInfo', 'driver', 'totalPrice'));
    }
}
