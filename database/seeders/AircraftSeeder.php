<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use Illuminate\Database\Seeder;

/**
 * Aircraft Seeder
 *
 * DATA SOURCE: DEMO data — angka fuel_burn_factor adalah estimasi kasar.
 * Angka ini BUKAN data resmi ICAO atau pabrikan.
 * Data production harus diganti dari ICAO Carbon Calculator atau sumber resmi.
 */
class AircraftSeeder extends Seeder
{
    public function run(): void
    {
        $aircrafts = [
            [
                'manufacturer'               => 'Boeing',
                'model'                      => '737-800',
                'icao_type'                  => 'B738',
                'iata_type'                  => '738',
                'equivalent_aircraft'        => 'B737',
                'y_seats'                    => 162,
                'fuel_burn_factor'           => 5.20,   // DEMO: kg/km approx
                'passenger_to_freight_factor'=> 0.80,
                'co2_factor'                 => 3.16,
                'data_source'                => 'DEMO',
                'notes'                      => 'Data DEMO — tidak merepresentasikan data resmi ICAO/Boeing',
                'status'                     => true,
            ],
            [
                'manufacturer'               => 'Airbus',
                'model'                      => 'A320-200',
                'icao_type'                  => 'A320',
                'iata_type'                  => '320',
                'equivalent_aircraft'        => 'A320',
                'y_seats'                    => 150,
                'fuel_burn_factor'           => 4.90,
                'passenger_to_freight_factor'=> 0.80,
                'co2_factor'                 => 3.16,
                'data_source'                => 'DEMO',
                'notes'                      => 'Data DEMO — tidak merepresentasikan data resmi ICAO/Airbus',
                'status'                     => true,
            ],
            [
                'manufacturer'               => 'Boeing',
                'model'                      => '737 MAX 8',
                'icao_type'                  => 'B38M',
                'iata_type'                  => '7M8',
                'equivalent_aircraft'        => 'B737',
                'y_seats'                    => 178,
                'fuel_burn_factor'           => 4.70,
                'passenger_to_freight_factor'=> 0.80,
                'co2_factor'                 => 3.16,
                'data_source'                => 'DEMO',
                'notes'                      => 'Data DEMO — tidak merepresentasikan data resmi ICAO/Boeing',
                'status'                     => true,
            ],
            [
                'manufacturer'               => 'Airbus',
                'model'                      => 'A330-300',
                'icao_type'                  => 'A333',
                'iata_type'                  => '333',
                'equivalent_aircraft'        => 'A330',
                'y_seats'                    => 295,
                'fuel_burn_factor'           => 10.50,
                'passenger_to_freight_factor'=> 0.82,
                'co2_factor'                 => 3.16,
                'data_source'                => 'DEMO',
                'notes'                      => 'Data DEMO — tidak merepresentasikan data resmi ICAO/Airbus',
                'status'                     => true,
            ],
            [
                'manufacturer'               => 'Boeing',
                'model'                      => '777-300ER',
                'icao_type'                  => 'B77W',
                'iata_type'                  => '77W',
                'equivalent_aircraft'        => 'B777',
                'y_seats'                    => 386,
                'fuel_burn_factor'           => 14.20,
                'passenger_to_freight_factor'=> 0.75,
                'co2_factor'                 => 3.16,
                'data_source'                => 'DEMO',
                'notes'                      => 'Data DEMO — tidak merepresentasikan data resmi ICAO/Boeing',
                'status'                     => true,
            ],
        ];

        foreach ($aircrafts as $aircraft) {
            Aircraft::updateOrCreate(
                ['icao_type' => $aircraft['icao_type']],
                $aircraft
            );
        }

        $this->command->info('✅ ' . count($aircrafts) . ' pesawat demo berhasil di-seed.');
        $this->command->warn('⚠️  Data pesawat ini adalah DEMO. Fuel burn factor BUKAN data resmi ICAO.');
    }
}
