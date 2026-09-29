@extends('layouts.app')

@section('title', 'Kalkulator Pengurangan Emisi Karbon (CO₂) Rute RNAV')
@section('page-title', 'Kalkulator Pengurangan Emisi Karbon')
@section('page-subtitle', 'Aviation Emission Reduction & Route Efficiency Calculation Based on AirNav Standard')

@section('content')
<div class="p-4 md:p-6 space-y-6" x-data="carbonCalculatorApp()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-sky-300 border border-blue-200 dark:border-blue-800">
                    AirNav Indonesia
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">RNAV Route Efficiency & Emission Standard</span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                Kalkulator Pengurangan Emisi Karbon (CO₂) Rute RNAV
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Kalkulasi metrik efisiensi rute penerbangan, penghematan bahan bakar avtur, estimasi biaya operasional, dan serapan emisi pohon.
            </p>
        </div>

        <!-- Quick Route Presets -->
        <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-400 font-medium hidden md:inline">Preset Rute:</span>
            <div class="inline-flex p-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <button type="button" @click="applyPreset('cgk-dps')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    CGK &rarr; DPS (TW II)
                </button>
                <button type="button" @click="applyPreset('cgk-sub')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    CGK &rarr; SUB (TW II)
                </button>
                <button type="button" @click="applyPreset('cgk-upg')"
                        class="px-2.5 py-1 font-medium rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                    CGK &rarr; UPG (TW II)
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
                            1. Rute Penerbangan
                        </span>
                        <button type="button" @click="swapAirports"
                                class="text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:underline flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Tukar Rute
                        </button>
                    </div>

                    <!-- Departure Airport -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Bandara Asal (Origin) <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.departure_airport_id" @change="onRouteChanged" required
                                class="w-full form-select-base">
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
                            Bandara Tujuan (Destination) <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.arrival_airport_id" @change="onRouteChanged" required
                                class="w-full form-select-base">
                            <option value="">-- Pilih Bandara Tujuan --</option>
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
                            <span>Estimasi Nautical Miles (Nm):</span>
                            <span class="font-semibold text-[#0B5A9E] dark:text-sky-400" x-text="(previewDistance.gcd / form.nm_to_km).toFixed(1) + ' Nm'"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Route Optimization (sd & tm) Section -->
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            2. Parameter Penghematan Rute (sd & tm)
                        </span>
                    </div>

                    <!-- Saved Distance (sd) in Nautical Miles -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Penghematan Jarak (sd) <span class="text-red-500">*</span>
                            </label>
                            <span class="text-xs font-mono font-medium text-emerald-600 dark:text-emerald-400"
                                  x-text="'= ' + (form.saved_distance_nm * form.nm_to_km).toFixed(4) + ' km / penerbangan'"></span>
                        </div>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   x-model.number="form.saved_distance_nm"
                                   required
                                   placeholder="12.1"
                                   class="w-full form-input-base pr-20 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">Nm</span>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">1 Nautical Mile (Nm) = 1,852 km</span>
                    </div>

                    <!-- Saved Time (tm) in Minutes -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Penghematan Waktu Tempuh (tm) <span class="text-red-500">*</span>
                            </label>
                            <span class="text-xs font-mono font-medium text-blue-600 dark:text-blue-400"
                                  x-text="'= ' + Math.round(form.saved_time_minutes * 60) + ' detik / penerbangan'"></span>
                        </div>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   x-model.number="form.saved_time_minutes"
                                   required
                                   placeholder="1.7"
                                   class="w-full form-input-base pr-20 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">menit</span>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Rata-rata waktu terbang dihemat per penerbangan</span>
                    </div>
                </div>

                <!-- 3. Traffic Frequency & Evaluation Period (fl & d) Section -->
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            3. Frekuensi & Periode Evaluasi (fl & d)
                        </span>
                        <span class="text-xs font-mono font-bold text-[#0B5A9E] dark:text-sky-400"
                              x-text="'Total: ' + (Math.round(form.flights_per_day * form.period_days)).toLocaleString() + ' Flights'"></span>
                    </div>

                    <!-- Average Flights Per Day (fl) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Rata-rata Frekuensi Penerbangan (fl) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   step="1"
                                   min="0.1"
                                   x-model.number="form.flights_per_day"
                                   required
                                   placeholder="50"
                                   class="w-full form-input-base pr-28 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">flights / hari</span>
                        </div>
                    </div>

                    <!-- Period Presets Quick Buttons -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Periode Evaluasi Kalender
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-lg text-xs">
                            <button type="button" @click="setPeriod(30, '1 Bulan (30 hari)')"
                                    :class="form.period_days === 30 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                1 Bulan (30h)
                            </button>
                            <button type="button" @click="setPeriod(90, 'Triwulan I (90 hari)')"
                                    :class="form.period_days === 90 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                TW I (90h)
                            </button>
                            <button type="button" @click="setPeriod(91, 'Triwulan II (91 hari)')"
                                    :class="form.period_days === 91 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                TW II (91h)
                            </button>
                            <button type="button" @click="setPeriod(92, 'Triwulan III / IV (92 hari)')"
                                    :class="form.period_days === 92 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                TW III/IV (92h)
                            </button>
                            <button type="button" @click="setPeriod(182, 'Semester (182 hari)')"
                                    :class="form.period_days === 182 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                Semester (182h)
                            </button>
                            <button type="button" @click="setPeriod(365, '1 Tahun (365 hari)')"
                                    :class="form.period_days === 365 ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                                    class="py-1.5 rounded transition text-center">
                                1 Tahun (365h)
                            </button>
                        </div>
                    </div>

                    <!-- Total Evaluation Days (d) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Jumlah Hari Kalender (d) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   min="1"
                                   x-model.number="form.period_days"
                                   @input="form.period_label = form.period_days + ' Hari'"
                                   required
                                   placeholder="91"
                                   class="w-full form-input-base pr-16 font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">hari</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Standard Operational Parameters & Assumptions (AirNav Standard) -->
                <div class="p-5 space-y-3" x-data="{ showAdvanced: false }">
                    <button type="button" @click="showAdvanced = !showAdvanced"
                            class="w-full flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            4. Parameter & Asumsi Standar Baku
                        </span>
                        <svg class="w-4 h-4 transform transition-transform" :class="showAdvanced ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="showAdvanced" x-collapse x-cloak class="space-y-3 pt-2">
                        <!-- Fuel Burn Factor (fb) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Fuel Burn Factor (fb)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" x-model.number="form.fuel_burn_rate" class="w-full form-input-base pr-28 font-mono">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">kg BBM / km</span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Standar AirNav: 3,59 kg avtur per km</span>
                        </div>

                        <!-- Emission Factor (ef) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Emission Factor CO₂ (ef)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" x-model.number="form.co2_factor" class="w-full form-input-base pr-32 font-mono">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">kg CO₂ / kg BBM</span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Standar Avtur Jet-A1: 3,15 kg CO₂ per kg BBM</span>
                        </div>

                        <!-- Cost Index / Operasional (ci) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Cost Index Operasional (ci)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.1" x-model.number="form.cost_index" class="w-full form-input-base pr-28 font-mono">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">USD / menit</span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Standar operasional: 25 USD per menit</span>
                        </div>

                        <!-- Exchange Rate (fx) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Kurs Tukar (fx)
                            </label>
                            <div class="relative">
                                <input type="number" step="10" x-model.number="form.exchange_rate" class="w-full form-input-base pr-24 font-mono">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">IDR / USD</span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Standar kurs konversi: Rp 15.600 per USD</span>
                        </div>

                        <!-- Tree Absorption Factor -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Faktor Serapan Pohon
                            </label>
                            <div class="relative">
                                <input type="number" step="0.1" x-model.number="form.tree_absorption_factor" class="w-full form-input-base pr-36 font-mono">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-mono pointer-events-none">kg CO₂ / pohon / th</span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">1 pohon menyerap 21 kg CO₂ per tahun</span>
                        </div>
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
                        <span x-text="isLoading ? 'Menghitung Pengurangan Emisi...' : 'Hitung Pengurangan Emisi'"></span>
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
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-[#0B5A9E] dark:text-sky-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div class="max-w-md space-y-1">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Siap Menghitung Pengurangan Emisi Rute</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pilih bandara rute, masukkan penghematan jarak (sd), penghematan waktu (tm), frekuensi harian, dan periode evaluasi untuk menghasilkan kalkulasi emisi akurat.
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" @click="applyPreset('cgk-dps')" class="btn-secondary">
                        Coba Skenario Simulasi: CGK &rarr; DPS (TW II)
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="isLoading" class="card p-10 flex flex-col items-center justify-center text-center min-h-[460px] space-y-3">
                <div class="w-7 h-7 border-2 border-[#0B5A9E] border-t-transparent rounded-full animate-spin"></div>
                <div class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Menghitung Efisiensi Rute...</div>
                <div class="text-xs text-slate-400 font-mono">Memproses formula kalkulasi baku AirNav Indonesia</div>
            </div>

            <!-- Result Cards & Dashboard (when calculated) -->
            <div x-show="hasResult && !isLoading" x-cloak class="space-y-5">

                <!-- 1. Executive Summary & Hero KPI Card -->
                <div class="card p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold font-mono text-slate-900 dark:text-white" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code"></span>
                            <span class="text-xs text-slate-500 dark:text-slate-400" x-text="resultData.departure.city + ' (' + resultData.departure.iata_code + ') ke ' + resultData.arrival.city + ' (' + resultData.arrival.iata_code + ')'"></span>
                        </div>
                        <span class="px-2.5 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                            Efisiensi Rute RNAV
                        </span>
                    </div>

                    <!-- HERO METRIC: TOTAL EMISI CO2 DIKURANGI -->
                    <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-[11px] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400">
                                Total Emisi CO₂ Dikurangi
                            </div>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-4xl sm:text-5xl font-extrabold font-mono text-[#0B5A9E] dark:text-sky-400 tracking-tight"
                                      x-text="resultData.result.co2_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})"></span>
                                <span class="text-xl font-bold text-slate-700 dark:text-slate-300 font-mono">Ton CO₂</span>
                            </div>
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                Setara <span class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="resultData.result.co2_saved_kg.toLocaleString('id-ID') + ' kg CO₂'"></span>
                                untuk <span class="font-mono font-semibold" x-text="resultData.result.total_flights.toLocaleString('id-ID') + ' penerbangan'"></span>
                                (<span x-text="resultData.result.period_label"></span>)
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 sm:text-right border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-slate-800 pt-3 sm:pt-0 sm:pl-5 space-y-1">
                            <div>Standar: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">AirNav Indonesia</span></div>
                            <div>Fuel Burn Rate: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300" x-text="resultData.result.fuel_burn_rate + ' kg/km'"></span></div>
                            <div>Emission Factor: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300" x-text="resultData.result.co2_factor + ' kg CO₂/kg BBM'"></span></div>
                            <div class="text-[11px] text-slate-400 font-mono" x-text="'Hemat Jarak: ' + resultData.result.saved_distance_nm + ' Nm (' + resultData.result.saved_distance_km + ' km)'"></div>
                        </div>
                    </div>

                    <!-- 4 Main Secondary KPI Metrics -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        <!-- 1. Bahan Bakar Avtur Dihemat -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bahan Bakar Avtur Dihemat</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.fuel_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})"></span>
                                <span class="text-xs font-normal text-slate-500">Ton</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="resultData.result.fuel_saved_kg.toLocaleString('id-ID') + ' kg BBM'"></div>
                        </div>

                        <!-- 2. CO2 per Single Flight -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CO₂ per Penerbangan</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.co2_per_flight_kg.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})"></span>
                                <span class="text-xs font-normal text-slate-500">kg</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="'Hemat ' + resultData.result.saved_distance_km + ' km/flt'"></div>
                        </div>

                        <!-- 3. Penghematan Biaya Operasional -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hemat Biaya Operasional</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="formatCurrencyIdr(resultData.result.cost_saved_idr)"></span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="'$ ' + resultData.result.cost_saved_usd.toLocaleString('id-ID') + ' USD'"></div>
                        </div>

                        <!-- 4. Ekuivalensi Serapan Pohon -->
                        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ekuivalen Serapan Pohon</div>
                            <div class="text-lg sm:text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                                <span x-text="resultData.result.tree_equivalence.toLocaleString('id-ID')"></span>
                                <span class="text-xs font-normal text-slate-500">pohon</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">21 kg CO₂ / pohon / th</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Interactive Map Visualizer (TETAP DIPERTAHANKAN UTUH) -->
                <div class="card overflow-hidden flex flex-col">
                    <div class="px-4 py-2.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs bg-slate-50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#0B5A9E]"></span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Peta Rute Penerbangan</span>
                        </div>
                        <span class="font-mono text-slate-500 dark:text-slate-400" x-text="resultData.departure.iata_code + ' → ' + resultData.arrival.iata_code + ' (Hemat: ' + resultData.result.saved_distance_nm + ' Nm / ' + resultData.result.saved_distance_km + ' km)'"></span>
                    </div>
                    <div id="calculator-map" class="w-full h-72 bg-[#e0f2fe] dark:bg-[#0b1329] transition-colors relative"></div>
                </div>

                <!-- 3. Audit & Calculation Table Breakdown -->
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Audit & Langkah Perhitungan Formula Baku</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Verifikasi substitusi parameter baku berdasarkan metodologi evaluasi rute AirNav Indonesia</p>
                        </div>
                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400 hidden sm:inline">
                            CO₂ (kg) = sd × 1,852 × fl × d × fb × ef
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-4 py-2.5">Tahap Perhitungan</th>
                                    <th class="px-4 py-2.5">Rumus Baku</th>
                                    <th class="px-4 py-2.5">Substitusi Nilai Input</th>
                                    <th class="px-4 py-2.5 text-right">Hasil Perhitungan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        1. Total Penerbangan Periode
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">fl × d</td>
                                    <td class="px-4 py-3" x-text="resultData.result.flights_per_day + ' flights/hari × ' + resultData.result.period_days + ' hari'"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.total_flights.toLocaleString('id-ID') + ' penerbangan'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        2. Hemat Jarak per Penerbangan
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">sd × 1,852</td>
                                    <td class="px-4 py-3" x-text="resultData.result.saved_distance_nm + ' Nm × 1,852'"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.saved_distance_km + ' km'"></td>
                                </tr>
                                <tr class="bg-blue-50/40 dark:bg-blue-950/20">
                                    <td class="px-4 py-3 font-sans font-bold text-[#0B5A9E] dark:text-sky-400">
                                        3. Total Pengurangan Emisi CO₂
                                    </td>
                                    <td class="px-4 py-3 text-[#0B5A9E] dark:text-sky-400">sd × 1,852 × fl × d × fb × ef</td>
                                    <td class="px-4 py-3 text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.saved_distance_nm + ' × 1,852 × ' + resultData.result.flights_per_day + ' × ' + resultData.result.period_days + ' × ' + resultData.result.fuel_burn_rate + ' × ' + resultData.result.co2_factor"></td>
                                    <td class="px-4 py-3 text-right font-black text-sm text-[#0B5A9E] dark:text-sky-400" x-text="resultData.result.co2_saved_kg.toLocaleString('id-ID') + ' kg (' + resultData.result.co2_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2}) + ' Ton)'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        4. Total Bahan Bakar Avtur Dihemat
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">CO₂ (kg) / ef / 1000</td>
                                    <td class="px-4 py-3" x-text="resultData.result.co2_saved_kg.toLocaleString('id-ID') + ' kg / ' + resultData.result.co2_factor + ' / 1000'"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.fuel_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2}) + ' Ton avtur'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        5. Pengurangan CO₂ per Penerbangan
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">CO₂ (kg) / Total Penerbangan</td>
                                    <td class="px-4 py-3" x-text="resultData.result.co2_saved_kg.toLocaleString('id-ID') + ' kg / ' + resultData.result.total_flights.toLocaleString('id-ID')"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.co2_per_flight_kg.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2}) + ' kg CO₂'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        6. Penghematan Biaya Operasional (USD)
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">tm × ci × fl × d</td>
                                    <td class="px-4 py-3" x-text="resultData.result.saved_time_minutes + ' mnt × $' + resultData.result.cost_index + ' × ' + resultData.result.flights_per_day + ' × ' + resultData.result.period_days"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="'$' + resultData.result.cost_saved_usd.toLocaleString('id-ID') + ' USD'"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        7. Penghematan Biaya Operasional (IDR)
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">Hemat Biaya USD × Kurs (fx)</td>
                                    <td class="px-4 py-3" x-text="'$' + resultData.result.cost_saved_usd.toLocaleString('id-ID') + ' × Rp ' + resultData.result.exchange_rate.toLocaleString('id-ID')"></td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + resultData.result.cost_saved_idr.toLocaleString('id-ID')"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-900 dark:text-slate-100">
                                        8. Ekuivalensi Serapan Pohon Tahunan
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">CO₂ (kg) / 21 kg per pohon</td>
                                    <td class="px-4 py-3" x-text="resultData.result.co2_saved_kg.toLocaleString('id-ID') + ' kg / 21'"></td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white" x-text="resultData.result.tree_equivalence.toLocaleString('id-ID') + ' pohon'"></td>
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
                            <span x-text="copied ? 'Tersalin ke Clipboard!' : 'Salin Ringkasan'"></span>
                        </button>
                        <button type="button" @click="window.print()"
                                class="btn-secondary h-9 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Laporan
                        </button>
                    </div>

                    <a href="{{ route('flights.index') }}"
                       class="text-xs font-semibold text-[#0B5A9E] dark:text-sky-400 hover:underline flex items-center gap-1">
                        Monitoring Penerbangan &rarr;
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
            saved_distance_nm: 12.1,
            saved_time_minutes: 1.7,
            flights_per_day: 50,
            period_days: 91,
            period_label: 'Triwulan II (91 hari)',
            nm_to_km: {{ $defaultNmToKm ?? 1.852 }},
            fuel_burn_rate: {{ $defaultFuelBurnRate ?? 3.59 }},
            co2_factor: {{ $defaultCo2Factor ?? 3.15 }},
            cost_index: {{ $defaultCostIndex ?? 25.0 }},
            exchange_rate: {{ $defaultExchangeRate ?? 15600.0 }},
            tree_absorption_factor: {{ $defaultTreeFactor ?? 21.0 }},
        },
        previewDistance: {
            gcd: 0,
            nm: 0,
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
        tileLayerInstance: null,
        planeAnimationId: null,
        resizeObs: null,
        themeListenerAttached: false,

        setPeriod(days, label) {
            this.form.period_days = days;
            this.form.period_label = label;
        },

        formatCurrencyIdr(val) {
            if (!val) return 'Rp 0';
            if (val >= 1000000000) {
                return 'Rp ' + (val / 1000000000).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Miliar';
            }
            if (val >= 1000000) {
                return 'Rp ' + (val / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Juta';
            }
            return 'Rp ' + val.toLocaleString('id-ID');
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
                    this.previewDistance = {
                        gcd: Math.round(gcd),
                        nm: Math.round(gcd / this.form.nm_to_km)
                    };
                    return;
                }
            }
            this.previewDistance = { gcd: 0, nm: 0 };
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

            if (preset === 'cgk-dps') {
                this.form.departure_airport_id = findAirportId('CGK') || depSelect.options[1]?.value;
                this.form.arrival_airport_id = findAirportId('DPS') || depSelect.options[2]?.value;
                this.form.saved_distance_nm = 12.1;
                this.form.saved_time_minutes = 1.7;
                this.form.flights_per_day = 50;
                this.form.period_days = 91;
                this.form.period_label = 'Triwulan II (91 hari)';
            } else if (preset === 'cgk-sub') {
                this.form.departure_airport_id = findAirportId('CGK') || depSelect.options[1]?.value;
                this.form.arrival_airport_id = findAirportId('SUB') || depSelect.options[2]?.value;
                this.form.saved_distance_nm = 8.5;
                this.form.saved_time_minutes = 1.2;
                this.form.flights_per_day = 42;
                this.form.period_days = 91;
                this.form.period_label = 'Triwulan II (91 hari)';
            } else if (preset === 'cgk-upg') {
                this.form.departure_airport_id = findAirportId('CGK') || depSelect.options[1]?.value;
                this.form.arrival_airport_id = findAirportId('UPG') || depSelect.options[2]?.value;
                this.form.saved_distance_nm = 15.4;
                this.form.saved_time_minutes = 2.1;
                this.form.flights_per_day = 35;
                this.form.period_days = 91;
                this.form.period_label = 'Triwulan II (91 hari)';
            }

            this.onRouteChanged();
            this.submitCalculation();
        },

        async submitCalculation() {
            if (!this.form.departure_airport_id || !this.form.arrival_airport_id) {
                alert('Silakan pilih bandara asal dan tujuan.');
                return;
            }
            if (this.form.departure_airport_id === this.form.arrival_airport_id) {
                alert('Bandara asal dan tujuan tidak boleh sama.');
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
                alert('Gagal terhubung ke server: ' + err.message);
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

            // Loop flight animation along route
            const loopDuration = 8000;
            let animStart = null;

            const animateFlightLoop = (now) => {
                if (!this.mapInstance) return;
                if (!animStart) animStart = now;
                const elapsed = now - animStart;
                const progress = (elapsed % loopDuration) / loopDuration;

                const curLat = dep.lat + (arr.lat - dep.lat) * progress;
                const curLng = dep.lng + (arr.lng - dep.lng) * progress;

                planeMarker.setLatLng([curLat, curLng]);
                this.planeAnimationId = requestAnimationFrame(animateFlightLoop);
            };

            this.planeAnimationId = requestAnimationFrame(animateFlightLoop);

            // Fit bounds with comfortable padding
            this.mapInstance.fitBounds(bounds, { padding: [60, 60] });

            // Invalidate map size after Alpine finishes expanding the container
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

        copySummary() {
            if (!this.hasResult) return;
            const r = this.resultData;
            const res = r.result;
            const text = `LAPORAN PENGURANGAN EMISI RUTE RNAV - AIRNAV INDONESIA\n` +
                `====================================================\n` +
                `Rute: ${r.departure.iata_code} (${r.departure.city}) -> ${r.arrival.iata_code} (${r.arrival.city})\n` +
                `Periode Evaluasi: ${res.period_label} (${res.period_days} hari kalender)\n` +
                `Frekuensi Penerbangan: ${res.flights_per_day} flights/hari (Total: ${res.total_flights.toLocaleString('id-ID')} penerbangan)\n` +
                `Hemat Jarak: ${res.saved_distance_nm} Nm (${res.saved_distance_km} km per penerbangan)\n` +
                `Hemat Waktu: ${res.saved_time_minutes} menit per penerbangan\n` +
                `----------------------------------------------------\n` +
                `TOTAL PENGURANGAN EMISI CO2: ${res.co2_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})} Ton CO2 (${res.co2_saved_kg.toLocaleString('id-ID')} kg)\n` +
                `TOTAL AVTUR DIHEMAT: ${res.fuel_saved_ton.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})} Ton avtur (${res.fuel_saved_kg.toLocaleString('id-ID')} kg)\n` +
                `PENGURANGAN CO2 PER FLIGHT: ${res.co2_per_flight_kg.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2})} kg CO2\n` +
                `PENGHEMATAN BIAYA OPERASIONAL: $ ${res.cost_saved_usd.toLocaleString('id-ID')} USD (Rp ${res.cost_saved_idr.toLocaleString('id-ID')})\n` +
                `EKUIVALENSI SERAPAN POHON: ${res.tree_equivalence.toLocaleString('id-ID')} pohon per tahun\n` +
                `----------------------------------------------------\n` +
                `Standar: AirNav Indonesia (fb=3,59 kg/km, ef=3,15 kg CO2/kg avtur, ci=$25/mnt, fx=Rp 15.600, serapan=21 kg/pohon)`;

            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2500);
            });
        },

        resetForm() {
            this.form.departure_airport_id = '';
            this.form.arrival_airport_id = '';
            this.form.saved_distance_nm = 12.1;
            this.form.saved_time_minutes = 1.7;
            this.form.flights_per_day = 50;
            this.form.period_days = 91;
            this.form.period_label = 'Triwulan II (91 hari)';
            this.previewDistance = { gcd: 0, nm: 0 };
            this.hasResult = false;
            if (this.planeAnimationId) {
                cancelAnimationFrame(this.planeAnimationId);
                this.planeAnimationId = null;
            }
            if (this.mapInstance) {
                this.mapInstance.remove();
                this.mapInstance = null;
            }
        }
    };
}
</script>
@endpush
