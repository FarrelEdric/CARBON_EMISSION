<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarbonCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_carbon_calculator_page_renders_successfully_for_operator()
    {
        $operator = User::create([
            'name'     => 'Operator User',
            'username' => 'operator_user',
            'email'    => 'operator@ace.local',
            'password' => bcrypt('password'),
            'role'     => 'operator',
            'status'   => true,
        ]);

        $response = $this->actingAs($operator)->get('/carbon-calculator');
        $response->assertOk();
        $response->assertSee('Kalkulator Pengurangan Emisi Karbon (CO₂) Rute RNAV');
        $response->assertSee('Total Emisi CO₂ Dikurangi');
        $response->assertSee('Bahan Bakar Avtur Dihemat');
        $response->assertSee('calculator-map');
    }

    public function test_rnav_carbon_emission_calculation_formula_accuracy()
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'username' => 'admin_user',
            'email'    => 'admin@ace.local',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'status'   => true,
        ]);

        $dep = Airport::create([
            'iata_code' => 'CGK',
            'icao_code' => 'WIII',
            'name'      => 'Soekarno-Hatta',
            'city'      => 'Jakarta',
            'country'   => 'Indonesia',
            'latitude'  => -6.1256,
            'longitude' => 106.6559,
            'is_active' => true,
        ]);

        $arr = Airport::create([
            'iata_code' => 'DPS',
            'icao_code' => 'WADD',
            'name'      => 'I Gusti Ngurah Rai',
            'city'      => 'Denpasar',
            'country'   => 'Indonesia',
            'latitude'  => -8.7482,
            'longitude' => 115.1671,
            'is_active' => true,
        ]);

        // Input based on prompt sample:
        // Rute: CGK - DPS
        // Hemat Jarak: 12.1 Nm
        // Hemat Waktu: 1.7 Menit
        // Frekuensi: 50 flt/hari
        // Periode: 91 hari (TW II)
        $payload = [
            'route_name'             => 'CGK - DPS',
            'departure_airport_id'   => $dep->id,
            'arrival_airport_id'     => $arr->id,
            'saved_distance_nm'      => 12.1,
            'saved_time_minutes'     => 1.7,
            'flights_per_day'        => 50,
            'period_days'            => 91,
            'period_label'           => 'Triwulan II (TW II = 91 hari)',
            'nm_to_km'               => 1.852,
            'fuel_burn_rate'         => 3.59,
            'co2_factor'             => 3.15,
            'cost_index'             => 25.0,
            'exchange_rate'          => 15600.0,
            'tree_absorption_factor' => 21.0,
        ];

        $response = $this->actingAs($admin)->postJson('/carbon-calculator/calculate', $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $result = $response->json('result');

        // 1. Total Hari (d) = 91
        $this->assertEquals(91, $result['period_days']);

        // 2. Hemat Jarak (km) = 12.1 * 1.852 = 22.4092 km
        $this->assertEqualsWithDelta(22.4092, $result['saved_distance_km'], 0.001);

        // 3. Total Penerbangan = 50 * 91 = 4550 flights
        $this->assertEquals(4550, $result['total_flights']);

        // 4. Pengurangan Emisi CO2 (kg) = 12.1 * 1.852 * 50 * 91 * 3.59 * 3.15 = 1,153,035.70 kg
        $this->assertEqualsWithDelta(1153035.70, $result['co2_saved_kg'], 1.0);

        // 5. Pengurangan Emisi CO2 (Ton) = 1,153.04 Ton
        $this->assertEqualsWithDelta(1153.0357, $result['co2_saved_ton'], 0.01);

        // 6. BBM Dihemat (Ton) = 1,153,035.70 / (3.15 * 1000) = 366.04 Ton
        $this->assertEqualsWithDelta(366.043, $result['fuel_saved_ton'], 0.01);

        // 7. CO2 per flight (kg) = 1,153,035.70 / 4550 = 253.41 kg
        $this->assertEqualsWithDelta(253.41, $result['co2_per_flight_kg'], 0.1);

        // 8. Penghematan Biaya Operasional (USD) = 1.7 * 25 * 50 * 91 = $193,375.00
        $this->assertEqualsWithDelta(193375.00, $result['cost_saved_usd'], 0.01);

        // 8b. Biaya Dihemat (IDR) = 193375 * 15600 = Rp 3,016,650,000
        $this->assertEquals(3016650000, $result['cost_saved_idr']);

        // 9. Ekuivalensi Serapan Pohon = 1,153,035.70 / 21 = 54906 Pohon
        $this->assertEquals(54906, $result['tree_equivalence']);
    }
}
