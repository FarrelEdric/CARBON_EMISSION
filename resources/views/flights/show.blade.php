@extends('layouts.app')

@section('title', 'Flight Details — ' . $flight->flight_number)
@section('page-title', 'Flight Details')
@section('page-subtitle', $flight->flight_number . ' (' . ($flight->departureAirport?->iata_code ?? '') . ' → ' . ($flight->arrivalAirport?->iata_code ?? '') . ')')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black font-mono text-[#0B5A9E] dark:text-sky-400">{{ $flight->flight_number }}</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                    {{ $flight->flight_date?->format('d M Y') }}
                </span>
            </div>
            <h1 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mt-1">
                {{ $flight->departureAirport?->name }} ({{ $flight->departureAirport?->iata_code }}) &rarr; {{ $flight->arrivalAirport?->name }} ({{ $flight->arrivalAirport?->iata_code }})
            </h1>
        </div>

        <div class="flex gap-2">
            @can('manage-flights')
            <a href="{{ route('flights.edit', $flight) }}" class="btn-primary">
                Edit Flight
            </a>
            @endcan
            <a href="{{ route('flights.index') }}" class="btn-secondary">
                &larr; Flight List
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-medium text-slate-400 block mb-1">Total CO₂ Emissions</span>
            <div class="text-xl font-bold font-mono text-slate-800 dark:text-slate-100">
                {{ number_format($flight->co2_total_kg, 2) }} kg
            </div>
            <span class="text-[10px] text-slate-400">{{ number_format($flight->co2_total_kg / 1000, 3) }} tonnes</span>
        </div>
        <div class="card p-4">
            <span class="text-[11px] font-medium text-slate-400 block mb-1">CO₂ per Passenger</span>
            <div class="text-xl font-bold font-mono text-[#0B5A9E] dark:text-sky-400">
                {{ number_format($flight->co2_per_passenger_kg, 2) }} kg
            </div>
            <span class="text-[10px] text-slate-400">per pax</span>
        </div>
        <div class="card p-4">
            <span class="text-[11px] font-medium text-slate-400 block mb-1">Fuel Consumption</span>
            <div class="text-xl font-bold font-mono text-slate-800 dark:text-slate-100">
                {{ number_format($flight->total_fuel_kg) }} kg
            </div>
            <span class="text-[10px] text-slate-400">Jet-A1</span>
        </div>
        <div class="card p-4">
            <span class="text-[11px] font-medium text-slate-400 block mb-1">Distance (GCD)</span>
            <div class="text-xl font-bold font-mono text-slate-800 dark:text-slate-100">
                {{ number_format($flight->distance_gcd_km, 1) }} km
            </div>
            <span class="text-[10px] text-slate-400">Adjusted: {{ number_format($flight->distance_adjusted_km ?? $flight->distance_gcd_km, 1) }} km</span>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Route & Airport Details -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Airport & Route Details</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Origin Airport</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $flight->departureAirport?->name }} ({{ $flight->departureAirport?->iata_code }} / {{ $flight->departureAirport?->icao_code }})
                    </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Destination Airport</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $flight->arrivalAirport?->name }} ({{ $flight->arrivalAirport?->iata_code }} / {{ $flight->arrivalAirport?->icao_code }})
                    </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Great Circle Distance (GCD)</span>
                    <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ number_format($flight->distance_gcd_km, 1) }} km</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400">ICAO Corrected Distance</span>
                    <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ number_format($flight->distance_adjusted_km ?? $flight->distance_gcd_km, 1) }} km</span>
                </div>
            </div>
        </div>

        <!-- Aircraft & Payload Details -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Aircraft & Passenger Details</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Aircraft Type</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $flight->aircraft?->manufacturer }} {{ $flight->aircraft?->model }} ({{ $flight->aircraft?->icao_type }})
                    </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Seat Capacity (Y-Seats)</span>
                    <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ $flight->y_seats ?? $flight->aircraft?->y_seats }} seats</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Passenger Load Factor</span>
                    <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ round($flight->passenger_load_factor * 100, 1) }}%</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400">Estimated Passengers</span>
                    <span class="font-mono font-semibold text-[#0B5A9E] dark:text-sky-400">{{ round($flight->passenger_count ?? (($flight->y_seats ?? $flight->aircraft?->y_seats) * $flight->passenger_load_factor)) }} pax</span>
                </div>
            </div>
        </div>
    </div>

    @if($flight->notes)
    <div class="card p-5">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Operational Notes</h3>
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $flight->notes }}</p>
    </div>
    @endif

</div>
@endsection
