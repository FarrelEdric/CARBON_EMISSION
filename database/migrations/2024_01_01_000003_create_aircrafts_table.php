<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircrafts', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('model');
            $table->string('icao_type', 10)->nullable();
            $table->string('iata_type', 10)->nullable();
            $table->string('equivalent_aircraft')->nullable()->comment('ICAO equivalent aircraft code');
            $table->integer('y_seats')->nullable()->comment('Economy class seats');
            $table->decimal('fuel_burn_factor', 10, 4)->nullable()->comment('kg per km');
            $table->decimal('passenger_to_freight_factor', 5, 4)->default(0.80)->comment('Share of fuel allocated to passengers');
            $table->decimal('co2_factor', 5, 4)->default(3.16)->comment('ICAO CO2 conversion factor');
            $table->string('data_source')->default('DEMO')->comment('DEMO, ICAO, OFFICIAL');
            $table->text('notes')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('icao_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircrafts');
    }
};
