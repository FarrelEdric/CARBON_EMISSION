<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Http\Requests\StoreAirportRequest;
use App\Http\Requests\UpdateAirportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AirportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Airport::query();

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($request->get('status') !== null && $request->get('status') !== '') {
            $query->where('status', $request->boolean('status'));
        }

        $airports = $query->orderBy('iata_code')->paginate(15)->withQueryString();

        return view('airports.index', compact('airports'));
    }

    public function create(): View
    {
        return view('airports.create');
    }

    public function store(StoreAirportRequest $request): RedirectResponse
    {
        Airport::create($request->validated());
        return redirect()->route('airports.index')->with('success', 'Bandara berhasil ditambahkan.');
    }

    public function show(Airport $airport): View
    {
        $airport->load(['departureFlights.arrivalAirport', 'arrivalFlights.departureAirport']);
        return view('airports.show', compact('airport'));
    }

    public function edit(Airport $airport): View
    {
        return view('airports.edit', compact('airport'));
    }

    public function update(UpdateAirportRequest $request, Airport $airport): RedirectResponse
    {
        $airport->update($request->validated());
        return redirect()->route('airports.index')->with('success', 'Bandara berhasil diperbarui.');
    }

    public function destroy(Airport $airport): RedirectResponse
    {
        if ($airport->departureFlights()->count() > 0 || $airport->arrivalFlights()->count() > 0) {
            return back()->with('error', 'Bandara tidak dapat dihapus karena memiliki data penerbangan.');
        }
        $airport->delete();
        return redirect()->route('airports.index')->with('success', 'Bandara berhasil dihapus.');
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $airports = Airport::active()
            ->search($search)
            ->orderBy('iata_code')
            ->limit(20)
            ->get(['id', 'iata_code', 'icao_code', 'name', 'city', 'latitude', 'longitude']);

        return response()->json($airports->map(fn($a) => [
            'id'         => $a->id,
            'iata_code'  => $a->iata_code,
            'icao_code'  => $a->icao_code,
            'name'       => $a->name,
            'city'       => $a->city,
            'display'    => $a->display_name,
            'lat'        => $a->latitude,
            'lng'        => $a->longitude,
        ]));
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $airports = Airport::orderBy('iata_code')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Airports');

        // Header
        $headers = ['ID', 'IATA Code', 'ICAO Code', 'Name', 'City', 'Province', 'Country', 'Latitude', 'Longitude', 'Elevation', 'Status'];
        $sheet->fromArray([$headers], null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Data
        foreach ($airports as $i => $airport) {
            $row = $i + 2;
            $sheet->fromArray([[
                $airport->id,
                $airport->iata_code,
                $airport->icao_code,
                $airport->name,
                $airport->city,
                $airport->province,
                $airport->country,
                $airport->latitude,
                $airport->longitude,
                $airport->elevation,
                $airport->status ? 'Aktif' : 'Nonaktif',
            ]], null, "A{$row}");
        }

        // Auto-width
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'airports_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row
            array_shift($rows);

            $success = 0;
            $failed  = 0;
            $errors  = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                if (empty(array_filter($row))) continue;

                [$iata, $icao, $name, $city, $province, $country, $lat, $lng, $elevation] = array_pad($row, 9, null);

                if (empty($name)) {
                    $errors[] = "Baris {$rowNum}: Nama bandara wajib diisi.";
                    $failed++;
                    continue;
                }

                try {
                    Airport::updateOrCreate(
                        ['iata_code' => $iata],
                        [
                            'icao_code' => $icao,
                            'name'      => $name,
                            'city'      => $city,
                            'province'  => $province,
                            'country'   => $country ?: 'ID',
                            'latitude'  => $lat,
                            'longitude' => $lng,
                            'elevation' => $elevation,
                            'status'    => true,
                        ]
                    );
                    $success++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNum}: " . $e->getMessage();
                    $failed++;
                }
            }

            $message = "Import selesai: {$success} berhasil, {$failed} gagal.";
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }
}
