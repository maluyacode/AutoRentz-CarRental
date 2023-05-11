<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('pickup_location_id')
                ->constrained('locations')
                ->nullable()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('return_location_id')
                ->constrained('locations')
                ->nullable(rue)
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('address')
                ->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'finished'])
                ->default('pending');
            $table->foreignId('customer_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('car_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('driver_id')
                ->constrained()
                ->nullable()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
