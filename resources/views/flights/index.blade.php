@extends('layouts.app')

@section('title', 'Penerbangan')
@section('page-title', 'Data Penerbangan')
@section('page-subtitle', 'Flight Monitoring')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: false }">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-48 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
                <div class="skeleton h-9 w-20 rounded-md"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                @foreach([120, 90, 160, 120, 90, 90, 80, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 8; $i++)
            <div class="flex gap-4 items-center px-4 py-3 border-b border-slate-100 dark:border-slate-800/60 last:border-0">
                <div class="flex flex-col gap-1" style="width:120px; flex-shrink:0">
                    <div class="skeleton h-4 w-20 rounded"></div>
                    <div class="skeleton h-3 w-14 rounded"></div>
                </div>
                <div class="skeleton h-4 w-24 rounded" style="flex-shrink:0"></div>
                <div class="flex flex-col gap-1" style="width:160px; flex-shrink:0">
                    <div class="skeleton h-4 w-28 rounded"></div>
                    <div class="skeleton h-3 w-20 rounded"></div>
                </div>
                <div class="skeleton h-4 w-24 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-20 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-20 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-6 w-16 rounded-full" style="flex-shrink:0"></div>
                <div class="flex gap-1" style="flex-shrink:0">
                    <div class="skeleton h-6 w-10 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="ace-content-ready space-y-4">

    <!-- Filter & Actions Bar -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('flights.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor penerbangan..."
                   class="form-input-base w-52">
            <select name="period" class="form-select-base">
                <option value="">Semua Periode</option>
                @foreach(['today'=>'Hari Ini','this-week'=>'Minggu Ini','this-month'=>'Bulan Ini','this-year'=>'Tahun Ini'] as $v => $l)
                    <option value="{{ $v }}" {{ request('period') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request()->hasAny(['search', 'period']))
            <a href="{{ route('flights.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Reset</a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Import -->
            <div x-data="{ open: false }">
                <button @click="open = true" class="btn-secondary">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Excel</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 w-full max-w-md shadow-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-1.5">Import Data Penerbangan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unggah file format .xlsx atau .xls dengan kolom data penerbangan resmi.</p>
                        <form method="POST" action="{{ route('flights.import-excel') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#0B5A9E] file:text-white hover:file:bg-[#084a82] mb-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Upload & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <a href="{{ route('flights.export-excel', request()->query()) }}" class="btn-success">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>

            <a href="{{ route('flights.create') }}" class="btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Penerbangan</span>
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        @if($flights->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium">Tidak ada data penerbangan ditemukan.</p>
            <a href="{{ route('flights.create') }}" class="mt-2 text-xs text-[#0B5A9E] hover:underline">Tambah Penerbangan Pertama</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Penerbangan</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Tanggal</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Rute</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Pesawat</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">GCD (km)</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Fuel (kg)</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">CO₂ Total</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">CO₂/Pax</th>
                        <th class="px-4 py-2.5 text-center font-medium uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($flights as $flight)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <a href="{{ route('flights.show', $flight) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium text-[#0B5A9E] dark:text-sky-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 hover:bg-blue-100">
                                {{ $flight->flight_number }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $flight->flight_date->format('d M Y') }}</td>
                        <td class="px-4 py-2.5 font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                            {{ $flight->departureAirport?->iata_code }} <span class="text-slate-400">→</span> {{ $flight->arrivalAirport?->iata_code }}
                        </td>
                        <td class="px-4 py-2.5 text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $flight->aircraft?->manufacturer }} {{ $flight->aircraft?->model }}</td>
                        <td class="px-4 py-2.5 text-right font-mono tabular-nums text-slate-700 dark:text-slate-300">{{ number_format($flight->distance_gcd_km, 1) }}</td>
                        <td class="px-4 py-2.5 text-right font-mono tabular-nums text-slate-700 dark:text-slate-300">{{ number_format($flight->total_fuel_kg) }}</td>
                        <td class="px-4 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-800 dark:text-slate-200">{{ number_format($flight->co2_total_kg, 2) }} kg</td>
                        <td class="px-4 py-2.5 text-right font-mono tabular-nums font-semibold text-[#0B5A9E] dark:text-sky-400">{{ number_format($flight->co2_per_passenger_kg, 2) }} kg</td>
                        <td class="px-4 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('flights.show', $flight) }}" class="p-1 text-slate-400 hover:text-[#0B5A9E] hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors" title="Detail">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('flights.edit', $flight) }}" class="p-1 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('flights.destroy', $flight) }}"
                                      onsubmit="return confirm('Hapus penerbangan {{ $flight->flight_number }}? Tindakan ini tidak dapat dibatalkan.')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded transition-colors" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($flights->hasPages())
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
            {{ $flights->links() }}
        </div>
        @endif
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
