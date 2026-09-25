@extends('layouts.app')

@section('title', 'Penerbangan')
@section('page-title', 'Data Penerbangan')
@section('page-subtitle', 'Flight Monitoring')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: true }"
     x-init="setTimeout(() => loading = false, 600)">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-48 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
                <div class="skeleton h-9 w-20 rounded-lg"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                @foreach([120, 90, 160, 120, 90, 90, 80, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 8; $i++)
            <div class="flex gap-4 items-center px-4 py-4 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
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
                   class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E] focus:border-transparent w-48">
            <select name="period" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                <option value="">Semua Periode</option>
                @foreach(['today'=>'Hari Ini','this-week'=>'Minggu Ini','this-month'=>'Bulan Ini','this-year'=>'Tahun Ini'] as $v => $l)
                    <option value="{{ $v }}" {{ request('period') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82] transition-colors">Cari</button>
            <a href="{{ route('flights.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Reset</a>
        </form>

        <div class="flex gap-2">
            <!-- Import -->
            <div x-data="{ open: false }">
                <button @click="open = true" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
                <div x-show="open" @click.away="open = false"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl mx-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4">Import Data Penerbangan</h3>
                        <form method="POST" action="{{ route('flights.import-excel') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0B5A9E] file:text-white hover:file:bg-[#084a82] mb-4">
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">Upload & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <a href="{{ route('flights.export-excel', request()->query()) }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel
            </a>

            <a href="{{ route('flights.create') }}"
               class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Penerbangan
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        @if($flights->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-14 h-14 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data penerbangan ditemukan.</p>
            <a href="{{ route('flights.create') }}" class="mt-4 px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">Tambah Penerbangan Pertama</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penerbangan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rute</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pesawat</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">GCD (km)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fuel (kg)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CO₂ Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CO₂/Pax</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($flights as $flight)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3">
                            <a href="{{ route('flights.show', $flight) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#0B5A9E]/10 text-[#0B5A9E] dark:bg-[#0B5A9E]/20 dark:text-blue-300 hover:bg-[#0B5A9E]/20">
                                {{ $flight->flight_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $flight->flight_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $flight->departureAirport?->iata_code }} <span class="text-[#0B5A9E]">→</span> {{ $flight->arrivalAirport?->iata_code }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">{{ $flight->aircraft?->manufacturer }} {{ $flight->aircraft?->model }}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-300">{{ number_format($flight->distance_gcd_km, 1) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-300">{{ number_format($flight->total_fuel_kg) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-slate-800 dark:text-slate-200">{{ number_format($flight->co2_total_kg, 2) }} kg</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-[#0B5A9E]">{{ number_format($flight->co2_per_passenger_kg, 2) }} kg</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('flights.show', $flight) }}" class="p-1.5 text-slate-400 hover:text-[#0B5A9E] hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('flights.edit', $flight) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('flights.destroy', $flight) }}"
                                      onsubmit="return confirm('Hapus penerbangan {{ $flight->flight_number }}? Tindakan ini tidak dapat dibatalkan.')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-[#D22228] hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
            {{ $flights->links() }}
        </div>
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
