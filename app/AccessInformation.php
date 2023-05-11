<?php

namespace App;

use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use DateTime;
use Faker\Factory as Faker;

class AccessInformation
{
    public function __construct()
    {
    }

    public function picklocation($id)
    {
        $picklocation = DB::table('locations')
            ->join('bookings', 'locations.id', 'bookings.pickup_location_id')
            ->where('locations.id', $id)
            ->first();
        return $picklocation->street . " " . $picklocation->baranggay . " " . $picklocation->city;
    }

    public function returnlocation($id)
    {
        $returnlocation = DB::table('locations')
            ->join('bookings', 'locations.id', 'bookings.return_location_id')
            ->where('locations.id', $id)
            ->first();
        return $returnlocation->street . " " . $returnlocation->baranggay . " " . $returnlocation->city;
    }

    public function car($id)
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

    public function concatCarName($car_id)
    {
        $car = $this->car($car_id);
        return $car->platenumber . ' - ' . $car->modelname . ' ' . $car->modelyear;
    }

    public function driverInfo($id)
    {
        $driver = Driver::find($id);
        return $driver->fname . ' ' . $driver->lname;
    }

    public function countDays($start_date, $end_date)
    {
        if ($start_date || $end_date) {
            $datetime1 = date_create($start_date);
            $datetime2 = date_create($end_date);
            $diff = date_diff($datetime1, $datetime2);
            $count = (int) $diff->format('%a');
            $display = $count + 1;
            return $display;
        }
    }

    public function formatDate($rawdate)
    {
        $date_string = $rawdate;
        $dateStr = new DateTime($date_string);
        $formatedDate = date_format($dateStr, 'M d, Y');
        return $formatedDate;
    }

    public function carJoined()
    {
        return $cars = DB::table('cars as ca')
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
            );
    }
}
