@extends('layouts.app')

@section('title', 'Aircraft Details — ' . $aircraft->manufacturer . ' ' . $aircraft->model)
@section('page-title', 'Aircraft Details')
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
                        Active
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                        Inactive
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Data Source: {{ $aircraft->data_source ?? 'ICAO Methodology' }}</p>
        </div>

        <div class="flex gap-2">
            @can('manage-aircraft')
            <a href="{{ route('aircraft.edit', $aircraft) }}" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Edit Aircraft
            </a>
            @endcan
            <a href="{{ route('aircraft.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                &larr; Aircraft Directory
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Specs -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Fleet Specifications</h3>
            <div>
                <span class="text-xs text-slate-400 block">Manufacturer</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aircraft->manufacturer }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Model</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aircraft->model }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">IATA Type</span>
                <span class="text-sm font-mono text-slate-800 dark:text-slate-100">{{ $aircraft->iata_type ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Equivalent Aircraft</span>
                <span class="text-sm font-mono text-slate-800 dark:text-slate-100">{{ $aircraft->equivalent_aircraft ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Seat Capacity (Y-Seats)</span>
                <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($aircraft->y_seats) }} seats</span>
            </div>
        </div>

        <!-- ICAO Calculation Factors -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Emission & Consumption Factors (ICAO)</h3>
            <div>
                <span class="text-xs text-slate-400 block">Fuel Burn Factor</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ $aircraft->fuel_burn_factor ? number_format($aircraft->fuel_burn_factor, 4) . ' kg/km' : 'Based on Distance Matrix' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Passenger to Freight Factor</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ number_format($aircraft->passenger_to_freight_factor ?? 0.85, 2) }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">CO₂ Emission Factor</span>
                <span class="text-sm font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($aircraft->co2_factor ?? 3.16, 2) }} kg CO₂ / kg Fuel</span>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                <span class="text-xs text-slate-400 block">Flight History</span>
                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $aircraft->flights->count() }} recorded flights</span>
            </div>
        </div>

        <!-- Notes / Formula -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Methodology & Notes</h3>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                {{ $aircraft->notes ?? 'This aircraft utilizes the ICAO Fuel Consumption Curve calculation based on Great Circle Distance (GCD) and operational route corrections.' }}
            </p>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300">
                CO₂ = Total Fuel × 3.16<br>
                Pax CO₂ = CO₂ × 0.85 / (Y-Seats × LF)
            </div>
        </div>
    </div>

</div>
@endsection
