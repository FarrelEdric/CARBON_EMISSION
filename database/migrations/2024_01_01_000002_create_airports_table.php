<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('iata_code', 10)->unique()->nullable();
            $table->string('icao_code', 10)->unique()->nullable();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country', 10)->default('ID');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('elevation')->nullable()->comment('Feet AMSL');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('iata_code');
            $table->index('icao_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
