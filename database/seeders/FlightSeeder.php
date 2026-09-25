<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Aircraft;
use App\Models\Flight;
use App\Services\CarbonEmissionCalculator;
use Illuminate\Database\Seeder;

/**
 * Flight Seeder
 *
 * Membuat 5 dummy flight sesuai spesifikasi.
 * Semua angka adalah DEMO dan hasil kalkulasi otomatis dari CarbonEmissionCalculator.
 */
class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $calculator = new CarbonEmissionCalculator();

        $flightsData = [
            [
                'flight_number' => 'GA401',
                'flight_date'   => '2024-09-01',
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'DPS',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 5000,
                'load_factor'   => 0.82,
                'notes'         => 'DEMO flight - Garuda Indonesia CGK ke DPS',
            ],
            [
                'flight_number' => 'QZ123',
                'flight_date'   => '2024-09-02',
                'dep_iata'      => 'DPS',
                'arr_iata'      => 'UPG',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 4500,
                'load_factor'   => 0.78,
                'notes'         => 'DEMO flight - AirAsia DPS ke UPG',
            ],
            [
                'flight_number' => 'GA312',
                'flight_date'   => '2024-09-03',
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'SUB',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 4200,
                'load_factor'   => 0.85,
                'notes'         => 'DEMO flight - Garuda Indonesia CGK ke SUB',
            ],
            [
                'flight_number' => 'ID678',
                'flight_date'   => '2024-09-04',
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'KNO',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 6800,
                'load_factor'   => 0.75,
                'notes'         => 'DEMO flight - Batik Air CGK ke KNO',
            ],
            [
                'flight_number' => 'JT901',
                'flight_date'   => '2024-09-05',
                'dep_iata'      => 'BTH',
                'arr_iata'      => 'CGK',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 3800,
                'load_factor'   => 0.80,
                'notes'         => 'DEMO flight - Lion Air BTH ke CGK',
            ],
        ];

        foreach ($flightsData as $data) {
            $departure = Airport::where('iata_code', $data['dep_iata'])->first();
            $arrival   = Airport::where('iata_code', $data['arr_iata'])->first();
            $aircraft  = Aircraft::where('icao_type', $data['aircraft_icao'])->first();

            if (!$departure || !$arrival || !$aircraft) {
                $this->command->warn("⚠️  Skipping {$data['flight_number']}: airport atau aircraft tidak ditemukan.");
                continue;
            }

            $calc = $calculator->calculateFromAirports(
                $departure,
                $arrival,
                $data['total_fuel_kg'],
                $aircraft->passenger_to_freight_factor,
                $aircraft->y_seats,
                $data['load_factor'],
                $aircraft->co2_factor
            );

            Flight::updateOrCreate(
                [
                    'flight_number' => $data['flight_number'],
                    'flight_date'   => $data['flight_date'],
                ],
                [
                    'departure_airport_id'        => $departure->id,
                    'arrival_airport_id'           => $arrival->id,
                    'aircraft_id'                  => $aircraft->id,
                    'distance_gcd_km'              => $calc['distance_gcd_km'],
                    'distance_adjusted_km'         => $calc['distance_adjusted_km'],
                    'total_fuel_kg'                => $calc['total_fuel_kg'],
                    'passenger_to_freight_factor'  => $calc['passenger_to_freight_factor'],
                    'y_seats'                      => $calc['y_seats'],
                    'passenger_load_factor'        => $calc['passenger_load_factor'],
                    'passenger_count'              => $calc['passenger_count'],
                    'co2_factor'                   => $calc['co2_factor'],
                    'co2_total_kg'                 => $calc['co2_total_kg'],
                    'co2_per_passenger_kg'         => $calc['co2_per_passenger_kg'],
                    'notes'                        => $data['notes'],
                ]
            );

            $this->command->info(
                "✅ Flight {$data['flight_number']} ({$data['dep_iata']} → {$data['arr_iata']}): " .
                "GCD={$calc['distance_gcd_km']}km, CO₂/pax={$calc['co2_per_passenger_kg']}kg"
            );
        }
    }
}
