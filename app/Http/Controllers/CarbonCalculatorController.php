<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Aircraft;
use App\Models\CarbonFactor;
use App\Models\OperationalRoute;
use App\Services\CarbonEmissionCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CarbonCalculatorController extends Controller
{
    public function __construct(private CarbonEmissionCalculator $calculator) {}

    public function index(): View
    {
        $airports = Airport::active()->orderBy('iata_code')->get([
            'id', 'iata_code', 'icao_code', 'name', 'city', 'latitude', 'longitude'
        ]);

        $aircraft = Aircraft::active()->orderBy('manufacturer')->get([
            'id', 'manufacturer', 'model', 'icao_type', 'y_seats', 'fuel_burn_factor', 'passenger_to_freight_factor', 'co2_factor'
        ]);

        $defaultCo2Factor = CarbonFactor::where('factor_key', 'icao_co2_factor')->where('is_active', true)->value('factor_value') ?? 3.16;

        return view('carbon-calculator.index', compact('airports', 'aircraft', 'defaultCo2Factor'));
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_airport_id'        => 'required|exists:airports,id',
            'arrival_airport_id'          => 'required|exists:airports,id',
            'aircraft_id'                 => 'nullable|exists:aircrafts,id',
            'total_fuel_kg'               => 'required|numeric|min:0.01',
            'passenger_to_freight_factor' => 'required|numeric|between:0,1',
            'y_seats'                     => 'required|integer|min:1',
            'passenger_load_factor'       => 'required|numeric|between:0,1',
            'co2_factor'                  => 'nullable|numeric|min:0',
        ]);

        $departure = Airport::findOrFail($validated['departure_airport_id']);
        $arrival   = Airport::findOrFail($validated['arrival_airport_id']);

        if ($departure->id === $arrival->id) {
            return response()->json(['error' => 'Bandara asal dan tujuan tidak boleh sama.'], 422);
        }

        if (!$departure->latitude || !$departure->longitude || !$arrival->latitude || !$arrival->longitude) {
            return response()->json(['error' => 'Data koordinat bandara tidak lengkap untuk kalkulasi jarak.'], 422);
        }

        $co2Factor = (float) ($validated['co2_factor'] ?? CarbonFactor::getIcaoCo2Factor());

        $result = $this->calculator->calculateFromAirports(
            $departure,
            $arrival,
            (float) $validated['total_fuel_kg'],
            (float) $validated['passenger_to_freight_factor'],
            (int) $validated['y_seats'],
            (float) $validated['passenger_load_factor'],
            $co2Factor
        );

        // Check if an operational route exists in Master Data
        $operationalRoute = OperationalRoute::where('departure_airport_id', $departure->id)
            ->where('arrival_airport_id', $arrival->id)
            ->where('status', true)
            ->first();

        $opRouteData = null;
        if ($operationalRoute) {
            $opRouteData = [
                'name'        => $operationalRoute->route_name ?? 'Rute Resmi AirNav',
                'distance_km' => $operationalRoute->distance_km,
                'source'      => $operationalRoute->source_label,
                'waypoints'   => $operationalRoute->waypoints,
            ];
        }

        return response()->json([
            'success'           => true,
            'result'            => $result,
            'departure'         => [
                'id'        => $departure->id,
                'iata_code' => $departure->iata_code,
                'icao_code' => $departure->icao_code,
                'name'      => $departure->name,
                'city'      => $departure->city,
                'lat'       => (float) $departure->latitude,
                'lng'       => (float) $departure->longitude,
            ],
            'arrival'           => [
                'id'        => $arrival->id,
                'iata_code' => $arrival->iata_code,
                'icao_code' => $arrival->icao_code,
                'name'      => $arrival->name,
                'city'      => $arrival->city,
                'lat'       => (float) $arrival->latitude,
                'lng'       => (float) $arrival->longitude,
            ],
            'operational_route' => $opRouteData,
        ]);
    }
}
