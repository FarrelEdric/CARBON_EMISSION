<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Http\Requests\StoreAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AircraftController extends Controller
{
    public function index(Request $request): View
    {
        $query = Aircraft::query();

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $aircrafts = $query->orderBy('manufacturer')->paginate(15)->withQueryString();

        return view('aircraft.index', compact('aircrafts'));
    }

    public function create(): View
    {
        return view('aircraft.create');
    }

    public function store(StoreAircraftRequest $request): RedirectResponse
    {
        Aircraft::create($request->validated());
        return redirect()->route('aircraft.index')->with('success', 'Pesawat berhasil ditambahkan.');
    }

    public function show(Aircraft $aircraft): View
    {
        $aircraft->load('flights');
        return view('aircraft.show', compact('aircraft'));
    }

    public function edit(Aircraft $aircraft): View
    {
        return view('aircraft.edit', compact('aircraft'));
    }

    public function update(UpdateAircraftRequest $request, Aircraft $aircraft): RedirectResponse
    {
        $aircraft->update($request->validated());
        return redirect()->route('aircraft.index')->with('success', 'Data pesawat berhasil diperbarui.');
    }

    public function destroy(Aircraft $aircraft): RedirectResponse
    {
        if ($aircraft->flights()->count() > 0) {
            return back()->with('error', 'Pesawat tidak dapat dihapus karena memiliki data penerbangan.');
        }
        $aircraft->delete();
        return redirect()->route('aircraft.index')->with('success', 'Pesawat berhasil dihapus.');
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $aircrafts = Aircraft::orderBy('manufacturer')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aircraft');

        $headers = ['ID', 'Manufacturer', 'Model', 'ICAO Type', 'IATA Type', 'Y-Seats', 'Fuel Burn Factor', 'Pax/Freight Factor', 'CO2 Factor', 'Data Source', 'Status'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ]);

        foreach ($aircrafts as $i => $aircraft) {
            $row = $i + 2;
            $sheet->fromArray([[
                $aircraft->id,
                $aircraft->manufacturer,
                $aircraft->model,
                $aircraft->icao_type,
                $aircraft->iata_type,
                $aircraft->y_seats,
                $aircraft->fuel_burn_factor,
                $aircraft->passenger_to_freight_factor,
                $aircraft->co2_factor,
                $aircraft->data_source,
                $aircraft->status ? 'Aktif' : 'Nonaktif',
            ]], null, "A{$row}");
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'aircraft_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls|max:10240']);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            array_shift($rows); // Remove header

            $success = 0; $failed = 0; $errors = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue;
                $rowNum = $index + 2;

                [$manufacturer, $model, $icaoType, $iataType, $ySeats, $fuelBurn, $paxFactor, $co2Factor] = array_pad($row, 8, null);

                if (empty($manufacturer) || empty($model)) {
                    $errors[] = "Baris {$rowNum}: Manufacturer dan model wajib diisi.";
                    $failed++; continue;
                }

                try {
                    Aircraft::updateOrCreate(
                        ['icao_type' => $icaoType],
                        compact('manufacturer', 'model', 'iataType', 'ySeats', 'fuelBurn', 'paxFactor', 'co2Factor') + ['data_source' => 'imported', 'status' => true]
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
