<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modelo;
use App\Models\Manufacturer;
use App\Models\Type;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Faker\Provider\Fakecar;
use Illuminate\Database\Eloquent\Model;

class ModeloSeeder extends Seeder
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

        $manufacturers = Manufacturer::select('id')->get()->toArray();
        $manufacturerID = $faker->randomElement($manufacturers);

        $types = Type::select('id')->get()->toArray();
        $typeID = $faker->randomElement($types);

        Modelo::create([
            'name' => $faker->vehicleModel,
            'year' => $faker->year(),
            'type_id' => $typeID['id'],
            'manufacturer_id' => $manufacturerID['id']
        ]);
    }
}
