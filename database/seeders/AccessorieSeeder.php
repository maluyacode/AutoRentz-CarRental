<?php

namespace Database\Seeders;

use App\Models\Accessorie;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;

class AccessorieSeeder extends Seeder
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
        for ($i = 0; $i < 10; $i++) {
            foreach ($faker->vehicleProperties as $key => $name) {
                if (Accessorie::where('name', $name)->exists()) {
                    $name = $faker->word();
                }
                Accessorie::create([
                    'name' => $name,
                    'fee' => $faker->randomFloat(2, 500, 2000),
                    'image_path' => 'accessories.png' . '=' . 'accessories2.png'
                ]);
            }
        }
    }
}
