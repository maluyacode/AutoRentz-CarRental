<?php

namespace App\Imports;

use App\Models\Accessorie;
use App\Models\Car;
use App\Models\Fuel;
use App\Models\Modelo;
use App\Models\Transmission;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CarImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $car = Car::create([
                "platenumber" => $row["platenumber"],
                "price_per_day" => $row["rent_price"],
                "seats" => $row["seats"],
                "description" => $row["description"],
                "image_path" => 'Switched in media',
                "cost_price" => $row["cost_price"],
                "modelos_id" => Modelo::where('name', trim($row["model"]))->value('id'),
                "transmission_id" => Transmission::where('name', trim($row["transmission"]))->value('id'),
                "fuel_id" => Fuel::where('name', trim($row["fuel"]))->value('id'),
            ]);
            $accessories = Accessorie::whereIn('name', explode(',', $row['accessories']))->get();
            $ids = $accessories->map(function ($data) {
                return $data->id;
            });
            $car->accessories()->attach($ids);
        }
    }
}
