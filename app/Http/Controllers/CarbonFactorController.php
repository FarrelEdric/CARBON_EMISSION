<?php

namespace App\Http\Controllers;

use App\Models\CarbonFactor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CarbonFactorController extends Controller
{
    public function index(Request $request): View
    {
        $query = CarbonFactor::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('factor_key', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        if ($request->get('status') !== null && $request->get('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $factors = $query->orderBy('factor_key')->paginate(15)->withQueryString();
        return view('carbon-factors.index', compact('factors'));
    }

    public function create(): View
    {
        return view('carbon-factors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'factor_key'     => 'required|string|max:50|unique:carbon_factors,factor_key',
            'factor_value'   => 'required|numeric|min:0',
            'unit'           => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'source'         => 'nullable|string|max:200',
            'effective_date' => 'nullable|date',
            'is_active'      => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        CarbonFactor::create($validated);
        return redirect()->route('carbon-factors.index')->with('success', 'Faktor karbon berhasil ditambahkan.');
    }

    public function show(CarbonFactor $carbonFactor): View
    {
        return view('carbon-factors.show', compact('carbonFactor'));
    }

    public function edit(CarbonFactor $carbonFactor): View
    {
        return view('carbon-factors.edit', compact('carbonFactor'));
    }

    public function update(Request $request, CarbonFactor $carbonFactor): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'factor_key'     => 'required|string|max:50|unique:carbon_factors,factor_key,' . $carbonFactor->id,
            'factor_value'   => 'required|numeric|min:0',
            'unit'           => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'source'         => 'nullable|string|max:200',
            'effective_date' => 'nullable|date',
            'is_active'      => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $carbonFactor->update($validated);
        return redirect()->route('carbon-factors.index')->with('success', 'Faktor karbon berhasil diperbarui.');
    }

    public function destroy(CarbonFactor $carbonFactor): RedirectResponse
    {
        $carbonFactor->delete();
        return redirect()->route('carbon-factors.index')->with('success', 'Faktor karbon berhasil dihapus.');
    }

    public function exportExcel(): StreamedResponse
    {
        $factors = CarbonFactor::orderBy('factor_key')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Carbon Factors');

        $headers = ['ID', 'Nama Parameter', 'Unique Key', 'Nilai Koefisien', 'Satuan Unit', 'Sumber Acuan', 'Tanggal Efektif', 'Status', 'Deskripsi'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B5A9E']],
        ]);

        foreach ($factors as $i => $factor) {
            $row = $i + 2;
            $sheet->fromArray([[
                $factor->id,
                $factor->name,
                $factor->factor_key,
                $factor->factor_value,
                $factor->unit,
                $factor->source,
                $factor->effective_date ? $factor->effective_date->format('Y-m-d') : '-',
                $factor->is_active ? 'Aktif' : 'Nonaktif',
                $factor->description,
            ]], null, "A{$row}");
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'carbon_factors_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
