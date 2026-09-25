<?php

namespace App\Http\Controllers;

use App\Models\OperationalRoute;
use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OperationalRouteController extends Controller
{
    public function index(Request $request): View
    {
        $query = OperationalRoute::with(['departureAirport', 'arrivalAirport']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('route_name', 'like', "%{$search}%")
                  ->orWhereHas('departureAirport', function ($ap) use ($search) {
                      $ap->where('iata_code', 'like', "%{$search}%")
                         ->orWhere('icao_code', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%");
                  })
                  ->orWhereHas('arrivalAirport', function ($ap) use ($search) {
                      $ap->where('iata_code', 'like', "%{$search}%")
                         ->orWhere('icao_code', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%");
                  });
            });
        }

        if ($source = $request->get('source')) {
            $query->where('source', $source);
        }

        if ($request->get('status') !== null && $request->get('status') !== '') {
            $query->where('status', $request->boolean('status'));
        }

        $routes = $query->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('operational-routes.index', compact('routes'));
    }

    public function create(): View
    {
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name']);
        return view('operational-routes.create', compact('airports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id'   => 'required|exists:airports,id|different:departure_airport_id',
            'route_name'           => 'nullable|string|max:100',
            'waypoints'            => 'nullable|json',
            'distance_km'          => 'nullable|numeric|min:0',
            'source'               => 'required|in:demo,official,imported',
            'status'               => 'nullable|boolean',
        ]);
        $validated['status'] = $request->boolean('status');

        if (isset($validated['waypoints'])) {
            $validated['waypoints'] = json_decode($validated['waypoints'], true);
        }

        OperationalRoute::create($validated);
        return redirect()->route('operational-routes.index')->with('success', 'Rute operasional berhasil ditambahkan.');
    }

    public function show(OperationalRoute $operationalRoute): View
    {
        $operationalRoute->load(['departureAirport', 'arrivalAirport']);
        return view('operational-routes.show', compact('operationalRoute'));
    }

    public function edit(OperationalRoute $operationalRoute): View
    {
        $airports = Airport::active()->orderBy('iata_code')->get(['id', 'iata_code', 'name']);
        return view('operational-routes.edit', compact('operationalRoute', 'airports'));
    }

    public function update(Request $request, OperationalRoute $operationalRoute): RedirectResponse
    {
        $validated = $request->validate([
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id'   => 'required|exists:airports,id|different:departure_airport_id',
            'route_name'           => 'nullable|string|max:100',
            'waypoints'            => 'nullable|json',
            'distance_km'          => 'nullable|numeric|min:0',
            'source'               => 'required|in:demo,official,imported',
            'status'               => 'nullable|boolean',
        ]);
        $validated['status'] = $request->boolean('status');

        if (isset($validated['waypoints'])) {
            $validated['waypoints'] = json_decode($validated['waypoints'], true);
        }

        $operationalRoute->update($validated);
        return redirect()->route('operational-routes.index')->with('success', 'Rute operasional berhasil diperbarui.');
    }

    public function destroy(OperationalRoute $operationalRoute): RedirectResponse
    {
        $operationalRoute->delete();
        return redirect()->route('operational-routes.index')->with('success', 'Rute operasional berhasil dihapus.');
    }

    public function exportExcel(): StreamedResponse
    {
        $routes = OperationalRoute::with(['departureAirport', 'arrivalAirport'])->orderBy('id')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Operational Routes');

        $headers = ['ID', 'Asal (IATA)', 'Bandara Asal', 'Tujuan (IATA)', 'Bandara Tujuan', 'Nama Rute', 'Jarak Operasional (km)', 'Sumber Data', 'Status'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ]);

        foreach ($routes as $i => $route) {
            $row = $i + 2;
            $sheet->fromArray([[
                $route->id,
                $route->departureAirport?->iata_code,
                $route->departureAirport?->name,
                $route->arrivalAirport?->iata_code,
                $route->arrivalAirport?->name,
                $route->route_name ?? 'Standar',
                $route->distance_km,
                $route->source_label,
                $route->status ? 'Aktif' : 'Nonaktif',
            ]], null, "A{$row}");
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'operational_routes_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
