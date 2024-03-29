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
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF;

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


    public function print($id)
    {
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

        $additionalFee = $book->car->accessories->map(function ($accessory) {
            return $accessory->fee;
        })->sum();
        $days = date_diff(
            date_create($book->end_date),
            date_create($book->start_date)
        )->format('%a') + 1;
        $carPrice = $additionalFee + $book->car->price_per_day;
        $total = $carPrice * $days;

        $pdf = app(PDF::class);
        $view = view('print.transaction', ['book' => $book, 'total' => $total, 'carPrice' => $carPrice]);
        $pdf->loadHTML($view->render());

        return $pdf->download('autorentzinvoice_' . $id . '_' . now() . '.pdf');
        // return View::make('print.transaction', compact('book', 'customer', 'car', 'accessInfo', 'driver', 'totalPrice'));
    }

    public function bookings()
    {
        $bookings = Booking::with([
            'customer',
            'car',
            'car.accessories',
            'car.modelo',
            'car.modelo.manufacturer',
            'car.modelo.type',
            'picklocation',
            'returnlocation',
            'driver'
        ])->get();

        return response()->json($bookings);
    }

    public function adminPendings() //admin access
    {
        $bookings = Booking::with([
            'customer',
            'car',
            'car.accessories',
            'car.modelo',
            'car.modelo.manufacturer',
            'car.modelo.type',
            'picklocation',
            'returnlocation',
            'driver'
        ])->where('status', 'pending')->get();

        return response()->json($bookings);
    }

    public function adminConfirms() //admin access
    {
        $bookings = Booking::with([
            'customer',
            'car',
            'car.accessories',
            'car.modelo',
            'car.modelo.manufacturer',
            'car.modelo.type',
            'picklocation',
            'returnlocation',
            'driver'
        ])->where('status', 'confirmed')->get();

        return response()->json($bookings);
    }

    public function adminCancelled() //admin access
    {
        $bookings = Booking::with([
            'customer',
            'car',
            'car.accessories',
            'car.modelo',
            'car.modelo.manufacturer',
            'car.modelo.type',
            'picklocation',
            'returnlocation',
            'driver'
        ])->where('status', 'cancelled')->get();

        return response()->json($bookings);
    }

    public function adminFinish() //admin access
    {
        $bookings = Booking::with([
            'customer',
            'car',
            'car.accessories',
            'car.modelo',
            'car.modelo.manufacturer',
            'car.modelo.type',
            'picklocation',
            'returnlocation',
            'driver'
        ])->where('status', 'finished')->get();

        return response()->json($bookings);
    }
}
