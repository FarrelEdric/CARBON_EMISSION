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
}
