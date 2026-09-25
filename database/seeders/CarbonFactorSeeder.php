<?php

namespace Database\Seeders;

use App\Models\CarbonFactor;
use Illuminate\Database\Seeder;

class CarbonFactorSeeder extends Seeder
{
    public function run(): void
    {
        $factors = [
            [
                'name'           => 'ICAO CO₂ Conversion Factor',
                'factor_key'     => 'icao_co2_factor',
                'factor_value'   => 3.16,
                'unit'           => 'tonne CO₂ / tonne fuel',
                'description'    => 'Standard ICAO factor: 1 tonne of aviation fuel combustion produces 3.16 tonnes of CO₂.',
                'source'         => 'ICAO Carbon Emissions Calculator Methodology, Version 12',
                'effective_date' => '2024-01-01',
                'is_active'      => true,
            ],
            [
                'name'           => 'GHG CO₂ Factor',
                'factor_key'     => 'ghg_co2_factor',
                'factor_value'   => 3.16,
                'unit'           => 'tonne CO₂ / tonne fuel',
                'description'    => 'Greenhouse gas CO₂ equivalent factor for aviation fuel (Jet-A / Jet-A1).',
                'source'         => 'IPCC Guidelines for National Greenhouse Gas Inventories',
                'effective_date' => '2024-01-01',
                'is_active'      => false,
            ],
            [
                'name'           => 'Multiplier Factor (RFI)',
                'factor_key'     => 'rfi_multiplier',
                'factor_value'   => 1.9,
                'unit'           => 'multiplier',
                'description'    => 'Radiative Forcing Index (RFI) - accounts for non-CO₂ climate effects at altitude. NOT used in base ICAO calculator. DEMO ONLY.',
                'source'         => 'DEMO - Various academic sources',
                'effective_date' => '2024-01-01',
                'is_active'      => false,
            ],
        ];

        foreach ($factors as $factor) {
            CarbonFactor::updateOrCreate(
                ['factor_key' => $factor['factor_key']],
                $factor
            );
        }
    }
}
