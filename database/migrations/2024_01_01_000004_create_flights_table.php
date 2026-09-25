<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number', 20);
            $table->date('flight_date');
            $table->foreignId('departure_airport_id')->constrained('airports')->restrictOnDelete();
            $table->foreignId('arrival_airport_id')->constrained('airports')->restrictOnDelete();
            $table->foreignId('aircraft_id')->constrained('aircrafts')->restrictOnDelete();

            // Distance
            $table->decimal('distance_gcd_km', 10, 2)->nullable()->comment('Great Circle Distance (raw Haversine)');
            $table->decimal('distance_adjusted_km', 10, 2)->nullable()->comment('GCD + ICAO correction factor');

            // Fuel & Load
            $table->decimal('total_fuel_kg', 12, 2)->nullable()->comment('Total fuel burned in kg');
            $table->decimal('passenger_to_freight_factor', 5, 4)->nullable()->default(0.80);
            $table->integer('y_seats')->nullable()->comment('Economy class seats for this flight');
            $table->decimal('passenger_load_factor', 5, 4)->nullable()->default(0.80)->comment('0.00 to 1.00');
            $table->integer('passenger_count')->nullable()->comment('Estimated passenger count');

            // CO2
            $table->decimal('co2_factor', 5, 4)->nullable()->default(3.16)->comment('ICAO CO2 conversion factor');
            $table->decimal('co2_total_kg', 12, 2)->nullable()->comment('Total CO2 for the flight in kg');
            $table->decimal('co2_per_passenger_kg', 10, 4)->nullable()->comment('CO2 per passenger in kg');

            // Operational route waypoints as JSON
            $table->json('operational_waypoints')->nullable()->comment('Array of {lat,lng} for operational route visualization');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('flight_date');
            $table->index('flight_number');
            $table->index('departure_airport_id');
            $table->index('arrival_airport_id');
            $table->index('aircraft_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
