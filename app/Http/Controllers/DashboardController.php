<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\OperationalRoute;
use App\Services\FlightTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request);

        $query = $this->buildFlightQuery($filters);

        // KPI Aggregations
        $kpis = $this->calculateKpis(clone $query);

        // Chart Data
        $charts = $this->buildChartData($filters);

        // Flight Table (recent flights)
        $flights = (clone $query)
            ->with(['departureAirport', 'arrivalAirport', 'aircraft'])
            ->orderByDesc('flight_date')
            ->limit(20)
            ->get();

        // Airports for filter dropdowns
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name', 'city']);
        $aircraft = \App\Models\Aircraft::active()->orderBy('manufacturer')->get(['id', 'manufacturer', 'model', 'icao_type']);

        return view('dashboard.index', compact(
            'kpis', 'flights', 'airports', 'aircraft', 'filters', 'charts'
        ));
    }

    public function mapData(Request $request): JsonResponse
    {
        $filters = $this->getFilters($request);
        $query   = $this->buildFlightQuery($filters);

        $flights = (clone $query)
            ->with(['departureAirport', 'arrivalAirport'])
            ->get();

        $routes     = OperationalRoute::with(['departureAirport', 'arrivalAirport'])->active()->get();
        $airportIds = $flights->pluck('departure_airport_id')
            ->merge($flights->pluck('arrival_airport_id'))
            ->unique();
        $airports   = Airport::whereIn('id', $airportIds)->get();

        return response()->json([
            'airports' => $airports->map(fn($a) => [
                'id'        => $a->id,
                'iata_code' => $a->iata_code,
                'name'      => $a->name,
                'city'      => $a->city,
                'lat'       => $a->latitude,
                'lng'       => $a->longitude,
            ]),
            'flights' => $flights->map(fn($f) => [
                'id'             => $f->id,
                'flight_number'  => $f->flight_number,
                'flight_date'    => $f->flight_date->format('d M Y'),
                'dep_lat'        => $f->departureAirport?->latitude,
                'dep_lng'        => $f->departureAirport?->longitude,
                'dep_iata'       => $f->departureAirport?->iata_code,
                'arr_lat'        => $f->arrivalAirport?->latitude,
                'arr_lng'        => $f->arrivalAirport?->longitude,
                'arr_iata'       => $f->arrivalAirport?->iata_code,
                'co2_per_pax'    => round($f->co2_per_passenger_kg, 2),
                'co2_total'      => round($f->co2_total_kg, 2),
                'distance_gcd'   => $f->distance_gcd_km,
            ]),
            'operationalRoutes' => $routes->map(fn($r) => [
                'id'         => $r->id,
                'route_name' => $r->route_name,
                'waypoints'  => $r->waypoints,
                'source'     => $r->source,
                'source_label' => $r->source_label,
                'dep_iata'   => $r->departureAirport?->iata_code,
                'arr_iata'   => $r->arrivalAirport?->iata_code,
            ]),
        ]);
    }

    public function chartData(Request $request): JsonResponse
    {
        $filters = $this->getFilters($request);
        return response()->json($this->buildChartData($filters));
    }

    public function liveFlights(Request $request, FlightTrackingService $trackingService): JsonResponse
    {
        $customBbox = null;
        if ($request->filled(['lamin', 'lomin', 'lamax', 'lomax'])) {
            $customBbox = [
                'lamin' => (float) $request->get('lamin'),
                'lomin' => (float) $request->get('lomin'),
                'lamax' => (float) $request->get('lamax'),
                'lomax' => (float) $request->get('lomax'),
            ];
        }

        $data = $trackingService->getLiveFlights($customBbox);

        return response()->json($data);
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    private function getFilters(Request $request): array
    {
        return [
            'period'     => $request->get('period', 'this-year'),
            'date_from'  => $request->get('date_from'),
            'date_to'    => $request->get('date_to'),
            'origin'     => $request->get('origin'),
            'destination'=> $request->get('destination'),
            'aircraft'   => $request->get('aircraft'),
            'flight_number' => $request->get('flight_number'),
        ];
    }

    private function buildFlightQuery(array $filters)
    {
        return Flight::query()
            ->byPeriod($filters['period'], $filters['date_from'], $filters['date_to'])
            ->byDeparture($filters['origin'])
            ->byArrival($filters['destination'])
            ->byAircraft($filters['aircraft'] ? (int) $filters['aircraft'] : null)
            ->byFlightNumber($filters['flight_number'] ?? null);
    }

    private function calculateKpis($query): array
    {
        $data = (clone $query)->selectRaw('
            COUNT(*) as total_flights,
            COALESCE(SUM(co2_total_kg), 0) as total_co2_kg,
            COALESCE(AVG(co2_per_passenger_kg), 0) as avg_co2_per_pax,
            COALESCE(SUM(total_fuel_kg), 0) as total_fuel_kg,
            COALESCE(SUM(passenger_count), 0) as total_passengers,
            COALESCE(AVG(passenger_load_factor), 0) as avg_load_factor
        ')->first();

        return [
            'total_flights'     => (int) ($data->total_flights ?? 0),
            'total_co2_kg'      => round($data->total_co2_kg ?? 0, 2),
            'total_co2_tonnes'  => round(($data->total_co2_kg ?? 0) / 1000, 4),
            'avg_co2_per_pax'   => round($data->avg_co2_per_pax ?? 0, 2),
            'total_fuel_kg'     => round($data->total_fuel_kg ?? 0, 2),
            'total_passengers'  => (int) ($data->total_passengers ?? 0),
            'avg_load_factor'   => round(($data->avg_load_factor ?? 0) * 100, 1),
        ];
    }

    private function buildChartData(array $filters): array
    {
        $baseQuery = $this->buildFlightQuery($filters);

        // 1. CO2 Trend by Date
        $trend = (clone $baseQuery)
            ->selectRaw('flight_date, SUM(co2_total_kg) as co2_kg, COUNT(*) as flights')
            ->groupBy('flight_date')
            ->orderBy('flight_date')
            ->get();

        // 2. CO2 by Route
        $byRoute = (clone $baseQuery)
            ->with(['departureAirport', 'arrivalAirport'])
            ->selectRaw('departure_airport_id, arrival_airport_id, SUM(co2_total_kg) as co2_kg, COUNT(*) as flights')
            ->groupBy('departure_airport_id', 'arrival_airport_id')
            ->orderByDesc('co2_kg')
            ->limit(8)
            ->get();

        // 3. CO2 by Aircraft Type
        $byAircraft = (clone $baseQuery)
            ->with('aircraft')
            ->selectRaw('aircraft_id, SUM(co2_total_kg) as co2_kg, COUNT(*) as flights')
            ->groupBy('aircraft_id')
            ->orderByDesc('co2_kg')
            ->limit(8)
            ->get();

        // 4. Avg CO2 per Passenger by Route
        $avgPerPax = (clone $baseQuery)
            ->with(['departureAirport', 'arrivalAirport'])
            ->selectRaw('departure_airport_id, arrival_airport_id, AVG(co2_per_passenger_kg) as avg_co2_pax')
            ->groupBy('departure_airport_id', 'arrival_airport_id')
            ->orderByDesc('avg_co2_pax')
            ->limit(8)
            ->get();

        return [
            'trend' => [
                'labels' => $trend->pluck('flight_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M Y')),
                'co2'    => $trend->pluck('co2_kg')->map(fn($v) => round($v, 2)),
                'flights'=> $trend->pluck('flights'),
            ],
            'byRoute' => [
                'labels' => $byRoute->map(fn($f) => ($f->departureAirport?->iata_code ?? '?') . '→' . ($f->arrivalAirport?->iata_code ?? '?')),
                'co2'    => $byRoute->pluck('co2_kg')->map(fn($v) => round($v, 2)),
            ],
            'byAircraft' => [
                'labels' => $byAircraft->map(fn($f) => $f->aircraft ? "{$f->aircraft->manufacturer} {$f->aircraft->model}" : 'Unknown'),
                'co2'    => $byAircraft->pluck('co2_kg')->map(fn($v) => round($v, 2)),
            ],
            'avgPerPax' => [
                'labels' => $avgPerPax->map(fn($f) => ($f->departureAirport?->iata_code ?? '?') . '→' . ($f->arrivalAirport?->iata_code ?? '?')),
                'values' => $avgPerPax->pluck('avg_co2_pax')->map(fn($v) => round($v, 2)),
            ],
        ];
    }
}
