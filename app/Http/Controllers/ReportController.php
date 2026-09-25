<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Airport;
use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'period'     => $request->get('period', 'this-month'),
            'date_from'  => $request->get('date_from'),
            'date_to'    => $request->get('date_to'),
            'origin'     => $request->get('origin'),
            'destination'=> $request->get('destination'),
            'aircraft'   => $request->get('aircraft'),
        ];

        $query = Flight::with(['departureAirport', 'arrivalAirport', 'aircraft'])
            ->byPeriod($filters['period'], $filters['date_from'], $filters['date_to'])
            ->byDeparture($filters['origin'])
            ->byArrival($filters['destination'])
            ->byAircraft($filters['aircraft'] ? (int) $filters['aircraft'] : null);

        $summary = (clone $query)->selectRaw('
            COUNT(*) as total_flights,
            COALESCE(SUM(total_fuel_kg), 0) as total_fuel_kg,
            COALESCE(SUM(co2_total_kg), 0) as total_co2_kg,
            COALESCE(AVG(co2_per_passenger_kg), 0) as avg_co2_per_pax,
            COALESCE(SUM(passenger_count), 0) as total_passengers
        ')->first();

        $flights  = (clone $query)->orderByDesc('flight_date')->paginate(20)->withQueryString();
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name']);
        $aircraft = Aircraft::active()->orderBy('manufacturer')->get(['id', 'manufacturer', 'model']);

        return view('reports.index', compact('flights', 'summary', 'airports', 'aircraft', 'filters'));
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = [
            'period'     => $request->get('period', 'this-month'),
            'date_from'  => $request->get('date_from'),
            'date_to'    => $request->get('date_to'),
            'origin'     => $request->get('origin'),
            'destination'=> $request->get('destination'),
            'aircraft'   => $request->get('aircraft'),
        ];

        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'aircraft'])
            ->byPeriod($filters['period'], $filters['date_from'], $filters['date_to'])
            ->byDeparture($filters['origin'])
            ->byArrival($filters['destination'])
            ->byAircraft($filters['aircraft'] ? (int) $filters['aircraft'] : null)
            ->orderByDesc('flight_date')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Carbon Emission Report');

        // Title
        $sheet->setCellValue('A1', 'ACE — AIRNAV CARBON EMMISION Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Header
        $headers = ['Flight No.', 'Tanggal', 'Asal', 'Tujuan', 'Pesawat', 'GCD (km)', 'Adj. Distance (km)', 'Fuel (kg)', 'Pax Load Factor', 'Pax Count', 'Total CO2 (kg)', 'CO2/Pax (kg)', 'Total CO2 (tonnes)'];
        $sheet->fromArray([$headers], null, 'A4');
        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ]);

        foreach ($flights as $i => $f) {
            $row = $i + 5;
            $sheet->fromArray([[
                $f->flight_number,
                $f->flight_date->format('Y-m-d'),
                $f->departureAirport?->iata_code,
                $f->arrivalAirport?->iata_code,
                $f->aircraft ? "{$f->aircraft->manufacturer} {$f->aircraft->model}" : '',
                $f->distance_gcd_km,
                $f->distance_adjusted_km,
                $f->total_fuel_kg,
                number_format($f->passenger_load_factor * 100, 1) . '%',
                $f->passenger_count,
                $f->co2_total_kg,
                $f->co2_per_passenger_kg,
                round($f->co2_total_kg / 1000, 4),
            ]], null, "A{$row}");
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'carbon_report_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
