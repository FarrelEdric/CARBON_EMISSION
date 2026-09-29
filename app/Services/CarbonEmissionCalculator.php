<?php

namespace App\Services;

use App\Models\Airport;

/**
 * Carbon Emission Calculator Service
 *
 * Implements ICAO-based methodology for estimating CO₂ emissions
 * per passenger for commercial aviation flights.
 *
 * IMPORTANT: These calculations are ESTIMATES based on ICAO methodology.
 * Results are NOT direct measurements of actual engine emissions.
 */
class CarbonEmissionCalculator
{
    /**
     * ICAO CO₂ conversion factor
     * 1 tonne aviation fuel = 3.16 tonnes CO₂
     */
    const ICAO_CO2_FACTOR = 3.16;

    /**
     * Distance correction factors (ICAO methodology)
     */
    const CORRECTION_SHORT = 50;   // < 550 km
    const CORRECTION_MED   = 100;  // 550–5500 km
    const CORRECTION_LONG  = 125;  // > 5500 km

    /**
     * Calculate Great Circle Distance using the Haversine formula.
     *
     * @param  float  $lat1  Departure latitude
     * @param  float  $lng1  Departure longitude
     * @param  float  $lat2  Arrival latitude
     * @param  float  $lng2  Arrival longitude
     * @return float  Distance in kilometers
     */
    public function calculateGCD(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Get ICAO distance correction factor in km.
     *
     * @param  float  $gcdKm  Raw Great Circle Distance in km
     * @return float  Correction distance in km (50, 100, or 125)
     */
    public function getDistanceCorrection(float $gcdKm): float
    {
        return (float) match(true) {
            $gcdKm < 550   => self::CORRECTION_SHORT,
            $gcdKm <= 5500 => self::CORRECTION_MED,
            default        => self::CORRECTION_LONG,
        };
    }

    /**

     * @param  float  $gcdKm  Raw Great Circle Distance in km
     * @return float  Adjusted distance in km
     */
    public function applyDistanceCorrection(float $gcdKm): float
    {
        return round($gcdKm + $this->getDistanceCorrection($gcdKm), 2);
    }

    /**
     *
     * @param  float  $totalFuelKg             Total fuel burned (kg)
     * @param  float  $passengerToFreightFactor Fraction of fuel attributed to passengers (0–1)
     * @param  int    $ySeats                  Economy class seat count
     * @param  float  $passengerLoadFactor     Load factor as decimal (0–1)
     * @param  float  $co2Factor               CO₂ conversion factor (default 3.16)
     * @return float  CO₂ per passenger in kg
     */
    public function calculateCo2PerPassenger(
        float $totalFuelKg,
        float $passengerToFreightFactor,
        int   $ySeats,
        float $passengerLoadFactor,
        float $co2Factor = self::ICAO_CO2_FACTOR
    ): float {
        if ($ySeats <= 0 || $passengerLoadFactor <= 0) {
            return 0.0;
        }

        $passengerFuel = $totalFuelKg * $passengerToFreightFactor;
        $estimatedPassengers = $ySeats * $passengerLoadFactor;

        return round($co2Factor * $passengerFuel / $estimatedPassengers, 4);
    }

    /**
     *
     * @param  float  $totalFuelKg  Total fuel in kg
     * @param  float  $co2Factor    CO₂ conversion factor
     * @return float  Total CO₂ in kg
     */
    public function calculateTotalCo2(
        float $totalFuelKg,
        float $co2Factor = self::ICAO_CO2_FACTOR
    ): float {
        return round($totalFuelKg * $co2Factor, 2);
    }

    /**
     * Calculate estimated passenger count.
     */
    public function calculatePassengerCount(int $ySeats, float $loadFactor): int
    {
        return (int) round($ySeats * $loadFactor);
    }

    /**

     * @param  array  $params  {

     * @return array
     */
    public function calculate(array $params): array
    {
        $gcd = $this->calculateGCD(
            (float) $params['departure_lat'],
            (float) $params['departure_lng'],
            (float) $params['arrival_lat'],
            (float) $params['arrival_lng']
        );

        $adjustedDistance = $this->applyDistanceCorrection($gcd);
        $correction = $this->getDistanceCorrection($gcd);

        $co2Factor = (float) ($params['co2_factor'] ?? self::ICAO_CO2_FACTOR);
        $totalFuelKg = (float) $params['total_fuel_kg'];
        $paxFreightFactor = (float) $params['passenger_to_freight_factor'];
        $ySeats = (int) $params['y_seats'];
        $loadFactor = (float) $params['passenger_load_factor'];

        $passengerFuelKg = $totalFuelKg * $paxFreightFactor;
        $freightFuelKg = max(0.0, $totalFuelKg - $passengerFuelKg);
        $estimatedPassengers = $this->calculatePassengerCount($ySeats, $loadFactor);
        $co2TotalKg = $this->calculateTotalCo2($totalFuelKg, $co2Factor);
        $passengerCo2TotalKg = round($passengerFuelKg * $co2Factor, 2);
        $passengerCo2TotalTonnes = round($passengerCo2TotalKg / 1000, 4);
        $freightCo2TotalKg = round($freightFuelKg * $co2Factor, 2);
        $freightCo2TotalTonnes = round($freightCo2TotalKg / 1000, 4);
        $co2PerPassenger = $this->calculateCo2PerPassenger(
            $totalFuelKg, $paxFreightFactor, $ySeats, $loadFactor, $co2Factor
        );

        return [
            'distance_gcd_km'             => $gcd,
            'distance_adjusted_km'        => $adjustedDistance,
            'correction_km'               => $correction,
            'total_fuel_kg'               => $totalFuelKg,
            'passenger_fuel_kg'           => round($passengerFuelKg, 2),
            'freight_fuel_kg'             => round($freightFuelKg, 2),
            'passenger_to_freight_factor' => $paxFreightFactor,
            'y_seats'                     => $ySeats,
            'passenger_load_factor'       => $loadFactor,
            'passenger_count'             => $estimatedPassengers,
            'co2_factor'                  => $co2Factor,
            'co2_total_kg'                => $co2TotalKg,
            'co2_total_tonnes'            => round($co2TotalKg / 1000, 4),
            'passenger_co2_total_kg'      => $passengerCo2TotalKg,
            'passenger_co2_total_tonnes'  => $passengerCo2TotalTonnes,
            'freight_co2_total_kg'        => $freightCo2TotalKg,
            'freight_co2_total_tonnes'    => $freightCo2TotalTonnes,
            'co2_per_passenger_kg'        => $co2PerPassenger,
        ];
    }

    /**
     * Calculate from airport models directly.
     */
    public function calculateFromAirports(
        Airport $departure,
        Airport $arrival,
        float $totalFuelKg,
        float $paxFreightFactor,
        int $ySeats,
        float $loadFactor,
        float $co2Factor = self::ICAO_CO2_FACTOR
    ): array {
        return $this->calculate([
            'departure_lat'               => $departure->latitude,
            'departure_lng'               => $departure->longitude,
            'arrival_lat'                 => $arrival->latitude,
            'arrival_lng'                 => $arrival->longitude,
            'total_fuel_kg'               => $totalFuelKg,
            'passenger_to_freight_factor' => $paxFreightFactor,
            'y_seats'                     => $ySeats,
            'passenger_load_factor'       => $loadFactor,
            'co2_factor'                  => $co2Factor,
        ]);
    }

    /**
     * Calculate RNAV Route Efficiency & Carbon Emission Reduction (AirNav Standard)
     *
     * Rumus:
     * - Total Penerbangan = fl * d
     * - Hemat Jarak (km/flight) = sd * 1.852
     * - Total Hemat Jarak (km) = sd * 1.852 * fl * d
     * - CO2 (kg) = sd * 1.852 * fl * d * fb * ef
     * - CO2 (ton) = CO2 (kg) / 1000
     * - Avtur Dihemat (kg) = CO2 (kg) / ef = sd * 1.852 * fl * d * fb
     * - Avtur Dihemat (ton) = Avtur Dihemat (kg) / 1000
     * - CO2 per flight (kg) = CO2 (kg) / (fl * d) = sd * 1.852 * fb * ef
     * - Hemat Biaya (USD) = tm * ci * fl * d
     * - Hemat Biaya (IDR) = Hemat Biaya (USD) * fx
     * - Ekuivalensi Pohon = round(CO2 (kg) / 21)
     */
    public function calculateRnavEfficiency(array $params): array
    {
        $sd = (float) ($params['saved_distance_nm'] ?? 0);
        $tm = (float) ($params['saved_time_minutes'] ?? 0);
        $fl = (float) ($params['flights_per_day'] ?? 0);
        $d  = (int) ($params['period_days'] ?? 1);

        $nmToKm      = (float) ($params['nm_to_km'] ?? 1.852);
        $fb          = (float) ($params['fuel_burn_rate'] ?? 3.59);
        $ef          = (float) ($params['co2_factor'] ?? 3.15);
        $ci          = (float) ($params['cost_index'] ?? 25.0);
        $fx          = (float) ($params['exchange_rate'] ?? 15600.0);
        $treeFactor  = (float) ($params['tree_absorption_factor'] ?? 21.0);
        $periodLabel = (string) ($params['period_label'] ?? ($d . ' Hari'));

        $totalFlights = (int) round($fl * $d);
        $savedDistanceKm = round($sd * $nmToKm, 4);
        $totalSavedDistanceKm = round($savedDistanceKm * $fl * $d, 2);

        // CO2 Saved
        $co2SavedKg = round($sd * $nmToKm * $fl * $d * $fb * $ef, 2);
        $co2SavedTon = round($co2SavedKg / 1000, 4);

        // Fuel Saved
        $fuelSavedKg = $ef > 0 ? round($co2SavedKg / $ef, 2) : 0.0;
        $fuelSavedTon = round($fuelSavedKg / 1000, 4);

        // CO2 per flight
        $co2PerFlightKg = $totalFlights > 0
            ? round($co2SavedKg / ($fl * $d), 2)
            : round($sd * $nmToKm * $fb * $ef, 2);

        // Cost Savings
        $costSavedUsd = round($tm * $ci * $fl * $d, 2);
        $costSavedIdr = (int) round($costSavedUsd * $fx);

        // Tree Equivalence
        $treeEquivalence = $treeFactor > 0 ? (int) round($co2SavedKg / $treeFactor) : 0;

        return [
            'saved_distance_nm'       => $sd,
            'saved_time_minutes'      => $tm,
            'flights_per_day'         => $fl,
            'period_days'             => $d,
            'period_label'            => $periodLabel,
            'nm_to_km'                => $nmToKm,
            'fuel_burn_rate'          => $fb,
            'co2_factor'              => $ef,
            'cost_index'              => $ci,
            'exchange_rate'           => $fx,
            'tree_absorption_factor'  => $treeFactor,
            'total_flights'           => $totalFlights,
            'saved_distance_km'       => $savedDistanceKm,
            'total_saved_distance_km' => $totalSavedDistanceKm,
            'co2_saved_kg'            => $co2SavedKg,
            'co2_saved_ton'           => $co2SavedTon,
            'fuel_saved_kg'           => $fuelSavedKg,
            'fuel_saved_ton'          => $fuelSavedTon,
            'co2_per_flight_kg'       => $co2PerFlightKg,
            'cost_saved_usd'          => $costSavedUsd,
            'cost_saved_idr'          => $costSavedIdr,
            'tree_equivalence'        => $treeEquivalence,
        ];
    }
}
