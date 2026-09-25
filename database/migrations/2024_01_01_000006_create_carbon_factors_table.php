<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carbon_factors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('factor_key')->unique()->comment('e.g. icao_co2_factor, ghg_co2');
            $table->decimal('factor_value', 10, 6);
            $table->string('unit')->nullable()->comment('e.g. tonne CO2/tonne fuel');
            $table->text('description')->nullable();
            $table->string('source')->nullable()->comment('ICAO Doc 9889, etc.');
            $table->date('effective_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carbon_factors');
    }
};
