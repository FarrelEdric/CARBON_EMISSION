@extends('layouts.app')

@section('title', 'ICAO Carbon Emissions Calculator')
@section('page-title', 'Carbon Emissions Calculator')
@section('page-subtitle', 'Aviation Emission Estimation System Based on ICAO Doc 9889 Standards')

@section('content')
<div class="p-4 md:p-6 space-y-6" x-data="carbonCalculatorApp()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    ICAO Doc 9889
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">CORSIA Standard Framework</span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                Flight CO₂ Emissions Calculator
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Commercial passenger flight fuel burn and CO₂ emissions estimation based on corrected GCD distance and passenger-freight allocation.
            </p>
        </div>

        <!-- Quick Route Presets -->
        <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-400 font-medium hidden md:inline">Route Presets:</span>
            <div class="inline-flex p-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <button type="button" @click="applyPreset('icao')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    ICAO Sample
                </button>
                <button type="button" @click="applyPreset('cgk-dps')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    CGK &rarr; DPS
                </button>
                <button type="button" @click="applyPreset('cgk-upg')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    CGK &rarr; UPG
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid: Left = Form Inputs, Right = Results & Map -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ================= LEFT COLUMN: FORM CONTROLS (5 cols) ================= -->
        <div class="lg:col-span-5">
            <form @submit.prevent="submitCalculation" class="card divide-y divide-slate-100 dark:divide-slate-800">

                <!-- 1. Flight Route Section -->
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            1. Flight Route
                        </span>
                        <button type="button" @click="swapAirports"
                                class="text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:underline flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Swap Route
                        </button>
                    </div>

                    <!-- Departure Airport -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Origin Airport <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.departure_airport_id" @change="onRouteChanged" required
                                class="w-full form-select-base">
                            <option value="">-- Select Origin Airport --</option>
                            @foreach($airports as $ap)
                                <option value="{{ $ap->id }}" data-lat="{{ $ap->latitude }}" data-lng="{{ $ap->longitude }}" data-iata="{{ $ap->iata_code }}">
                                    {{ $ap->iata_code }} — {{ $ap->name }} ({{ $ap->city }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Arrival Airport -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Destination Airport <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.arrival_airport_id" @change="onRouteChanged" required
                                class="w-full form-select-base">
                            <option value="">-- Select Destination Airport --</option>
                            @foreach($airports as $ap)
                                <option value="{{ $ap->id }}" data-lat="{{ $ap->latitude }}" data-lng="{{ $ap->longitude }}" data-iata="{{ $ap->iata_code }}">
                                    {{ $ap->iata_code }} — {{ $ap->name }} ({{ $ap->city }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Route Distance Live Info -->
                    <div x-show="previewDistance.gcd > 0" x-cloak class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-lg border border-slate-200 dark:border-slate-800 text-xs space-y-1.5 font-mono">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Great Circle Distance (GCD):</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="previewDistance.gcd.toLocaleString() + ' km'"></span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>ICAO Route Deviation Correction:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="'+' + previewDistance.correction + ' km'"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 dark:border-slate-800 pt-1.5 text-slate-900 dark:text-slate-100 font-bold">
                            <span>Estimated Corrected Distance:</span>
                            <span class="text-[#0B5A9E] dark:text-sky-400" x-text="previewDistance.adjusted.toLocaleString() + ' km'"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Aircraft & Configuration Section -->
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            2. Aircraft Type & Capacity
                        </span>
                    </div>

                    <!-- Aircraft Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Select Aircraft Fleet Model
                        </label>
                        <select x-model="form.aircraft_id" @change="onAircraftChanged"
                                class="w-full form-select-base">
                            <option value="">-- Custom / Manual Input --</option>
                            @foreach($aircraft as $ac)
                                <option value="{{ $ac->id }}"
                                        data-seats="{{ $ac->y_seats }}"
                                        data-paxratio="{{ $ac->passenger_to_freight_factor }}"
                                        data-fuelburn="{{ $ac->fuel_burn_factor }}"
                                        data-co2="{{ $ac->co2_factor }}">
                                    {{ $ac->manufacturer }} {{ $ac->model }} ({{ $ac->icao_type }}) — {{ $ac->y_seats }} Seats
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Y-Seats -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Economy Class Seat Capacity (Y-Seats) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   min="1"
                                   x-model.number="form.y_seats"
                                   required
                                   placeholder="200"
                                   class="w-full form-input-base pr-16 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 pointer-events-none">seats</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Fuel & Operational Parameters Section -->
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            3. Fuel Consumption Parameters
                        </span>
                    </div>

                    <!-- Total Fuel Burn -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Total Flight Fuel Burn <span class="text-red-500">*</span>
                            </label>
                            <button type="button" x-show="canEstimateFuel" @click="estimateFuelFromDistance" x-cloak
                                    class="text-xs text-[#0B5A9E] dark:text-sky-400 hover:underline font-semibold">
                                Estimate from Distance
                            </button>
                        </div>
                        <div class="relative">
                            <input type="number"
                                   step="0.1"
                                   min="0.1"
                                   x-model.number="form.total_fuel_kg"
                                   required
                                   placeholder="5000"
                                   class="w-full form-input-base pr-12 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">kg</span>
                        </div>
                    </div>

                    <!-- Passenger to Freight Factor -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Passenger vs. Freight Allocation
                            </label>
                            <span class="text-xs font-mono font-bold text-[#0B5A9E] dark:text-sky-400" x-text="(form.passenger_to_freight_factor * 100).toFixed(0) + '% Pax'"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-lg">
                            <button type="button" @click="form.passenger_to_freight_factor = 0.80"
                                    :class="form.passenger_to_freight_factor === 0.80 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1 text-xs rounded transition">
                                80% (ICAO)
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 0.85"
                                    :class="form.passenger_to_freight_factor === 0.85 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1 text-xs rounded transition">
                                85%
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 0.90"
                                    :class="form.passenger_to_freight_factor === 0.90 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1 text-xs rounded transition">
                                90%
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 1.00"
                                    :class="form.passenger_to_freight_factor === 1.00 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1 text-xs rounded transition">
                                100%
                            </button>
                        </div>
                    </div>

                    <!-- Passenger Load Factor -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Passenger Load Factor (LF)
                            </label>
                            <span class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200" x-text="(form.passenger_load_factor * 100).toFixed(0) + '% (' + estimatedPaxCount + ' Passengers)'"></span>
                        </div>
                        <input type="range" min="0.10" max="1.00" step="0.01"
                               x-model.number="form.passenger_load_factor"
                               class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-[#0B5A9E]">
                    </div>

                    <!-- CO2 Factor -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            CO₂ Emission Factor
                        </label>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   x-model.number="form.co2_factor"
                                   required
                                   placeholder="3.16"
                                   class="w-full form-input-base pr-28 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">t CO₂ / t fuel</span>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">ICAO standard factor: 1 tonne fuel combustion produces 3.16 tonnes CO₂.</span>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="p-5 bg-slate-50/50 dark:bg-slate-800/40 flex items-center gap-3">
                    <button type="submit"
                            :disabled="isLoading"
                            class="btn-primary flex-1 h-10 text-xs sm:text-sm font-semibold rounded-lg shadow-xs">
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Calculating Emissions...' : 'Calculate Emissions'"></span>
                    </button>
                    <button type="button" @click="resetForm" class="btn-secondary h-10 px-4 text-xs sm:text-sm font-medium rounded-lg">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= RIGHT COLUMN: RESULTS, MAP & BREAKDOWN (7 cols) ================= -->
        <div class="lg:col-span-7 space-y-5">

            <!-- Initial Placeholder (when not calculated yet) -->
            <div x-show="!hasResult && !isLoading" class="card p-10 flex flex-col items-center justify-center text-center min-h-[460px] space-y-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div class="max-w-md space-y-1">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Ready to Calculate Emissions</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Select origin, destination, and fleet parameters on the left panel to compute flight CO₂ emissions in accordance with official ICAO standards.
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" @click="applyPreset('cgk-dps')" class="btn-secondary">
                        Try Simulation: CGK &rarr; DPS (B738)
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="isLoading" class="card p-10 flex flex-col items-center justify-center text-center min-h-[460px] space-y-3">
                <div class="w-7 h-7 border-2 border-[#0B5A9E] border-t-transparent rounded-full animate-spin"></div>
                <div class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Calculating Emissions...</div>
                <div class="text-xs text-slate-400 font-mono">Processing ICAO Doc 9889 methodology</div>
            </div>

            <!-- Result Cards & Dashboard (when calculated) -->
            <div x-show="hasResult && !isLoading" x-cloak class="space-y-5">

                <!-- 1. Executive Summary & Hero KPI Card -->
                <div class="card p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold font-mono text-slate-900 dark:text-white" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code"></span>
                            <span class="text-xs text-slate-500 dark:text-slate-400" x-text="resultData.departure.city + ' (' + resultData.departure.iata_code + ') to ' + resultData.arrival.city + ' (' + resultData.arrival.iata_code + ')'"></span>
                        </div>
                        <span class="px-2.5 py-1 rounded text-xs font-semibold"
                              :class="getEfficiencyBadgeClass(resultData.result.co2_per_passenger_kg)"
                              x-text="getEfficiencyLabel(resultData.result.co2_per_passenger_kg)">
                        </span>
                    </div>

                    <!-- HERO METRIC: Clean Professional Focus -->
                    <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-[11px] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400">
                                ESTIMATED CO₂
                            </div>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-4xl sm:text-5xl font-extrabold font-mono text-[#0B5A9E] dark:text-sky-400 tracking-tight"
                                      x-text="resultData.result.co2_per_passenger_kg.toFixed(2)"></span>
                                <span class="text-xl font-bold text-slate-700 dark:text-slate-300 font-mono">kg CO₂</span>
                            </div>
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                per passenger (economy class)
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 sm:text-right border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-slate-800 pt-3 sm:pt-0 sm:pl-5 space-y-1">
                            <div>Standard: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">ICAO Doc 9889</span></div>
                            <div>Emission Factor: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300" x-text="resultData.result.co2_factor + ' t CO₂ / t fuel'"></span></div>
                            <div class="text-[11px] text-slate-400">(1 kg Fuel = 3.16 kg CO₂)</div>
                        </div>
                    </div>

                    <!-- 4 Main Secondary KPI Metrics -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        <!-- 1. Total Passenger CO2 -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Passenger CO₂</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="(resultData.result.passenger_co2_total_tonnes || (resultData.result.passenger_fuel_kg * resultData.result.co2_factor / 1000)).toFixed(2)"></span>
                                <span class="text-xs font-normal text-slate-500">tonnes</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="(resultData.result.passenger_co2_total_kg || Math.round(resultData.result.passenger_fuel_kg * resultData.result.co2_factor)).toLocaleString() + ' kg CO₂'"></div>
                        </div>

                        <!-- 2. Fuel Consumption -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fuel Consumption</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.total_fuel_kg.toLocaleString()"></span>
                                <span class="text-xs font-normal text-slate-500">kg</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="'Pax: ' + resultData.result.passenger_fuel_kg.toLocaleString() + ' kg (' + (resultData.result.passenger_to_freight_factor * 100).toFixed(0) + '%)'"></div>
                        </div>

                        <!-- 3. Flight Distance -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Flight Distance</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.distance_adjusted_km.toLocaleString()"></span>
                                <span class="text-xs font-normal text-slate-500">km</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="'GCD ' + resultData.result.distance_gcd_km.toLocaleString() + ' (+' + Math.round(resultData.result.correction_km) + ' km)'"></div>
                        </div>

                        <!-- 4. Passengers -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passengers</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.passenger_count"></span>
                                <span class="text-xs font-normal text-slate-500">pax</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="resultData.result.y_seats + ' seats (' + (resultData.result.passenger_load_factor * 100).toFixed(0) + '% LF)'"></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Interactive Map Visualizer -->
                <div class="card overflow-hidden flex flex-col">
                    <div class="px-4 py-2.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs bg-slate-50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#0B5A9E]"></span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Flight Route Map</span>
                        </div>
                        <span class="font-mono text-slate-500 dark:text-slate-400" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code + ' (' + resultData.result.distance_adjusted_km + ' km)'"></span>
                    </div>
                    <div id="calculator-map" class="w-full h-72 bg-[#e0f2fe] dark:bg-[#0b1329] transition-colors relative"></div>
                </div>

                <!-- 3. Audit & Calculation Table Breakdown -->
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">ICAO Formula Calculation Audit</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Parameter substitution verification based on official Doc 9889 methodology</p>
                        </div>
                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400 hidden sm:inline">
                            CO₂/pax = 3.16 × (Fuel × P/F) / (Seats × LF)
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-4 py-2.5">Calculation Stage</th>
                                    <th class="px-4 py-2.5">Methodology Formula</th>
                                    <th class="px-4 py-2.5">Substituted Input Values</th>
                                    <th class="px-4 py-2.5 text-right">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        1. Corrected Route Distance
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">GCD + ICAO Deviation Correction</td>
                                    <td class="px-4 py-3" x-text="resultData.result.distance_gcd_km.toLocaleString() + ' km + ' + Math.round(resultData.result.correction_km) + ' km'"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.distance_adjusted_km.toLocaleString() + ' km'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        2. Passenger Fuel Share
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">Total Fuel × Pax Factor</td>
                                    <td class="px-4 py-3" x-text="resultData.result.total_fuel_kg.toLocaleString() + ' kg × ' + resultData.result.passenger_to_freight_factor"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.passenger_fuel_kg.toLocaleString() + ' kg'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        3. Estimated Passengers Carried
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">Y-Seats × Load Factor</td>
                                    <td class="px-4 py-3" x-text="resultData.result.y_seats + ' seats × ' + resultData.result.passenger_load_factor"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.passenger_count + ' pax'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        4. Total Passenger Emissions
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">Passenger Fuel × 3.16</td>
                                    <td class="px-4 py-3" x-text="resultData.result.passenger_fuel_kg.toLocaleString() + ' kg × ' + resultData.result.co2_factor"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="(resultData.result.passenger_co2_total_tonnes || (resultData.result.passenger_fuel_kg * resultData.result.co2_factor / 1000)).toFixed(2) + ' tonnes'"></td>
                                </tr>
                                <tr class="bg-blue-50/40 dark:bg-blue-950/20">
                                    <td class="px-4 py-3 font-sans font-bold text-[#0B5A9E] dark:text-sky-400">
                                        5. CO₂ Emissions per Passenger
                                    </td>
                                    <td class="px-4 py-3 text-[#0B5A9E] dark:text-sky-400">Total Pax Emissions / Pax Count</td>
                                    <td class="px-4 py-3 text-[#0B5A9E] dark:text-sky-400" x-text="(resultData.result.passenger_co2_total_kg || Math.round(resultData.result.passenger_fuel_kg * resultData.result.co2_factor)).toLocaleString() + ' kg / ' + resultData.result.passenger_count + ' pax'"></td>
                                    <td class="px-4 py-3 text-right font-black text-sm text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.co2_per_passenger_kg.toFixed(2) + ' kg CO₂/pax'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Action & Navigation Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <div class="flex gap-2">
                        <button type="button" @click="copySummary"
                                class="btn-secondary h-9 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <span x-text="copied ? 'Copied to Clipboard!' : 'Copy Summary'"></span>
                        </button>
                        <button type="button" @click="window.print()"
                                class="btn-secondary h-9 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Report
                        </button>
                    </div>

                    <a href="{{ route('flights.index') }}"
                       class="text-xs font-semibold text-[#0B5A9E] dark:text-sky-400 hover:underline flex items-center gap-1">
                        Flight Monitoring &rarr;
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function carbonCalculatorApp() {
    return {
        form: {
            departure_airport_id: '',
            arrival_airport_id: '',
            aircraft_id: '',
            total_fuel_kg: 5000,
            passenger_to_freight_factor: 0.80,
            y_seats: 200,
            passenger_load_factor: 0.80,
            co2_factor: {{ $defaultCo2Factor ?? 3.16 }},
        },
        previewDistance: {
            gcd: 0,
            correction: 0,
            adjusted: 0,
        },
        isLoading: false,
        hasResult: false,
        copied: false,
        resultData: {
            departure: {},
            arrival: {},
            result: {},
            operational_route: null,
        },
        mapInstance: null,
        selectedAircraftFuelBurn: null,
        tileLayerInstance: null,
        resizeObs: null,
        themeListenerAttached: false,

        get estimatedPaxCount() {
            return Math.round((this.form.y_seats || 0) * (this.form.passenger_load_factor || 0));
        },

        get canEstimateFuel() {
            return this.previewDistance.adjusted > 0 && this.selectedAircraftFuelBurn > 0;
        },

        estimateFuelFromDistance() {
            if (this.canEstimateFuel) {
                this.form.total_fuel_kg = Math.round(this.previewDistance.adjusted * this.selectedAircraftFuelBurn);
            }
        },

        onAircraftChanged(e) {
            const opt = e.target.selectedOptions[0];
            if (!opt || !opt.value) {
                this.selectedAircraftFuelBurn = null;
                return;
            }
            if (opt.dataset.seats) this.form.y_seats = parseInt(opt.dataset.seats);
            if (opt.dataset.paxratio) this.form.passenger_to_freight_factor = parseFloat(opt.dataset.paxratio);
            if (opt.dataset.co2) this.form.co2_factor = parseFloat(opt.dataset.co2);
            if (opt.dataset.fuelburn) {
                this.selectedAircraftFuelBurn = parseFloat(opt.dataset.fuelburn);
                if (this.previewDistance.adjusted > 0) {
                    this.form.total_fuel_kg = Math.round(this.previewDistance.adjusted * this.selectedAircraftFuelBurn);
                }
            } else {
                this.selectedAircraftFuelBurn = null;
            }
        },

        onRouteChanged() {
            const depSelect = document.querySelector('select[x-model="form.departure_airport_id"]');
            const arrSelect = document.querySelector('select[x-model="form.arrival_airport_id"]');

            const depOpt = depSelect ? depSelect.selectedOptions[0] : null;
            const arrOpt = arrSelect ? arrSelect.selectedOptions[0] : null;

            if (depOpt && arrOpt && depOpt.value && arrOpt.value && depOpt.value !== arrOpt.value) {
                const lat1 = parseFloat(depOpt.dataset.lat);
                const lng1 = parseFloat(depOpt.dataset.lng);
                const lat2 = parseFloat(arrOpt.dataset.lat);
                const lng2 = parseFloat(arrOpt.dataset.lng);

                if (!isNaN(lat1) && !isNaN(lng1) && !isNaN(lat2) && !isNaN(lng2)) {
                    const gcd = this.calculateGCD(lat1, lng1, lat2, lng2);
                    let correction = 100;
                    if (gcd < 550) correction = 50;
                    else if (gcd > 5500) correction = 125;

                    this.previewDistance = {
                        gcd: Math.round(gcd),
                        correction: correction,
                        adjusted: Math.round(gcd + correction)
                    };

                    if (this.selectedAircraftFuelBurn && (!this.form.total_fuel_kg || this.form.total_fuel_kg === 5000)) {
                        this.form.total_fuel_kg = Math.round(this.previewDistance.adjusted * this.selectedAircraftFuelBurn);
                    }
                    return;
                }
            }
            this.previewDistance = { gcd: 0, correction: 0, adjusted: 0 };
        },

        calculateGCD(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        },

        swapAirports() {
            const temp = this.form.departure_airport_id;
            this.form.departure_airport_id = this.form.arrival_airport_id;
            this.form.arrival_airport_id = temp;
            this.onRouteChanged();
        },

        applyPreset(preset) {
            const depSelect = document.querySelector('select[x-model="form.departure_airport_id"]');
            const arrSelect = document.querySelector('select[x-model="form.arrival_airport_id"]');

            const findAirportId = (iata) => {
                for (let opt of depSelect.options) {
                    if (opt.dataset.iata === iata || opt.text.includes(iata)) return opt.value;
                }
                return '';
            };

            if (preset === 'icao') {
                const cgk = findAirportId('CGK') || depSelect.options[1]?.value;
                const sub = findAirportId('SUB') || depSelect.options[2]?.value;
                this.form.departure_airport_id = cgk;
                this.form.arrival_airport_id = sub;
                this.form.aircraft_id = '';
                this.form.total_fuel_kg = 5000;
                this.form.passenger_to_freight_factor = 0.80;
                this.form.y_seats = 200;
                this.form.passenger_load_factor = 0.80;
                this.form.co2_factor = 3.16;
            } else if (preset === 'cgk-dps') {
                this.form.departure_airport_id = findAirportId('CGK') || depSelect.options[1]?.value;
                this.form.arrival_airport_id = findAirportId('DPS') || depSelect.options[2]?.value;
                this.form.total_fuel_kg = 4200;
                this.form.passenger_to_freight_factor = 0.85;
                this.form.y_seats = 180;
                this.form.passenger_load_factor = 0.85;
                this.form.co2_factor = 3.16;
            } else if (preset === 'cgk-upg') {
                this.form.departure_airport_id = findAirportId('CGK') || depSelect.options[1]?.value;
                this.form.arrival_airport_id = findAirportId('UPG') || depSelect.options[2]?.value;
                this.form.total_fuel_kg = 6100;
                this.form.passenger_to_freight_factor = 0.85;
                this.form.y_seats = 180;
                this.form.passenger_load_factor = 0.88;
                this.form.co2_factor = 3.16;
            }

            this.onRouteChanged();
            this.submitCalculation();
        },

        async submitCalculation() {
            if (!this.form.departure_airport_id || !this.form.arrival_airport_id) {
                alert('Please select both origin and destination airports.');
                return;
            }
            if (this.form.departure_airport_id === this.form.arrival_airport_id) {
                alert('Origin and destination airports cannot be the same.');
                return;
            }

            this.isLoading = true;

            try {
                const response = await fetch('{{ route('carbon-calculator.calculate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok || data.error) {
                    alert(data.error || 'An error occurred while calculating emissions.');
                    this.isLoading = false;
                    return;
                }

                this.resultData = data;
                this.hasResult = true;
                this.isLoading = false;

                // Render map
                this.$nextTick(() => {
                    this.renderMap(data.departure, data.arrival);
                });

            } catch (err) {
                console.error(err);
                alert('Failed to connect to server: ' + err.message);
                this.isLoading = false;
            }
        },

        async renderMap(dep, arr) {
            const mapEl = document.getElementById('calculator-map');
            if (!mapEl) return;

            if (this.planeAnimationId) {
                cancelAnimationFrame(this.planeAnimationId);
                this.planeAnimationId = null;
            }

            if (this.mapInstance) {
                this.mapInstance.remove();
                this.mapInstance = null;
            }

            const isDark = document.documentElement.classList.contains('dark');
            mapEl.style.backgroundColor = isDark ? '#0b1329' : '#e0f2fe';

            this.mapInstance = L.map('calculator-map', {
                zoomControl: true,
                attributionControl: false
            });

            const bounds = L.latLngBounds([
                [dep.lat, dep.lng],
                [arr.lat, arr.lng]
            ]);

            // OpenStreetMap Tile Layer
            const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
            this.tileLayerInstance = L.tileLayer(tileUrl, {
                subdomains: 'abc',
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(this.mapInstance);


            // High-visibility Departure Pin (Origin)
            const depDiv = L.divIcon({
                className: 'dep-active-marker',
                html: `
                    <div style="display:flex;flex-direction:column;align-items:center;transform:translate(-50%,-100%);pointer-events:none;">
                        <div style="display:flex;align-items:center;gap:4px;background:#0B5A9E;color:#ffffff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:6px;box-shadow:0 3px 10px rgba(11,90,158,0.5);border:1px solid #38bdf8;white-space:nowrap;font-family:Inter,sans-serif;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#38bdf8;display:inline-block;"></span>
                            ${dep.iata_code} • ${dep.city || dep.name} (Origin)
                        </div>
                        <div style="width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid #0B5A9E;"></div>
                        <div style="width:8px;height:8px;border-radius:50%;background:#0B5A9E;border:2px solid #ffffff;box-shadow:0 0 6px rgba(11,90,158,0.9);margin-top:-3px;"></div>
                    </div>
                `,
                iconSize: [0, 0]
            });
            L.marker([dep.lat, dep.lng], { icon: depDiv }).addTo(this.mapInstance);

            // High-visibility Arrival Pin (Destination)
            const arrDiv = L.divIcon({
                className: 'arr-active-marker',
                html: `
                    <div style="display:flex;flex-direction:column;align-items:center;transform:translate(-50%,-100%);pointer-events:none;">
                        <div style="display:flex;align-items:center;gap:4px;background:#059669;color:#ffffff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:6px;box-shadow:0 3px 10px rgba(5,150,105,0.5);border:1px solid #34d399;white-space:nowrap;font-family:Inter,sans-serif;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;"></span>
                            ${arr.iata_code} • ${arr.city || arr.name} (Destination)
                        </div>
                        <div style="width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid #059669;"></div>
                        <div style="width:8px;height:8px;border-radius:50%;background:#059669;border:2px solid #ffffff;box-shadow:0 0 6px rgba(5,150,105,0.9);margin-top:-3px;"></div>
                    </div>
                `,
                iconSize: [0, 0]
            });
            L.marker([arr.lat, arr.lng], { icon: arrDiv }).addTo(this.mapInstance);

            // Flight Path Polyline
            L.polyline([[dep.lat, dep.lng], [arr.lat, arr.lng]], {
                color: isDark ? '#38bdf8' : '#0B5A9E',
                weight: 3.5,
                opacity: 0.9,
                dashArray: '7, 7'
            }).addTo(this.mapInstance);

            // Calculate bearing (heading in degrees) from departure to arrival
            const toRad = Math.PI / 180;
            const toDeg = 180 / Math.PI;
            const lat1 = dep.lat * toRad;
            const lat2 = arr.lat * toRad;
            const dLng = (arr.lng - dep.lng) * toRad;
            const y = Math.sin(dLng) * Math.cos(lat2);
            const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
            const bearing = (Math.atan2(y, x) * toDeg + 360) % 360;

            // Animated Flight Airplane Marker
            const planeIcon = L.divIcon({
                className: 'flight-route-plane-marker',
                html: `
                    <div style="display:flex;align-items:center;justify-content:center;transform:translate(-50%,-50%);pointer-events:none;">
                        <div style="transform:rotate(${bearing}deg);display:flex;align-items:center;justify-content:center;">
                            <div style="background:${isDark ? '#0284c7' : '#0B5A9E'};width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 10px rgba(0,0,0,0.35);border:2px solid #ffffff;">
                                <svg style="width:15px;height:15px;color:#ffffff;fill:currentColor;" viewBox="0 0 24 24">
                                    <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                `,
                iconSize: [0, 0]
            });

            const planeMarker = L.marker([dep.lat, dep.lng], {
                icon: planeIcon,
                zIndexOffset: 1000
            }).addTo(this.mapInstance);

            // Loop flight animation along route (e.g. 5 seconds per cycle)
            const loopDuration = 8000;
            let animStart = null;

            const animateFlightLoop = (now) => {
                if (!this.mapInstance) return;
                if (!animStart) animStart = now;
                const elapsed = now - animStart;
                const progress = (elapsed % loopDuration) / loopDuration; // 0.0 -> 1.0

                const curLat = dep.lat + (arr.lat - dep.lat) * progress;
                const curLng = dep.lng + (arr.lng - dep.lng) * progress;

                planeMarker.setLatLng([curLat, curLng]);
                this.planeAnimationId = requestAnimationFrame(animateFlightLoop);
            };

            this.planeAnimationId = requestAnimationFrame(animateFlightLoop);

            // Fit bounds with comfortable padding
            this.mapInstance.fitBounds(bounds, { padding: [60, 60] });

            // FIX: Invalidate map size after Alpine finishes expanding the container
            setTimeout(() => {
                if (this.mapInstance) {
                    this.mapInstance.invalidateSize();
                    this.mapInstance.fitBounds(bounds, { padding: [60, 60] });
                }
            }, 100);

            setTimeout(() => {
                if (this.mapInstance) {
                    this.mapInstance.invalidateSize();
                    this.mapInstance.fitBounds(bounds, { padding: [60, 60] });
                }
            }, 300);

            // Auto-observe container resize
            if (window.ResizeObserver && !this.resizeObs) {
                this.resizeObs = new ResizeObserver(() => {
                    if (this.mapInstance) this.mapInstance.invalidateSize();
                });
                this.resizeObs.observe(mapEl);
            }

            // Sync with dark/light mode toggle
            if (!this.themeListenerAttached) {
                this.themeListenerAttached = true;
                window.addEventListener('theme-changed', () => {
                    if (this.hasResult && this.resultData?.departure?.lat) {
                        this.renderMap(this.resultData.departure, this.resultData.arrival);
                    }
                });
            }
        },

        getEfficiencyBadgeClass(co2Pax) {
            if (co2Pax < 90) return 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800';
            if (co2Pax <= 140) return 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800';
            return 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800';
        },

        getEfficiencyLabel(co2Pax) {
            if (co2Pax < 90) return 'Highly Efficient';
            if (co2Pax <= 140) return 'Moderate / Normal';
            return 'High Emissions';
        },

        copySummary() {
            if (!this.hasResult) return;
            const r = this.resultData;
            const paxCo2Tonnes = (r.result.passenger_co2_total_tonnes || (r.result.passenger_fuel_kg * r.result.co2_factor / 1000)).toFixed(2);
            const paxCo2Kg = (r.result.passenger_co2_total_kg || Math.round(r.result.passenger_fuel_kg * r.result.co2_factor)).toLocaleString();
            const text = `ACE AVIATION CARBON EMISSION REPORT\n` +
                `====================================\n` +
                `Route: ${r.departure.iata_code} (${r.departure.city}) -> ${r.arrival.iata_code} (${r.arrival.city})\n` +
                `Flight Distance: ${r.result.distance_adjusted_km.toLocaleString()} km (GCD: ${r.result.distance_gcd_km.toLocaleString()} km, ICAO Correction: +${Math.round(r.result.correction_km)} km)\n` +
                `Fuel Burn: ${r.result.total_fuel_kg.toLocaleString()} kg (Pax Share: ${r.result.passenger_fuel_kg.toLocaleString()} kg / ${(r.result.passenger_to_freight_factor*100).toFixed(0)}%)\n` +
                `Capacity & Load: ${r.result.y_seats} seats (LF: ${(r.result.passenger_load_factor*100).toFixed(0)}%, Passengers: ${r.result.passenger_count} pax)\n` +
                `------------------------------------\n` +
                `ESTIMATED CO2: ${r.result.co2_per_passenger_kg.toFixed(2)} kg CO2 per passenger\n` +
                `Total Passenger CO2: ${paxCo2Tonnes} tonnes (${paxCo2Kg} kg CO2)\n` +
                `Total Flight Fuel: ${r.result.total_fuel_kg.toLocaleString()} kg\n` +
                `Total Flight CO2: ${r.result.co2_total_tonnes.toFixed(2)} tonnes (${r.result.co2_total_kg.toLocaleString()} kg CO2)\n` +
                `------------------------------------\n` +
                `Methodology: ICAO Doc 9889 & CORSIA Framework\n` +
                `Formula: CO2/pax = 3.16 * (Total Fuel * Pax Factor) / (Y-Seats * Load Factor)`;

            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2500);
            });
        },

        resetForm() {
            this.form.departure_airport_id = '';
            this.form.arrival_airport_id = '';
            this.form.aircraft_id = '';
            this.form.total_fuel_kg = 5000;
            this.form.passenger_to_freight_factor = 0.80;
            this.form.y_seats = 200;
            this.form.passenger_load_factor = 0.80;
            this.form.co2_factor = {{ $defaultCo2Factor ?? 3.16 }};
            this.previewDistance = { gcd: 0, correction: 0, adjusted: 0 };
            this.hasResult = false;
            if (this.mapInstance) {
                this.mapInstance.remove();
                this.mapInstance = null;
            }
        }
    };
}
</script>
@endpush
