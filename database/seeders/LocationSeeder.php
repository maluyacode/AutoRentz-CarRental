<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        Location::create([
            'street' => $faker->streetAddress(),
            'baranggay' => $faker->streetName(),
            'city' => $faker->city(),
            'image_path' => 'LocationImage.png=LocationImage2.png',
        ]);
    }
}
