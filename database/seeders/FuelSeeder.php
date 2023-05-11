<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fuel;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;

class FuelSeeder extends Seeder
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
        Fuel::create([
            'name' => $faker->vehicleFuelType
        ]);
    }
}
