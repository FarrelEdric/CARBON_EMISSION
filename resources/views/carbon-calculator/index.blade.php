@extends('layouts.app')

@section('title', 'Kalkulator Emisi Karbon ICAO')
@section('page-title', 'Kalkulator Emisi Karbon')
@section('page-subtitle', 'Simulasi Perhitungan Emisi Aviasi Berdasarkan Metodologi Resmi ICAO')

@section('content')
<div class="p-4 md:p-6 space-y-6" x-data="carbonCalculatorApp()">

    <!-- Header & Methodology Banner -->
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        ICAO Standard Methodology
                    </span>
                    <span class="text-xs text-slate-400">Doc 9889 / CORSIA Framework</span>
                </div>
                <h1 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                    Perhitungan Estimasi Emisi CO₂ Penerbangan per Penumpang
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-3xl">
                    Kalkulator ini mengimplementasikan formula resmi ICAO: menghitung jarak lingkaran besar (Great Circle Distance / GCD) dengan koreksi deviasi rute operasional, mengalokasikan konsumsi bahan bakar melalui faktor penumpang-kargo, dan membaginya dengan estimasi keterisian kursi.
                </p>
            </div>

            <!-- Preset Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-slate-400 w-full lg:w-auto">Preset:</span>
                <button type="button" @click="applyPreset('icao')"
                        class="px-2.5 py-1 text-xs font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-md transition border border-slate-300 dark:border-slate-700 shadow-xs">
                    Contoh ICAO
                </button>
                <button type="button" @click="applyPreset('cgk-dps')"
                        class="px-2.5 py-1 text-xs font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-md transition border border-slate-300 dark:border-slate-700 shadow-xs">
                    CGK &rarr; DPS (B738)
                </button>
                <button type="button" @click="applyPreset('cgk-upg')"
                        class="px-2.5 py-1 text-xs font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-md transition border border-slate-300 dark:border-slate-700 shadow-xs">
                    CGK &rarr; UPG (A320)
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid: Left = Form Inputs, Right = Results & Map -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ================= LEFT COLUMN: FORM CONTROLS (5 cols) ================= -->
        <div class="lg:col-span-5 space-y-5">
            <form @submit.prevent="submitCalculation" class="space-y-5">

                <!-- 1. Flight Route Card -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                        <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">1. Rute Penerbangan</h3>
                        <button type="button" @click="swapAirports"
                                class="text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Tukar Asal/Tujuan
                        </button>
                    </div>

                    <!-- Departure Airport -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Bandara Keberangkatan (Asal) <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.departure_airport_id" @change="onRouteChanged" required
                                class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                            <option value="">-- Pilih Bandara Asal --</option>
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
                            Bandara Kedatangan (Tujuan) <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.arrival_airport_id" @change="onRouteChanged" required
                                class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                            <option value="">-- Pilih Bandara Tujuan --</option>
                            @foreach($airports as $ap)
                                <option value="{{ $ap->id }}" data-lat="{{ $ap->latitude }}" data-lng="{{ $ap->longitude }}" data-iata="{{ $ap->iata_code }}">
                                    {{ $ap->iata_code }} — {{ $ap->name }} ({{ $ap->city }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Realtime Route Distance Preview Badge -->
                    <div x-show="previewDistance.gcd > 0" x-cloak class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700/60 text-xs space-y-1">
                        <div class="flex justify-between text-slate-600 dark:text-slate-300">
                            <span>Jarak Lingkar Besar (GCD):</span>
                            <strong class="font-mono" x-text="previewDistance.gcd.toLocaleString() + ' km'"></strong>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-300">
                            <span>Koreksi Deviasi ICAO:</span>
                            <span class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold" x-text="'+' + previewDistance.correction + ' km'"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-1 text-slate-800 dark:text-slate-100 font-semibold">
                            <span>Estimasi Jarak Rute Terkoreksi:</span>
                            <span class="font-mono text-[#0B5A9E] dark:text-sky-400" x-text="previewDistance.adjusted.toLocaleString() + ' km'"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Aircraft & Capacity Card -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                        <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">2. Armada & Kapasitas Kursi</h3>
                        <span class="text-xs text-slate-400">Master Data Pesawat</span>
                    </div>

                    <!-- Aircraft Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Tipe Pesawat
                        </label>
                        <select x-model="form.aircraft_id" @change="onAircraftChanged"
                                class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-[#0B5A9E] focus:outline-none">
                            <option value="">-- Kustom / Input Manual --</option>
                            @foreach($aircraft as $ac)
                                <option value="{{ $ac->id }}"
                                        data-seats="{{ $ac->y_seats }}"
                                        data-paxratio="{{ $ac->passenger_to_freight_factor }}"
                                        data-fuelburn="{{ $ac->fuel_burn_factor }}"
                                        data-co2="{{ $ac->co2_factor }}">
                                    {{ $ac->manufacturer }} {{ $ac->model }} ({{ $ac->icao_type }}) — {{ $ac->y_seats }} Kursi
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Y-Seats -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Kapasitas Kursi Kelas Ekonomi (Y-Seats) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               min="1"
                               x-model.number="form.y_seats"
                               required
                               placeholder="Contoh: 180"
                               class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-1 focus:ring-[#0B5A9E] focus:outline-none">
                    </div>
                </div>

                <!-- 3. Fuel & Operational Factors Card -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                        <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">3. Bahan Bakar & Parameter Beban</h3>
                        <span class="text-xs text-slate-400">Parameter ICAO</span>
                    </div>

                    <!-- Total Fuel Burn -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Total Bahan Bakar Terbakar (kg Fuel) <span class="text-red-500">*</span>
                            </label>
                            <button type="button" x-show="canEstimateFuel" @click="estimateFuelFromDistance" x-cloak
                                    class="text-xs text-[#0B5A9E] dark:text-sky-400 hover:underline font-semibold">
                                &approx; Hitung dari Jarak
                            </button>
                        </div>
                        <div class="relative">
                            <input type="number"
                                   step="0.1"
                                   min="0.1"
                                   x-model.number="form.total_fuel_kg"
                                   required
                                   placeholder="Contoh: 5000"
                                   class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-1 focus:ring-[#0B5A9E] focus:outline-none pr-12">
                            <span class="absolute right-3.5 top-2 text-xs text-slate-400 font-mono">kg</span>
                        </div>
                    </div>

                    <!-- Passenger to Freight Factor -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Passenger-to-Freight Ratio (0.0 – 1.0)
                            </label>
                            <span class="text-xs font-mono font-bold text-[#0B5A9E] dark:text-sky-400" x-text="(form.passenger_to_freight_factor * 100).toFixed(0) + '% Pax'"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-2 mb-2">
                            <button type="button" @click="form.passenger_to_freight_factor = 0.80"
                                    :class="form.passenger_to_freight_factor === 0.80 ? 'bg-[#0B5A9E] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    class="py-1 text-xs font-semibold rounded transition">
                                80% (ICAO)
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 0.85"
                                    :class="form.passenger_to_freight_factor === 0.85 ? 'bg-[#0B5A9E] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    class="py-1 text-xs font-semibold rounded transition">
                                85%
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 0.90"
                                    :class="form.passenger_to_freight_factor === 0.90 ? 'bg-[#0B5A9E] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    class="py-1 text-xs font-semibold rounded transition">
                                90%
                            </button>
                            <button type="button" @click="form.passenger_to_freight_factor = 1.00"
                                    :class="form.passenger_to_freight_factor === 1.00 ? 'bg-[#0B5A9E] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    class="py-1 text-xs font-semibold rounded transition">
                                100%
                            </button>
                        </div>
                    </div>

                    <!-- Passenger Load Factor -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Tingkat Keterisian Kursi (Load Factor)
                            </label>
                            <span class="text-xs font-mono font-bold text-[#0B5A9E] dark:text-sky-400" x-text="(form.passenger_load_factor * 100).toFixed(0) + '% (' + estimatedPaxCount + ' Penumpang)'"></span>
                        </div>
                        <input type="range" min="0.10" max="1.00" step="0.01"
                               x-model.number="form.passenger_load_factor"
                               class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-[#0B5A9E]">
                    </div>

                    <!-- CO2 Factor -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Faktor Konversi CO₂ (kg CO₂ / kg Fuel)
                        </label>
                        <input type="number"
                               step="0.01"
                               x-model.number="form.co2_factor"
                               required
                               placeholder="3.16"
                               class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-1 focus:ring-[#0B5A9E] focus:outline-none">
                        <span class="text-[11px] text-slate-400 mt-1 block">Standar resmi ICAO Doc 9889 = 3.16</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-3">
                    <button type="submit"
                            :disabled="isLoading"
                            class="btn-primary flex-1 h-10 text-xs sm:text-sm font-semibold rounded-lg shadow-xs">
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Menghitung Emisi...' : 'Hitung Estimasi Emisi Karbon'"></span>
                    </button>
                    <button type="button" @click="resetForm" class="btn-secondary h-10 px-4 text-xs sm:text-sm font-medium rounded-lg shadow-xs">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= RIGHT COLUMN: RESULTS, MAP & BREAKDOWN (7 cols) ================= -->
        <div class="lg:col-span-7 space-y-5">

            <!-- Initial Placeholder (when not calculated yet) -->
            <div x-show="!hasResult && !isLoading" class="card p-8 flex flex-col items-center justify-center text-center min-h-[460px] space-y-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 flex items-center justify-center text-[#0B5A9E] dark:text-sky-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="max-w-md space-y-1.5">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Siap Menghitung Estimasi Emisi Karbon</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pilih bandara asal dan tujuan pada formulir di sebelah kiri atau klik tombol <strong>"Contoh ICAO"</strong> di bagian atas untuk melihat demonstrasi kalkulasi instan.
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" @click="applyPreset('icao')" class="btn-primary">
                        Gunakan Contoh Resmi ICAO (79.00 kg/pax)
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="isLoading" class="card p-8 flex flex-col items-center justify-center text-center min-h-[460px] space-y-3">
                <div class="w-8 h-8 border-3 border-[#0B5A9E] border-t-transparent rounded-full animate-spin"></div>
                <div class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Memproses Algoritma ICAO...</div>
                <div class="text-xs text-slate-400">Menghitung jarak Haversine, deviasi rute, dan alokasi penumpang.</div>
            </div>

            <!-- Result Cards & Dashboard (when calculated) -->
            <div x-show="hasResult && !isLoading" x-cloak class="space-y-5">

                <!-- Top Route & Benchmark Banner -->
                <div class="card p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-black font-mono text-slate-900 dark:text-white" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code"></span>
                            <span class="text-xs text-slate-500 dark:text-slate-400" x-text="resultData.departure.city + ' ke ' + resultData.arrival.city"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Efficiency Pill -->
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="getEfficiencyBadgeClass(resultData.result.co2_per_passenger_kg)"
                                  x-text="getEfficiencyLabel(resultData.result.co2_per_passenger_kg)">
                            </span>
                        </div>
                    </div>

                    <!-- 4 Main KPI Result Metrics -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                        <!-- Metric 1: CO2 per Pax -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CO₂ / Penumpang</div>
                            <div class="text-2xl font-black font-mono text-[#0B5A9E] dark:text-sky-400 mt-1" x-text="resultData.result.co2_per_passenger_kg.toFixed(2)"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5">kg CO₂ / pax</div>
                        </div>

                        <!-- Metric 2: Total CO2 Flight -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Emisi Flight</div>
                            <div class="text-2xl font-black font-mono text-slate-800 dark:text-slate-100 mt-1" x-text="resultData.result.co2_total_tonnes.toFixed(2)"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5" x-text="resultData.result.co2_total_kg.toLocaleString() + ' kg'"></div>
                        </div>

                        <!-- Metric 3: Passenger Count -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Est. Penumpang</div>
                            <div class="text-2xl font-black font-mono text-slate-800 dark:text-slate-100 mt-1" x-text="resultData.result.passenger_count"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5" x-text="'Dari ' + resultData.result.y_seats + ' kursi (' + (resultData.result.passenger_load_factor * 100).toFixed(0) + '%)'"></div>
                        </div>

                        <!-- Metric 4: Pax Fuel Allocation -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fuel Penumpang</div>
                            <div class="text-2xl font-black font-mono text-slate-800 dark:text-slate-100 mt-1" x-text="resultData.result.passenger_fuel_kg.toLocaleString()"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5" x-text="'Dari ' + resultData.result.total_fuel_kg.toLocaleString() + ' kg total'"></div>
                        </div>
                    </div>
                </div>

                <!-- Distance & Operational Route Details -->
                <div class="card p-5 space-y-3">
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Parameter Jarak Tempuh Rute</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-slate-400 block">Jarak Lingkar Besar (GCD)</span>
                            <span class="text-base font-bold font-mono text-slate-800 dark:text-slate-200" x-text="resultData.result.distance_gcd_km.toLocaleString() + ' km'"></span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-slate-400 block">Koreksi Deviasi ICAO</span>
                            <span class="text-base font-bold font-mono text-emerald-600 dark:text-emerald-400" x-text="'+' + resultData.result.correction_km + ' km'"></span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-slate-400 block">Jarak Terkoreksi ICAO</span>
                            <span class="text-base font-bold font-mono text-brand-600 dark:text-brand-400" x-text="resultData.result.distance_adjusted_km.toLocaleString() + ' km'"></span>
                        </div>
                    </div>

                    <!-- Operational Route Match from Master Data (if any) -->
                    <div x-show="resultData.operational_route" class="p-3 bg-brand-50/60 dark:bg-brand-900/20 rounded-lg border border-brand-200 dark:border-brand-800/40 text-xs flex items-center justify-between">
                        <div>
                            <span class="font-bold text-brand-700 dark:text-brand-300">Tersinkronisasi dengan Rute Master Data:</span>
                            <span class="text-slate-700 dark:text-slate-300 ml-1" x-text="resultData.operational_route?.name + ' (' + resultData.operational_route?.source + ')'"></span>
                        </div>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-100" x-text="resultData.operational_route?.distance_km + ' km'"></span>
                    </div>
                </div>

                <!-- Interactive Leaflet Map Visualizer -->
                <div class="card overflow-hidden flex flex-col">
                    <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-2 text-xs bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="mapMode === 'local' ? 'bg-emerald-500' : 'bg-brand-500'"></span>
                            <span class="font-semibold text-slate-800 dark:text-slate-100">Peta Visual Jalur Penerbangan</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium"
                                  :class="mapMode === 'local' ? 'bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800/50'"
                                  x-text="mapMode === 'local' ? 'Peta Vektor Lokal (Offline)' : 'Mode Satelit / CDN'">
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-slate-500 dark:text-slate-400 font-semibold" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code"></span>
                            <button type="button" @click="toggleMapMode"
                                    class="px-2.5 py-1 text-[11px] font-medium bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-lg transition text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 flex items-center gap-1.5 shadow-xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span x-text="mapMode === 'local' ? 'Opsi: Beralih ke CDN' : 'Opsi: Beralih ke Vektor Lokal'"></span>
                            </button>
                        </div>
                    </div>
                    <div id="calculator-map" class="w-full h-80 bg-[#e0f2fe] dark:bg-[#0b1329] transition-colors relative"></div>
                </div>

                <!-- ICAO Step-by-Step Formula Breakdown -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/80 pb-2">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Audit Perhitungan Formula ICAO</h3>
                        <span class="text-xs font-mono text-brand-600 dark:text-brand-400 font-semibold">CO₂/pax = CF × (Fuel × P/F) / (Seats × LF)</span>
                    </div>

                    <div class="space-y-2.5 text-xs font-mono">
                        <!-- Step 1 -->
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/80">
                            <span class="text-slate-400 block font-sans text-[11px] mb-0.5">Langkah 1: Alokasi Bahan Bakar Penumpang (Passenger Fuel)</span>
                            <span class="text-slate-700 dark:text-slate-300">
                                Fuel_Pax = <strong class="text-slate-900 dark:text-white" x-text="resultData.result.total_fuel_kg.toLocaleString() + ' kg'"></strong> &times; <strong class="text-slate-900 dark:text-white" x-text="resultData.result.passenger_to_freight_factor"></strong>
                                = <strong class="text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.passenger_fuel_kg.toLocaleString() + ' kg'"></strong>
                            </span>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/80">
                            <span class="text-slate-400 block font-sans text-[11px] mb-0.5">Langkah 2: Estimasi Total Penumpang Terangkut</span>
                            <span class="text-slate-700 dark:text-slate-300">
                                Pax_Count = <strong class="text-slate-900 dark:text-white" x-text="resultData.result.y_seats + ' kursi'"></strong> &times; <strong class="text-slate-900 dark:text-white" x-text="resultData.result.passenger_load_factor"></strong>
                                = <strong class="text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.passenger_count + ' orang'"></strong>
                            </span>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/80">
                            <span class="text-slate-400 block font-sans text-[11px] mb-0.5">Langkah 3: Emisi CO₂ per Penumpang</span>
                            <span class="text-slate-700 dark:text-slate-300">
                                CO₂/pax = <strong class="text-slate-900 dark:text-white" x-text="resultData.result.co2_factor"></strong> &times; (<strong class="text-slate-900 dark:text-white" x-text="resultData.result.passenger_fuel_kg.toLocaleString()"></strong> / <strong class="text-slate-900 dark:text-white" x-text="resultData.result.passenger_count"></strong>)
                                = <strong class="text-emerald-600 dark:text-emerald-400 text-sm" x-text="resultData.result.co2_per_passenger_kg.toFixed(4) + ' kg CO₂/pax'"></strong>
                            </span>
                        </div>

                        <!-- Step 4 -->
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/80">
                            <span class="text-slate-400 block font-sans text-[11px] mb-0.5">Langkah 4: Total Emisi CO₂ Keseluruhan Penerbangan</span>
                            <span class="text-slate-700 dark:text-slate-300">
                                Total_CO₂ = <strong class="text-slate-900 dark:text-white" x-text="resultData.result.total_fuel_kg.toLocaleString() + ' kg'"></strong> &times; <strong class="text-slate-900 dark:text-white" x-text="resultData.result.co2_factor"></strong>
                                = <strong class="text-slate-900 dark:text-white" x-text="resultData.result.co2_total_kg.toLocaleString() + ' kg'"></strong>
                                (<strong class="text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.co2_total_tonnes.toFixed(2) + ' Tonne'"></strong>)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Export & Action Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <div class="flex gap-2">
                        <button type="button" @click="copySummary"
                                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Ringkasan'"></span>
                        </button>
                        <button type="button" @click="window.print()"
                                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Laporan
                        </button>
                    </div>

                    <a href="{{ route('flights.index') }}"
                       class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Buka Monitoring Penerbangan &rarr;
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
        mapMode: 'cdn',
        localGeoJsonData: null,
        worldGeoJsonData: null,
        tileLayerInstance: null,
        resizeObs: null,
        themeListenerAttached: false,

        toggleMapMode() {
            this.mapMode = this.mapMode === 'local' ? 'cdn' : 'local';
            if (this.hasResult && this.resultData?.departure?.lat) {
                this.renderMap(this.resultData.departure, this.resultData.arrival);
            }
        },

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
                alert('Silakan pilih bandara asal dan bandara tujuan.');
                return;
            }
            if (this.form.departure_airport_id === this.form.arrival_airport_id) {
                alert('Bandara asal dan bandara tujuan tidak boleh sama.');
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
                    alert(data.error || 'Terjadi kesalahan saat menghitung emisi.');
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
                alert('Gagal menghubungi server: ' + err.message);
                this.isLoading = false;
            }
        },

        async renderMap(dep, arr) {
            const mapEl = document.getElementById('calculator-map');
            if (!mapEl) return;

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

            if (this.mapMode === 'local') {
                // ==================== 100% LOCAL VECTOR MODE ====================
                if (!this.localGeoJsonData) {
                    try {
                        const [idRes, worldRes] = await Promise.all([
                            fetch('{{ asset('data/indonesia-provinces.geojson') }}').then(r => r.json()),
                            fetch('{{ asset('data/world-countries.geojson') }}').then(r => r.json()).catch(() => null)
                        ]);
                        this.localGeoJsonData = idRes;
                        this.worldGeoJsonData = worldRes;
                    } catch(e) {
                        console.error('Local geojson error:', e);
                    }
                }

                // Render world landmass (surrounding ASEAN countries)
                if (this.worldGeoJsonData) {
                    L.geoJSON(this.worldGeoJsonData, {
                        style: {
                            fillColor: isDark ? '#111827' : '#cbd5e1',
                            fillOpacity: isDark ? 0.7 : 0.6,
                            color: isDark ? '#1f2937' : '#94a3b8',
                            weight: 0.8
                        }
                    }).addTo(this.mapInstance);
                }

                // Render Indonesian provinces (Local GeoJSON)
                if (this.localGeoJsonData) {
                    L.geoJSON(this.localGeoJsonData, {
                        style: {
                            fillColor: isDark ? '#1e293b' : '#ffffff',
                            fillOpacity: isDark ? 0.95 : 1,
                            color: isDark ? '#0284c7' : '#0B5A9E',
                            weight: 1.2,
                            dashArray: '3, 3'
                        }
                    }).addTo(this.mapInstance);
                }

                // Major Indonesian reference cities & airport points
                const referenceCities = [
                    { name: 'Jakarta', iata: 'CGK', lat: -6.1256, lng: 106.6558 },
                    { name: 'Surabaya', iata: 'SUB', lat: -7.3798, lng: 112.7875 },
                    { name: 'Denpasar', iata: 'DPS', lat: -8.7482, lng: 115.1672 },
                    { name: 'Medan', iata: 'KNO', lat: 3.6422, lng: 98.8853 },
                    { name: 'Makassar', iata: 'UPG', lat: -5.0617, lng: 119.5540 },
                    { name: 'Balikpapan', iata: 'BPN', lat: -1.2683, lng: 116.8944 },
                    { name: 'Yogyakarta', iata: 'YIA', lat: -7.9072, lng: 110.0544 },
                    { name: 'Semarang', iata: 'SRG', lat: -6.9744, lng: 110.3750 },
                    { name: 'Batam', iata: 'BTH', lat: 1.1211, lng: 104.1189 },
                    { name: 'Palembang', iata: 'PLM', lat: -2.8986, lng: 104.7003 },
                    { name: 'Banjarmasin', iata: 'BDJ', lat: -3.4422, lng: 114.7628 },
                    { name: 'Manado', iata: 'MDC', lat: 1.5492, lng: 124.9258 },
                    { name: 'Ambon', iata: 'AMQ', lat: -3.7083, lng: 128.0894 },
                    { name: 'Jayapura', iata: 'DJJ', lat: -2.5769, lng: 140.5161 },
                    { name: 'Kupang', iata: 'KOE', lat: -10.1714, lng: 123.6708 },
                    { name: 'Lombok', iata: 'LOP', lat: -8.7589, lng: 116.2764 },
                    { name: 'Pekanbaru', iata: 'PKU', lat: 0.4608, lng: 101.4447 },
                    { name: 'Pontianak', iata: 'PNK', lat: -0.1506, lng: 109.4039 },
                    { name: 'Padang', iata: 'PDG', lat: -0.7867, lng: 100.2806 },
                    { name: 'Sorong', iata: 'SOQ', lat: -0.8906, lng: 131.2869 }
                ];

                referenceCities.forEach(c => {
                    if (c.iata === dep.iata_code || c.iata === arr.iata_code) return;

                    const cityDiv = L.divIcon({
                        className: 'city-vector-marker',
                        html: `
                            <div style="display:flex;align-items:center;gap:4px;white-space:nowrap;transform:translate(-50%,-50%);pointer-events:none;">
                                <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${isDark ? '#38bdf8' : '#0B5A9E'};box-shadow:0 0 4px ${isDark ? '#38bdf8' : '#0B5A9E'};"></span>
                                <span style="font-size:9px;font-weight:700;font-family:Inter,sans-serif;color:${isDark ? '#94a3b8' : '#475569'};background:${isDark ? 'rgba(15,23,42,0.92)' : 'rgba(255,255,255,0.92)'};padding:1px 5px;border-radius:3px;border:1px solid ${isDark ? '#334155' : '#cbd5e1'};box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    ${c.name} (${c.iata})
                                </span>
                            </div>
                        `,
                        iconSize: [0, 0]
                    });
                    L.marker([c.lat, c.lng], { icon: cityDiv, interactive: false }).addTo(this.mapInstance);
                });

            } else {
                // ==================== TILE MODE (100% WATERMARK-FREE) ====================
                const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';

                this.tileLayerInstance = L.tileLayer(tileUrl, {
                    subdomains: 'abc',
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(this.mapInstance);
            }


            // High-visibility Departure Pin (Asal)
            const depDiv = L.divIcon({
                className: 'dep-active-marker',
                html: `
                    <div style="display:flex;flex-direction:column;align-items:center;transform:translate(-50%,-100%);pointer-events:none;">
                        <div style="display:flex;align-items:center;gap:4px;background:#0B5A9E;color:#ffffff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:6px;box-shadow:0 3px 10px rgba(11,90,158,0.5);border:1px solid #38bdf8;white-space:nowrap;font-family:Inter,sans-serif;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#38bdf8;display:inline-block;"></span>
                            ${dep.iata_code} • ${dep.city || dep.name} (Asal)
                        </div>
                        <div style="width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid #0B5A9E;"></div>
                        <div style="width:8px;height:8px;border-radius:50%;background:#0B5A9E;border:2px solid #ffffff;box-shadow:0 0 6px rgba(11,90,158,0.9);margin-top:-3px;"></div>
                    </div>
                `,
                iconSize: [0, 0]
            });
            L.marker([dep.lat, dep.lng], { icon: depDiv }).addTo(this.mapInstance);

            // High-visibility Arrival Pin (Tujuan)
            const arrDiv = L.divIcon({
                className: 'arr-active-marker',
                html: `
                    <div style="display:flex;flex-direction:column;align-items:center;transform:translate(-50%,-100%);pointer-events:none;">
                        <div style="display:flex;align-items:center;gap:4px;background:#059669;color:#ffffff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:6px;box-shadow:0 3px 10px rgba(5,150,105,0.5);border:1px solid #34d399;white-space:nowrap;font-family:Inter,sans-serif;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;"></span>
                            ${arr.iata_code} • ${arr.city || arr.name} (Tujuan)
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
            if (co2Pax < 90) return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
            if (co2Pax <= 140) return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
        },

        getEfficiencyLabel(co2Pax) {
            if (co2Pax < 90) return 'Tingkat Emisi Sangat Efisien';
            if (co2Pax <= 140) return 'Tingkat Emisi Normal / Moderat';
            return 'Tingkat Emisi Tinggi';
        },

        copySummary() {
            if (!this.hasResult) return;
            const r = this.resultData;
            const text = `ACE CARBON EMISSION REPORT\n` +
                `Rute: ${r.departure.iata_code} (${r.departure.city}) -> ${r.arrival.iata_code} (${r.arrival.city})\n` +
                `Jarak GCD: ${r.result.distance_gcd_km} km | Koreksi ICAO: +${r.result.correction_km} km | Jarak Efektif: ${r.result.distance_adjusted_km} km\n` +
                `Total Fuel: ${r.result.total_fuel_kg} kg | Passenger Fuel: ${r.result.passenger_fuel_kg} kg (${(r.result.passenger_to_freight_factor*100).toFixed(0)}%)\n` +
                `Kapasitas Kursi: ${r.result.y_seats} | Load Factor: ${(r.result.passenger_load_factor*100).toFixed(0)}% | Est. Pax: ${r.result.passenger_count}\n` +
                `------------------------------------\n` +
                `CO2 per Penumpang: ${r.result.co2_per_passenger_kg.toFixed(2)} kg CO2/pax\n` +
                `Total Emisi Penerbangan: ${r.result.co2_total_tonnes.toFixed(2)} Tonne (${r.result.co2_total_kg.toLocaleString()} kg CO2)\n` +
                `Metodologi: Standar ICAO Doc 9889 & CORSIA`;

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
