<?php

namespace App;

use Faker\Guesser\Name;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Customer;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class CustomerClass
{
    /**
     * Class constructor.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function insert()
    {
        Customer::create([
            "user_id" => Auth::user()->id,
            "name" => Auth::user()->name,
            "email" => Auth::user()->email,
        ]);
    }

    public function addToUserGarage($garage, $id, $customer_id)
    {
        // check if garage session is not null.
        if ($garage) {
            // loop all the car in the garage.
            foreach ($garage as $key => $cars) {
                // check if the car is already exist in garage
                if ($key == $id) {
                    return "already";
                }
            }
        }

        // check if the user/customer has garage created before.
        if ((!$garage) && (!Session::has('garage' . Auth::user()->id))) {
            // sets the default value to store in session garage
            $createUserGarage[$id] = [
                'customer_id' => $customer_id, 'car_id' => $id, 'start_date' => null, 'end_date' => null,
                'pick_id' => null, 'return_id' => null, 'address' => null, 'driver_id' => 0, 'status' => 'pending'
            ];
            // store the the array contains the details to be reserve in session garage.
            Session::put('garage' . Auth::user()->id, $createUserGarage);
            Session::save();
            $usersgarage = Session::get('garage' . Auth::user()->id);
            return "created"; // if the user/customer is first time adding car on his/her garage.
        } else {
            // sets the default value to store in session garage
            $createUserGarage = [
                'customer_id' => $customer_id, 'car_id' => $id, 'start_date' => null, 'end_date' => null,
                'pick_id' => null, 'return_id' => null, 'address' => null, 'driver_id' => null, 'status' => null
            ];
            // store the the array contains the details to be reserve in session garage.
            $usersgarage = Session::get('garage' . Auth::user()->id);
            $usersgarage[$id] = $createUserGarage;
            Session::put('garage' . Auth::user()->id, $usersgarage);
            Session::save();
            return "inserted"; // if the garage already exists.
        }
    }

    public function CarDetails($id)
    {
        $car = DB::table('cars as ca')
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
                'ma.id as manuID'
            )
            ->where('ca.id', $id)
            ->first();
        return $car;
    }

    // checking if deliver or pickup
    public function CheckTypeOfTransaction($typeData, $returnID, $pickID, $address)
    {
        if ($typeData == 'pickup') {
            return ['address' => null, 'return_id' => $returnID, 'pick_id' => $pickID];
        } else {
            return ['address' => $address, 'return_id' => null, 'pick_id' => null];
        }
        return "ERROR";
    }

    // update the data on session garage
    public function saveToGarageSession($editedBookInfo)
    {
        $prevoiusGarage = Session::get('garage' . Auth::user()->id);

        $prevoiusGarage[$editedBookInfo['car_id']] = $editedBookInfo;

        Session::put('garage' . Auth::user()->id,  $prevoiusGarage);
        Session::save();
        return $prevoiusGarage;
    }

    // store the data from session to database table 'bookings'
    public function sessionToBooking($usergarage, $id)
    {
        if ($usergarage && $id) {
            try {
                DB::beginTransaction();
                $carToBook = $usergarage[$id];
                $bookingTable = new Booking;
                $bookingTable->customer_id = $carToBook['customer_id'];
                $bookingTable->car_id = $carToBook['car_id'];
                $bookingTable->start_date = $carToBook['start_date'];
                $bookingTable->end_date = $carToBook['end_date'];
                $bookingTable->pickup_location_id = $carToBook['pick_id'];
                $bookingTable->return_location_id = $carToBook['return_id'];
                $bookingTable->address = $carToBook['address'];

                if ($carToBook['driver_id'] == 1) {
                    $bookingTable->driver_id = 1;
                } else {
                    $bookingTable->driver_id = null;
                }
                $bookingTable->status = $carToBook['status'];
                $bookingTable->save();
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
            }
            DB::commit();
            // remove the array of data from session garage id successfully transfered to bookings table
            unset($usergarage[$id]);
            Session::put('garage' . Auth::user()->id, $usergarage);
            Session::save();
            return "success";
        }
    }

    // computes the total rent price.
    public function computationDisplay($start, $end, $price, $accessory, $key){
        $fee = 0;
        if($accessory){
            foreach($accessory->where('car_id', $key) as $accessories){
                $accumulate = $accessories->fee;
                $fee = $fee + $accumulate;
            }
        }
        $datetime1 = date_create($start);
        $datetime2 = date_create($end);
        $diff = date_diff($datetime1, $datetime2);
        $count = $diff->format('%a') + 1;
        if($count == 0){
            return ($price * 1) + $fee;
        }
        return ($price + $fee) * $count;
    }
}
