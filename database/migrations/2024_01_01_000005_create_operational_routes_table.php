<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_airport_id')->constrained('airports')->restrictOnDelete();
            $table->foreignId('arrival_airport_id')->constrained('airports')->restrictOnDelete();
            $table->string('route_name')->nullable();
            $table->json('waypoints')->nullable()->comment('Array of {lat,lng} objects');
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->string('source')->default('demo')->comment('demo, official, imported');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['departure_airport_id', 'arrival_airport_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_routes');
    }
};
