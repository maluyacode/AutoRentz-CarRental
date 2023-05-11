<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transmission;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;

class TransmissionSeeder extends Seeder
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
        Transmission::create([
            'name' => $faker->vehicleGearBoxType
        ]);
    }
}
