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

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $dayBeforeYesterday = now()->subDays(2)->toDateString();
        $lastWeek1 = now()->subDays(5)->toDateString();
        $lastWeek2 = now()->subDays(6)->toDateString();
        $thisMonth1 = now()->subDays(10)->toDateString();
        $thisMonth2 = now()->subDays(18)->toDateString();
        $pastMonth1 = now()->subMonths(1)->startOfMonth()->addDays(4)->toDateString();
        $pastMonth2 = now()->subMonths(2)->startOfMonth()->addDays(12)->toDateString();

        $flightsData = [
            // Today Flights
            [
                'flight_number' => 'GA401',
                'flight_date'   => $today,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'DPS',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 4950,
                'load_factor'   => 0.85,
                'notes'         => 'Garuda Indonesia CGK ke DPS (Hari Ini)',
            ],
            [
                'flight_number' => 'QZ123',
                'flight_date'   => $today,
                'dep_iata'      => 'DPS',
                'arr_iata'      => 'UPG',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 4400,
                'load_factor'   => 0.82,
                'notes'         => 'AirAsia DPS ke UPG (Hari Ini)',
            ],
            // Yesterday Flights
            [
                'flight_number' => 'GA312',
                'flight_date'   => $yesterday,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'SUB',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 4150,
                'load_factor'   => 0.88,
                'notes'         => 'Garuda Indonesia CGK ke SUB (Kemarin)',
            ],
            [
                'flight_number' => 'ID678',
                'flight_date'   => $yesterday,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'KNO',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 6700,
                'load_factor'   => 0.79,
                'notes'         => 'Batik Air CGK ke KNO (Kemarin)',
            ],
            // This Week Flights
            [
                'flight_number' => 'GA608',
                'flight_date'   => $dayBeforeYesterday,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'UPG',
                'aircraft_icao' => 'A333',
                'total_fuel_kg' => 14200,
                'load_factor'   => 0.84,
                'notes'         => 'Garuda Indonesia CGK ke UPG Widebody',
            ],
            [
                'flight_number' => 'JT901',
                'flight_date'   => $dayBeforeYesterday,
                'dep_iata'      => 'BTH',
                'arr_iata'      => 'CGK',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 3850,
                'load_factor'   => 0.80,
                'notes'         => 'Lion Air BTH ke CGK',
            ],
            [
                'flight_number' => 'GA502',
                'flight_date'   => $lastWeek1,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'BPN',
                'aircraft_icao' => 'B38M',
                'total_fuel_kg' => 5800,
                'load_factor'   => 0.86,
                'notes'         => 'Garuda Indonesia CGK ke BPN (737 MAX)',
            ],
            [
                'flight_number' => 'QG202',
                'flight_date'   => $lastWeek2,
                'dep_iata'      => 'SUB',
                'arr_iata'      => 'DPS',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 2400,
                'load_factor'   => 0.81,
                'notes'         => 'Citilink SUB ke DPS',
            ],
            // This Month Flights
            [
                'flight_number' => 'GA712',
                'flight_date'   => $thisMonth1,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'DJJ',
                'aircraft_icao' => 'B77W',
                'total_fuel_kg' => 32500,
                'load_factor'   => 0.77,
                'notes'         => 'Garuda Indonesia CGK ke DJJ (Boeing 777)',
            ],
            [
                'flight_number' => 'SJ182',
                'flight_date'   => $thisMonth1,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'PLM',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 2650,
                'load_factor'   => 0.83,
                'notes'         => 'Sriwijaya Air CGK ke PLM',
            ],
            [
                'flight_number' => 'IW120',
                'flight_date'   => $thisMonth2,
                'dep_iata'      => 'UPG',
                'arr_iata'      => 'MDC',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 4300,
                'load_factor'   => 0.75,
                'notes'         => 'Wings Air UPG ke MDC',
            ],
            [
                'flight_number' => 'GA420',
                'flight_date'   => $thisMonth2,
                'dep_iata'      => 'DPS',
                'arr_iata'      => 'JOG',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 3200,
                'load_factor'   => 0.82,
                'notes'         => 'Garuda Indonesia DPS ke JOG',
            ],
            // Past Months / This Year
            [
                'flight_number' => 'ID701',
                'flight_date'   => $pastMonth1,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'PDG',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 4800,
                'load_factor'   => 0.80,
                'notes'         => 'Batik Air CGK ke PDG',
            ],
            [
                'flight_number' => 'GA802',
                'flight_date'   => $pastMonth2,
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'AMQ',
                'aircraft_icao' => 'A333',
                'total_fuel_kg' => 18900,
                'load_factor'   => 0.79,
                'notes'         => 'Garuda Indonesia CGK ke AMQ',
            ],
            // Baseline 2024 Reference Flights
            [
                'flight_number' => 'GA401-24',
                'flight_date'   => '2024-09-01',
                'dep_iata'      => 'CGK',
                'arr_iata'      => 'DPS',
                'aircraft_icao' => 'B738',
                'total_fuel_kg' => 5000,
                'load_factor'   => 0.82,
                'notes'         => 'Historical flight - CGK ke DPS',
            ],
            [
                'flight_number' => 'QZ123-24',
                'flight_date'   => '2024-09-02',
                'dep_iata'      => 'DPS',
                'arr_iata'      => 'UPG',
                'aircraft_icao' => 'A320',
                'total_fuel_kg' => 4500,
                'load_factor'   => 0.78,
                'notes'         => 'Historical flight - DPS ke UPG',
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
