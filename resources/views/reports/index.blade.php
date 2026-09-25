@extends('layouts.app')

@section('title', 'Laporan Emisi Karbon')
@section('page-title', 'Laporan & Rekapitulasi Emisi')
@section('page-subtitle', 'Ekspor Data Resmi Berdasarkan Metodologi ICAO / CORSIA')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    <!-- ====== UNIFIED SUMMARY STATS ====== -->
    <div class="card overflow-hidden shadow-xs">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-slate-200 dark:divide-slate-800">
            <!-- 1. Total Flights -->
            <div class="p-4 sm:p-5">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block mb-1">Total Penerbangan</span>
                <div class="text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">
                    {{ number_format($summary->total_flights ?? 0) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">penerbangan dalam filter</div>
            </div>

            <!-- 2. Total Fuel -->
            <div class="p-4 sm:p-5">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block mb-1">Total Bahan Bakar</span>
                <div class="text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">
                    {{ number_format(($summary->total_fuel_kg ?? 0) / 1000, 1) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">tonnes Jet-A1</div>
            </div>

            <!-- 3. Total CO2 (Tonnes) -->
            <div class="p-4 sm:p-5">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block mb-1">Total Emisi Karbon</span>
                <div class="text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">
                    {{ number_format(($summary->total_co2_kg ?? 0) / 1000, 2) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">tonnes CO₂ eq</div>
            </div>

            <!-- 4. Avg CO2 / Pax -->
            <div class="p-4 sm:p-5">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block mb-1">Rata-rata CO₂ / Pax</span>
                <div class="text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">
                    {{ number_format($summary->avg_co2_per_pax ?? 0, 2) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">kg CO₂ / penumpang</div>
            </div>

            <!-- 5. Total Passengers -->
            <div class="p-4 sm:p-5">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block mb-1">Total Penumpang</span>
                <div class="text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">
                    {{ number_format($summary->total_passengers ?? 0) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">estimasi penumpang</div>
            </div>
        </div>
    </div>

    <!-- ====== FILTER & EXPORT TOOLBAR ====== -->
    <div class="card p-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">

            <!-- Filter Controls Group -->
            <div class="flex flex-wrap items-end gap-3 flex-1">
                <!-- Period -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Periode</label>
                    <select name="period" onchange="toggleDateRange(this.value)"
                            class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                        @foreach(['today'=>'Hari Ini','yesterday'=>'Kemarin','this-week'=>'Minggu Ini','this-month'=>'Bulan Ini','this-quarter'=>'Kuartal Ini','this-year'=>'Tahun Ini','custom'=>'Rentang Kustom'] as $val => $label)
                            <option value="{{ $val }}" {{ ($filters['period'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Range -->
                <div id="date-range-wrapper" class="{{ ($filters['period'] ?? '') === 'custom' ? 'flex' : 'hidden' }} gap-2">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Dari</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                               class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Sampai</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                               class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                    </div>
                </div>

                <!-- Origin -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Bandara Asal</label>
                    <select name="origin"
                            class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                        <option value="">Semua Asal</option>
                        @foreach($airports as $ap)
                            <option value="{{ $ap->iata_code }}" {{ ($filters['origin'] ?? '') === $ap->iata_code ? 'selected' : '' }}>
                                {{ $ap->iata_code }} &mdash; {{ $ap->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Bandara Tujuan</label>
                    <select name="destination"
                            class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                        <option value="">Semua Tujuan</option>
                        @foreach($airports as $ap)
                            <option value="{{ $ap->iata_code }}" {{ ($filters['destination'] ?? '') === $ap->iata_code ? 'selected' : '' }}>
                                {{ $ap->iata_code }} &mdash; {{ $ap->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aircraft -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Pesawat</label>
                    <select name="aircraft"
                            class="px-2.5 py-1.5 text-xs sm:text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-600 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-600/30">
                        <option value="">Semua Pesawat</option>
                        @foreach($aircraft as $ac)
                            <option value="{{ $ac->id }}" {{ ($filters['aircraft'] ?? '') == $ac->id ? 'selected' : '' }}>
                                {{ $ac->manufacturer }} {{ $ac->model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">
                        Filter
                    </button>
                    @if(request()->hasAny(['period', 'origin', 'destination', 'aircraft', 'date_from', 'date_to']))
                    <a href="{{ route('reports.index') }}" class="btn-secondary">
                        Reset
                    </a>
                    @endif
                </div>
            </div>

            <!-- Export Button -->
            <div>
                <a href="{{ route('reports.export-excel', request()->query()) }}" class="btn-success">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Ekspor ke Excel</span>
                </a>
            </div>
        </form>
    </div>

    <!-- ====== REPORT DATA TABLE ====== -->
    <div class="card overflow-hidden">
        <div class="card-header">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-slate-100">
                Detail Log Emisi Penerbangan ({{ $flights->total() }} Data)
            </h3>
            <span class="text-xs text-slate-400">Formula ICAO Doc 9889</span>
        </div>

        @if($flights->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center px-4">
            <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-slate-500 text-xs">Tidak ada data emisi sesuai filter.</p>
        </div>
        @else

        {{-- ===== DESKTOP TABLE ===== --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Flight No.</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Tanggal</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Rute</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Pesawat</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">GCD</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Fuel</th>
                        <th class="px-4 py-2.5 text-center font-medium uppercase tracking-wider text-[11px]">Load Factor</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Total CO₂</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">CO₂/Pax</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Tonnes CO₂</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($flights as $f)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-mono font-medium text-xs text-blue-600 dark:text-blue-400">
                                {{ $f->flight_number }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $f->flight_date->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap font-medium text-slate-800 dark:text-slate-200">
                            <span>{{ $f->departureAirport?->iata_code ?? '?' }}</span>
                            <span class="text-slate-400 mx-1">&rarr;</span>
                            <span>{{ $f->arrivalAirport?->iata_code ?? '?' }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $f->aircraft?->manufacturer }} {{ $f->aircraft?->model }}
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums text-slate-600 dark:text-slate-300">
                            {{ number_format($f->distance_gcd_km, 1) }} km
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums text-slate-600 dark:text-slate-300">
                            {{ number_format($f->total_fuel_kg) }} kg
                        </td>
                        <td class="px-4 py-2.5 text-center whitespace-nowrap">
                            <span class="badge badge-neutral tabular-nums">
                                {{ number_format(($f->passenger_load_factor ?? 0) * 100, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-slate-800 dark:text-slate-200">
                            {{ number_format($f->co2_total_kg, 2) }} kg
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums font-medium text-blue-600 dark:text-blue-400">
                            {{ number_format($f->co2_per_passenger_kg, 2) }} kg
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-slate-900 dark:text-white">
                            {{ number_format($f->co2_total_kg / 1000, 3) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== TABLET TABLE (md screens) ===== --}}
        <div class="hidden md:block lg:hidden overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Flight No.</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Tanggal</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Rute</th>
                        <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Pesawat</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Total CO₂</th>
                        <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">CO₂/Pax</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($flights as $f)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-mono font-medium text-xs text-blue-600 dark:text-blue-400">{{ $f->flight_number }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 dark:text-slate-400">{{ $f->flight_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2.5 whitespace-nowrap font-medium text-slate-800 dark:text-slate-200">
                            {{ $f->departureAirport?->iata_code ?? '?' }} &rarr; {{ $f->arrivalAirport?->iata_code ?? '?' }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 dark:text-slate-400">{{ $f->aircraft?->manufacturer }} {{ $f->aircraft?->model }}</td>
                        <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-slate-800 dark:text-slate-200">{{ number_format($f->co2_total_kg, 2) }} kg</td>
                        <td class="px-4 py-2.5 text-right tabular-nums font-medium text-blue-600 dark:text-blue-400">{{ number_format($f->co2_per_passenger_kg, 2) }} kg</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE CARD LIST ===== --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800/80">
            @foreach($flights as $f)
            <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono font-semibold text-xs text-blue-600 dark:text-blue-400">{{ $f->flight_number }}</span>
                            <span class="text-[11px] text-slate-400">{{ $f->flight_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="font-semibold text-sm text-slate-800 dark:text-slate-200">
                            {{ $f->departureAirport?->iata_code ?? '?' }}
                            <span class="text-slate-400 mx-1">&rarr;</span>
                            {{ $f->arrivalAirport?->iata_code ?? '?' }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $f->aircraft?->manufacturer }} {{ $f->aircraft?->model }}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-semibold text-sm text-slate-800 dark:text-slate-200 tabular-nums">{{ number_format($f->co2_total_kg, 2) }} kg</div>
                        <div class="text-[11px] text-slate-400">total CO₂</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium tabular-nums mt-0.5">{{ number_format($f->co2_per_passenger_kg, 2) }} kg/pax</div>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center gap-4 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>LF: {{ number_format(($f->passenger_load_factor ?? 0) * 100, 1) }}%</span>
                    <span>Fuel: {{ number_format($f->total_fuel_kg) }} kg</span>
                    <span>{{ number_format($f->distance_gcd_km, 1) }} km</span>
                    <span class="ml-auto font-mono font-semibold text-slate-600 dark:text-slate-300">{{ number_format($f->co2_total_kg / 1000, 3) }} t</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($flights->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
            {{ $flights->links() }}
        </div>
        @endif
        @endif
    </div>

</div>

<script>
function toggleDateRange(val) {
    const wrapper = document.getElementById('date-range-wrapper');
    if (val === 'custom') {
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
    } else {
        wrapper.classList.add('hidden');
        wrapper.classList.remove('flex');
    }
}
</script>
@endsection
