<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking as Book;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\AccessInformation;

class Booking extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $newbook = Book::latest()->first();
        $customer = Customer::find($newbook->customer_id);
        $accessInfo = new AccessInformation();
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
            return $this->view('mail-formats.user-book', compact('newbook', 'customer', 'car', 'accessInfo'));
    }
}
