<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\OperationalRoute;
use Illuminate\Database\Seeder;

/**
 * Operational Route Seeder
 *
 * Membuat rute operasional dengan waypoint untuk visualisasi garis biru pada peta.
 * Data ini adalah DEMO dan BUKAN rute airway resmi AirNav Indonesia.
 * Label "Demo Operational Route" harus ditampilkan di UI.
 */
class OperationalRouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            [
                'dep_iata' => 'CGK',
                'arr_iata' => 'DPS',
                'route_name' => 'CGK-DPS Demo',
                'waypoints' => [
                    ['lat' => -6.1256, 'lng' => 106.6559],  // CGK
                    ['lat' => -6.5,    'lng' => 107.5],      // waypoint 1
                    ['lat' => -7.0,    'lng' => 109.0],      // waypoint 2
                    ['lat' => -7.5,    'lng' => 111.5],      // waypoint 3
                    ['lat' => -8.0,    'lng' => 113.0],      // waypoint 4
                    ['lat' => -8.7482, 'lng' => 115.1671],   // DPS
                ],
                'distance_km' => 960,
                'source' => 'demo',
            ],
            [
                'dep_iata' => 'DPS',
                'arr_iata' => 'UPG',
                'route_name' => 'DPS-UPG Demo',
                'waypoints' => [
                    ['lat' => -8.7482, 'lng' => 115.1671],   // DPS
                    ['lat' => -8.5,    'lng' => 116.5],       // waypoint 1
                    ['lat' => -7.5,    'lng' => 117.5],       // waypoint 2
                    ['lat' => -6.0,    'lng' => 118.5],       // waypoint 3
                    ['lat' => -5.0616, 'lng' => 119.5540],    // UPG
                ],
                'distance_km' => 500,
                'source' => 'demo',
            ],
            [
                'dep_iata' => 'CGK',
                'arr_iata' => 'SUB',
                'route_name' => 'CGK-SUB Demo',
                'waypoints' => [
                    ['lat' => -6.1256, 'lng' => 106.6559],   // CGK
                    ['lat' => -6.5,    'lng' => 107.5],       // waypoint 1
                    ['lat' => -6.9,    'lng' => 108.5],       // waypoint 2
                    ['lat' => -7.1,    'lng' => 110.0],       // waypoint 3
                    ['lat' => -7.3798, 'lng' => 112.7870],    // SUB
                ],
                'distance_km' => 680,
                'source' => 'demo',
            ],
            [
                'dep_iata' => 'CGK',
                'arr_iata' => 'KNO',
                'route_name' => 'CGK-KNO Demo',
                'waypoints' => [
                    ['lat' => -6.1256, 'lng' => 106.6559],   // CGK
                    ['lat' => -4.5,    'lng' => 105.5],       // waypoint 1
                    ['lat' => -2.0,    'lng' => 104.0],       // waypoint 2
                    ['lat' => 0.5,     'lng' => 101.5],       // waypoint 3
                    ['lat' => 2.0,     'lng' => 99.5],        // waypoint 4
                    ['lat' => 3.6422,  'lng' => 98.8853],     // KNO
                ],
                'distance_km' => 1350,
                'source' => 'demo',
            ],
            [
                'dep_iata' => 'BTH',
                'arr_iata' => 'CGK',
                'route_name' => 'BTH-CGK Demo',
                'waypoints' => [
                    ['lat' => 1.1212,  'lng' => 104.1192],   // BTH
                    ['lat' => 0.0,     'lng' => 104.0],       // waypoint 1
                    ['lat' => -2.5,    'lng' => 105.2],       // waypoint 2
                    ['lat' => -5.0,    'lng' => 106.0],       // waypoint 3
                    ['lat' => -6.1256, 'lng' => 106.6559],    // CGK
                ],
                'distance_km' => 830,
                'source' => 'demo',
            ],
        ];

        foreach ($routes as $route) {
            $departure = Airport::where('iata_code', $route['dep_iata'])->first();
            $arrival   = Airport::where('iata_code', $route['arr_iata'])->first();

            if (!$departure || !$arrival) {
                $this->command->warn("⚠️  Skipping {$route['dep_iata']}→{$route['arr_iata']}: airport tidak ditemukan.");
                continue;
            }

            OperationalRoute::updateOrCreate(
                [
                    'departure_airport_id' => $departure->id,
                    'arrival_airport_id'   => $arrival->id,
                ],
                [
                    'route_name'  => $route['route_name'],
                    'waypoints'   => $route['waypoints'],
                    'distance_km' => $route['distance_km'],
                    'source'      => $route['source'],
                    'status'      => true,
                ]
            );

            $this->command->info("✅ Rute demo {$route['dep_iata']} → {$route['arr_iata']} berhasil dibuat.");
        }

        $this->command->warn('⚠️  Semua rute ini adalah DEMO OPERATIONAL ROUTE, BUKAN airway resmi AirNav Indonesia.');
    }
}
