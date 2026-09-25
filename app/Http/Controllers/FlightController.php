<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Aircraft;
use App\Models\Flight;
use App\Http\Requests\StoreFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Services\CarbonEmissionCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FlightController extends Controller
{
    public function __construct(private CarbonEmissionCalculator $calculator) {}

    public function index(Request $request): View
    {
        $query = Flight::with(['departureAirport', 'arrivalAirport', 'aircraft']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $query->byPeriod($request->get('period'))
              ->byDeparture($request->get('origin'))
              ->byArrival($request->get('destination'))
              ->byAircraft($request->integer('aircraft') ?: null);

        $flights  = $query->orderByDesc('flight_date')->paginate(15)->withQueryString();
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name']);
        $aircraft = Aircraft::active()->orderBy('manufacturer')->get(['id', 'manufacturer', 'model', 'icao_type']);

        return view('flights.index', compact('flights', 'airports', 'aircraft'));
    }

    public function create(): View
    {
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name', 'city', 'latitude', 'longitude']);
        $aircraft = Aircraft::active()->orderBy('manufacturer')->get(['id', 'manufacturer', 'model', 'icao_type', 'y_seats', 'passenger_to_freight_factor', 'co2_factor']);

        return view('flights.create', compact('airports', 'aircraft'));
    }

    public function store(StoreFlightRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, &$flight) {
            // Calculate carbon emission
            $departure = Airport::findOrFail($data['departure_airport_id']);
            $arrival   = Airport::findOrFail($data['arrival_airport_id']);
            $aircraft  = Aircraft::findOrFail($data['aircraft_id']);

            $calc = $this->calculator->calculateFromAirports(
                $departure, $arrival,
                $data['total_fuel_kg'],
                $data['passenger_to_freight_factor'] ?? $aircraft->passenger_to_freight_factor,
                $data['y_seats'] ?? $aircraft->y_seats,
                $data['passenger_load_factor'],
                $data['co2_factor'] ?? $aircraft->co2_factor
            );

            $flight = Flight::create(array_merge($data, [
                'distance_gcd_km'      => $calc['distance_gcd_km'],
                'distance_adjusted_km' => $calc['distance_adjusted_km'],
                'passenger_count'      => $calc['passenger_count'],
                'co2_total_kg'         => $calc['co2_total_kg'],
                'co2_per_passenger_kg' => $calc['co2_per_passenger_kg'],
            ]));
        });

        return redirect()->route('flights.show', $flight)->with('success', 'Data penerbangan berhasil ditambahkan.');
    }

    public function show(Flight $flight): View
    {
        $flight->load(['departureAirport', 'arrivalAirport', 'aircraft']);
        return view('flights.show', compact('flight'));
    }

    public function edit(Flight $flight): View
    {
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name', 'city', 'latitude', 'longitude']);
        $aircraft = Aircraft::active()->orderBy('manufacturer')->get(['id', 'manufacturer', 'model', 'icao_type', 'y_seats', 'passenger_to_freight_factor', 'co2_factor']);
        $flight->load(['departureAirport', 'arrivalAirport', 'aircraft']);

        return view('flights.edit', compact('flight', 'airports', 'aircraft'));
    }

    public function update(UpdateFlightRequest $request, Flight $flight): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $flight) {
            $departure = Airport::findOrFail($data['departure_airport_id']);
            $arrival   = Airport::findOrFail($data['arrival_airport_id']);
            $aircraft  = Aircraft::findOrFail($data['aircraft_id']);

            $calc = $this->calculator->calculateFromAirports(
                $departure, $arrival,
                $data['total_fuel_kg'],
                $data['passenger_to_freight_factor'] ?? $aircraft->passenger_to_freight_factor,
                $data['y_seats'] ?? $aircraft->y_seats,
                $data['passenger_load_factor'],
                $data['co2_factor'] ?? $aircraft->co2_factor
            );

            $flight->update(array_merge($data, [
                'distance_gcd_km'      => $calc['distance_gcd_km'],
                'distance_adjusted_km' => $calc['distance_adjusted_km'],
                'passenger_count'      => $calc['passenger_count'],
                'co2_total_kg'         => $calc['co2_total_kg'],
                'co2_per_passenger_kg' => $calc['co2_per_passenger_kg'],
            ]));
        });

        return redirect()->route('flights.show', $flight)->with('success', 'Data penerbangan berhasil diperbarui.');
    }

    public function destroy(Flight $flight): RedirectResponse
    {
        $flight->delete();
        return redirect()->route('flights.index')->with('success', 'Data penerbangan berhasil dihapus.');
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Flight::with(['departureAirport', 'arrivalAirport', 'aircraft'])
            ->byPeriod($request->get('period'))
            ->byDeparture($request->get('origin'))
            ->byArrival($request->get('destination'))
            ->orderByDesc('flight_date');

        $flights = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Flights');

        $headers = ['Flight No.', 'Tanggal', 'Asal', 'Tujuan', 'Pesawat', 'GCD (km)', 'Adjusted (km)', 'Fuel (kg)', 'Pax Factor', 'Y-Seats', 'Load Factor', 'Pax Count', 'CO2 Factor', 'Total CO2 (kg)', 'CO2/Pax (kg)'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ]);

        foreach ($flights as $i => $f) {
            $row = $i + 2;
            $sheet->fromArray([[
                $f->flight_number,
                $f->flight_date->format('Y-m-d'),
                $f->departureAirport?->iata_code,
                $f->arrivalAirport?->iata_code,
                $f->aircraft ? "{$f->aircraft->manufacturer} {$f->aircraft->model}" : '',
                $f->distance_gcd_km,
                $f->distance_adjusted_km,
                $f->total_fuel_kg,
                $f->passenger_to_freight_factor,
                $f->y_seats,
                $f->passenger_load_factor,
                $f->passenger_count,
                $f->co2_factor,
                $f->co2_total_kg,
                $f->co2_per_passenger_kg,
            ]], null, "A{$row}");
        }

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'flights_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls|max:10240']);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            array_shift($rows);

            $success = 0; $failed = 0; $errors = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue;
                $rowNum = $index + 2;

                [$flightNum, $date, $depIata, $arrIata, $aircraftIcao, , , $totalFuel, $paxFactor, $ySeats, $loadFactor] = array_pad($row, 11, null);

                $departure = Airport::where('iata_code', $depIata)->first();
                $arrival   = Airport::where('iata_code', $arrIata)->first();
                $aircraft  = Aircraft::where('icao_type', $aircraftIcao)->first();

                if (!$departure || !$arrival || !$aircraft) {
                    $errors[] = "Baris {$rowNum}: Airport atau aircraft tidak ditemukan ({$depIata}, {$arrIata}, {$aircraftIcao}).";
                    $failed++; continue;
                }

                try {
                    $calc = $this->calculator->calculateFromAirports(
                        $departure, $arrival,
                        (float) $totalFuel,
                        (float) ($paxFactor ?? $aircraft->passenger_to_freight_factor),
                        (int) ($ySeats ?? $aircraft->y_seats),
                        (float) ($loadFactor ?? 0.8),
                        $aircraft->co2_factor
                    );

                    Flight::updateOrCreate(
                        ['flight_number' => $flightNum, 'flight_date' => $date],
                        [
                            'departure_airport_id' => $departure->id,
                            'arrival_airport_id'   => $arrival->id,
                            'aircraft_id'          => $aircraft->id,
                            'total_fuel_kg'        => $totalFuel,
                            'passenger_to_freight_factor' => $paxFactor ?? $aircraft->passenger_to_freight_factor,
                            'y_seats'              => $ySeats ?? $aircraft->y_seats,
                            'passenger_load_factor'=> $loadFactor ?? 0.8,
                            'co2_factor'           => $aircraft->co2_factor,
                        ] + $calc
                    );
                    $success++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNum}: " . $e->getMessage(); $failed++;
                }
            }

            if (!empty($errors)) session()->flash('import_errors', $errors);
            return back()->with('success', "Import selesai: {$success} berhasil, {$failed} gagal.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }
}
