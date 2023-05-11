<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Accessorie;
use App\Models\Fuel;
use App\Models\Transmission;
use App\Models\Modelo;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = (new Faker())::create();
        $faker->addProvider(new Fakecar($faker));

        $transimssions = Transmission::select('id')->get()->toArray();
        $transmissionID = $faker->randomElement($transimssions);
        // dd($transmissionID['id']);
        $fuels = Fuel::select('id')->get()->toArray();
        $fuelID = $faker->randomElement($fuels);

        $modelo = Modelo::select('id')->get()->toArray();
        $modeloID = $faker->randomElement($modelo);


        Car::create([
            'platenumber' => $faker->vehicleRegistration('[A-Z]{3} [0-9]{4}'),
            'price_per_day' => $faker->randomFloat(2, 1000, 3000),
            'seats' => $faker->vehicleSeatCount,
            'description' => $faker->text(500),
            'image_path' => 'FakeCarImage' . $faker->numberBetween(1, 6) . '.png' . '=' . 'FakeCarImage' . $faker->numberBetween(1, 6) . '.png',
            'cost_price' => $faker->randomFloat(2, 50000, 500000),
            'modelos_id' => $modeloID['id'],
            'transmission_id' => $transmissionID['id'],
            'fuel_id' => $fuelID['id'],
            'car_status' => 'available',
        ]);
        $carID = Car::max('id');
        $faker = Faker::create();
        $accessorie = Accessorie::select('id')->get()->toArray();
        for ($i = 0; $i <= $faker->numberBetween(1, 6); $i++) {
            $accessorieID = $faker->randomElement($accessorie);
            DB::table('accessorie_car')->insert([
                'car_id' => $carID,
                'accessorie_id' => $accessorieID['id'],
            ]);
        }
    }
}
