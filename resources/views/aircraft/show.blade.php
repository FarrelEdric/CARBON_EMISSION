@extends('layouts.app')

@section('title', 'Detail Pesawat — ' . $aircraft->manufacturer . ' ' . $aircraft->model)
@section('page-title', 'Detail Pesawat')
@section('page-subtitle', $aircraft->manufacturer . ' ' . $aircraft->model)

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $aircraft->manufacturer }} {{ $aircraft->model }}</span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-[#0B5A9E]/10 dark:bg-[#0B5A9E]/25 text-[#0B5A9E] dark:text-sky-300">
                    ICAO: {{ $aircraft->icao_type ?? '-' }}
                </span>
                @if($aircraft->status)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                        Aktif
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                        Nonaktif
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sumber Data: {{ $aircraft->data_source ?? 'ICAO Methodology' }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('aircraft.edit', $aircraft) }}" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Edit Pesawat
            </a>
            <a href="{{ route('aircraft.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                &larr; Daftar Pesawat
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Specs -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Spesifikasi Armada</h3>
            <div>
                <span class="text-xs text-slate-400 block">Pabrikan</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aircraft->manufacturer }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Model</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aircraft->model }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Tipe IATA</span>
                <span class="text-sm font-mono text-slate-800 dark:text-slate-100">{{ $aircraft->iata_type ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Equivalent Aircraft</span>
                <span class="text-sm font-mono text-slate-800 dark:text-slate-100">{{ $aircraft->equivalent_aircraft ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Kapasitas Kursi (Y-Seats)</span>
                <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($aircraft->y_seats) }} kursi</span>
            </div>
        </div>

        <!-- ICAO Calculation Factors -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Faktor Emisi & Konsumsi (ICAO)</h3>
            <div>
                <span class="text-xs text-slate-400 block">Fuel Burn Factor</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ $aircraft->fuel_burn_factor ? number_format($aircraft->fuel_burn_factor, 4) . ' kg/km' : 'Berdasarkan Tabel Jarak' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Passenger to Freight Factor</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ number_format($aircraft->passenger_to_freight_factor ?? 0.85, 2) }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Faktor Emisi CO₂</span>
                <span class="text-sm font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($aircraft->co2_factor ?? 3.16, 2) }} kg CO₂ / kg Fuel</span>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                <span class="text-xs text-slate-400 block">Riwayat Penerbangan</span>
                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $aircraft->flights->count() }} penerbangan tercatat</span>
            </div>
        </div>

        <!-- Notes / Formula -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Metodologi & Catatan</h3>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                {{ $aircraft->notes ?? 'Armada ini menggunakan perhitungan konsumsi bahan bakar ICAO Fuel Consumption Curve berdasarkan parameter jarak Great Circle Distance (GCD) dan koreksi rute operasional.' }}
            </p>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300">
                CO₂ = Total Fuel × 3.16<br>
                Pax CO₂ = CO₂ × 0.85 / (Y-Seats × LF)
            </div>
        </div>
    </div>

</div>
@endsection
