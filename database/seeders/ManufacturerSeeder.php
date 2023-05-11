<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;
use App\Models\Manufacturer;
class ManufacturerSeeder extends Seeder
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
        Manufacturer::create([
            'name' => $faker->vehicleBrand
        ]);
    }
}
