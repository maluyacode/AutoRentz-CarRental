<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        Driver::create([
            'fname' => $faker->firstNameMale(),
            'lname' => $faker->lastName(),
            'licensed_no' => strtoupper($faker->bothify('??#-##-####')),
            'description' => $faker->sentence(6, true),
            'address' => $faker->address(),
            'image_path' => 'DriverImage.png',
            'driver_status' => 'available'
        ]);
    }
}
