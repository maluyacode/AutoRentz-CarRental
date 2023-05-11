<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(1)->create();
        $this->call([
            TypeSeeder::class,
            ManufacturerSeeder::class,
            TransmissionSeeder::class,
            FuelSeeder::class,
            AccessorieSeeder::class, //MP
            ModeloSeeder::class,
            CarSeeder::class, // MP
            DriverSeeder::class, // MP
            LocationSeeder::class, // MP
        ]);
    }
}
