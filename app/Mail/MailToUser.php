<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\CustomerClass;
use App\Models\Booking as Book;
use App\AccessInformation;
use App\Models\Customer;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class MailToUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $newbook = Book::find($this->id);
        $customer = Customer::find($newbook->customer_id);
        $accessInfo = new AccessInformation();
        if ($newbook->driver_id) {
            $driver = Driver::find($newbook->driver_id);
        } else {
            $driver = null;
        }

        $car = DB::table('cars as ca')
            ->join('fuels as fu', 'fu.id', 'ca.fuel_id')
            ->join('transmissions as ta', 'ta.id', 'ca.transmission_id')
            // ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            // ->join('accessories as ac', 'ac.id', 'ac_ca.accessorie_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->join('types as ty', 'ty.id', 'mo.type_id')
            ->join('manufacturers as ma', 'ma.id', 'mo.manufacturer_id')
            ->select(
                'ca.*',
                'fu.name as fuelname',
                'ta.name as transname',
                // 'ac.name as accessoryname', 'ac.fee as accessoryfee',
                'mo.name as modelname',
                'mo.year as modelyear',
                'ty.name as typename',
                'ma.name as manufacturername',
                'fu.id as fuelID',
                'ta.id as transID', /*'ac.id as accID',*/
                'mo.id as modelID',
                'ty.id as typeID',
                'ma.id as manuID'
            )->where('ca.id', $newbook->car_id)
            ->first();
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        $customerClass = new CustomerClass();
        $totalPrice = $customerClass->computationDisplay($newbook->start_date, $newbook->end_date, $car->price_per_day, $accessory, $car->id);

        return $this->view('mail-formats.admin-confirmed', compact('newbook', 'customer', 'car', 'accessInfo', 'driver', 'totalPrice'));
    }
}
