@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan Emisi Karbon Penerbangan')

@section('content')
<div class="p-4 md:p-6 space-y-6"
     x-data="{ loading: true }"
     x-init="setTimeout(() => loading = false, 700)">

    {{-- ===== SKELETON DASHBOARD ===== --}}
    <div x-show="loading" x-cloak class="space-y-6">

        {{-- Filter bar skeleton --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex flex-col gap-1">
                    <div class="skeleton h-3 w-14 rounded mb-1"></div>
                    <div class="skeleton h-9 w-36 rounded-lg"></div>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="skeleton h-3 w-14 rounded mb-1"></div>
                    <div class="skeleton h-9 w-44 rounded-lg"></div>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="skeleton h-3 w-14 rounded mb-1"></div>
                    <div class="skeleton h-9 w-36 rounded-lg"></div>
                </div>
                <div class="skeleton h-9 w-24 rounded-lg mt-auto"></div>
            </div>
        </div>

        {{-- KPI Cards skeleton (4 cards) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="skeleton h-3 w-24 rounded"></div>
                    <div class="skeleton h-8 w-8 rounded-lg"></div>
                </div>
                <div class="skeleton h-7 w-32 rounded mb-2"></div>
                <div class="skeleton h-3 w-20 rounded"></div>
            </div>
            @endfor
        </div>

        {{-- Chart + Table skeleton row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <div class="skeleton h-4 w-40 rounded mb-1"></div>
                <div class="skeleton h-3 w-28 rounded mb-4"></div>
                <div class="skeleton h-52 w-full rounded-xl"></div>
            </div>
            {{-- Top routes --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <div class="skeleton h-4 w-36 rounded mb-4"></div>
                @for($j = 0; $j < 5; $j++)
                <div class="flex items-center gap-3 py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                    <div class="skeleton h-7 w-7 rounded-full flex-shrink-0"></div>
                    <div class="flex-1">
                        <div class="skeleton h-3 w-24 rounded mb-1"></div>
                        <div class="skeleton h-2.5 w-16 rounded"></div>
                    </div>
                    <div class="skeleton h-4 w-16 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Map skeleton --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <div class="skeleton h-4 w-48 rounded mb-1"></div>
                    <div class="skeleton h-3 w-32 rounded"></div>
                </div>
                <div class="flex gap-2">
                    <div class="skeleton h-8 w-24 rounded-lg"></div>
                    <div class="skeleton h-8 w-24 rounded-lg"></div>
                </div>
            </div>
            <div class="skeleton w-full rounded-b-2xl" style="height: 360px; border-radius: 0 0 1rem 1rem;"></div>
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="ace-content-ready space-y-6">

    <!-- ====== FILTERS ====== -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('dashboard') }}" id="filter-form" class="flex flex-wrap gap-3 items-end">

            <!-- Period -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Periode</label>
                <select name="period" onchange="toggleDateRange(this.value)"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E] focus:border-transparent">
                    @foreach(['today'=>'Hari Ini','yesterday'=>'Kemarin','this-week'=>'Minggu Ini','this-month'=>'Bulan Ini','this-quarter'=>'Kuartal Ini','this-year'=>'Tahun Ini','custom'=>'Custom'] as $val => $label)
                        <option value="{{ $val }}" {{ $filters['period'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range (shown for custom) -->
            <div id="date-range-wrapper" class="{{ $filters['period'] === 'custom' ? 'flex' : 'hidden' }} gap-2">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Dari</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                           class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Sampai</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                           class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                </div>
            </div>

            <!-- Origin -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Departure</label>
                <select name="origin"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                    <option value="">Semua</option>
                    @foreach($airports as $airport)
                        <option value="{{ $airport->iata_code }}" {{ $filters['origin'] === $airport->iata_code ? 'selected' : '' }}>
                            {{ $airport->iata_code }} — {{ $airport->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Destination -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Destination</label>
                <select name="destination"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                    <option value="">Semua</option>
                    @foreach($airports as $airport)
                        <option value="{{ $airport->iata_code }}" {{ $filters['destination'] === $airport->iata_code ? 'selected' : '' }}>
                            {{ $airport->iata_code }} — {{ $airport->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Aircraft -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Pesawat</label>
                <select name="aircraft"
                        class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E]">
                    <option value="">Semua</option>
                    @foreach($aircraft as $ac)
                        <option value="{{ $ac->id }}" {{ $filters['aircraft'] == $ac->id ? 'selected' : '' }}>
                            {{ $ac->manufacturer }} {{ $ac->model }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Terapkan Filter
                </button>
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- ====== KPI CARDS ====== -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total Penerbangan</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['total_flights']) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">penerbangan</div>
        </div>

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total CO₂</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['total_co2_tonnes'], 2) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">tonnes CO₂</div>
        </div>

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Rata-rata CO₂ / Penumpang</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['avg_co2_per_pax'], 2) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">kg CO₂/penumpang</div>
        </div>

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total Bahan Bakar</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['total_fuel_kg'] / 1000, 1) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">tonnes fuel</div>
        </div>

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total Penumpang</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['total_passengers']) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">penumpang est.</div>
        </div>

        <div class="kpi-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Rata-rata Load Factor</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $kpis['avg_load_factor'] }}<span class="text-base font-semibold">%</span></div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">load factor</div>
        </div>
    </div>

    <!-- ====== CHARTS ROW ====== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- CO2 Trend -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0B5A9E]"></span>
                Tren Emisi CO₂
            </h3>
            <div class="relative h-52">
                <canvas id="chart-trend"></canvas>
                @if(empty($charts['trend']['labels']) || count($charts['trend']['labels']) === 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">Tidak ada data untuk periode ini.</div>
                @endif
            </div>
        </div>

        <!-- CO2 by Aircraft -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0B5A9E]"></span>
                CO₂ per Jenis Pesawat
            </h3>
            <div class="relative h-52">
                <canvas id="chart-aircraft"></canvas>
                @if(empty($charts['byAircraft']['labels']) || count($charts['byAircraft']['labels']) === 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">Tidak ada data.</div>
                @endif
            </div>
        </div>

        <!-- CO2 by Route -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#D22228]"></span>
                CO₂ per Rute
            </h3>
            <div class="relative h-52">
                <canvas id="chart-route"></canvas>
            </div>
        </div>

        <!-- Avg CO2 per Pax -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Rata-rata CO₂/Penumpang per Rute
            </h3>
            <div class="relative h-52">
                <canvas id="chart-pax"></canvas>
            </div>
        </div>
    </div>

    <!-- ====== LIVE FLIGHT RADAR & TRACKING MAP ====== -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" id="flight-radar-card">
        <!-- Top Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#0B5A9E]/10 dark:bg-sky-500/20 text-[#0B5A9E] dark:text-sky-400 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            Peta Radar Penerbangan Real-Time
                        </h3>
                        <span id="flight-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="flight-status-text">Menghubungkan ADS-B...</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Live Tracking Transponder Pesawat ADS-B di Wilayah Udara Indonesia (FIR Jakarta &amp; Ujung Pandang)
                    </p>

                </div>
            </div>

            <!-- Stats & Action Controls -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <!-- Active Aircraft Count -->
                <div class="px-3 py-1 bg-white dark:bg-slate-700/80 rounded-lg border border-slate-200 dark:border-slate-600 font-semibold text-slate-700 dark:text-slate-200 shadow-sm flex items-center gap-1.5">
                    <span class="text-[#0B5A9E] dark:text-sky-400 font-bold">✈</span>
                    <span id="aircraft-count-display">0 Pesawat Aktif</span>
                </div>

                <!-- Last Updated -->
                <div class="px-2.5 py-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-mono">
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="last-updated-display">-</span>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1.5">
                    <button type="button" id="btn-refresh-flights" title="Refresh Posisi Pesawat Sekarang"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 transition shadow-sm flex items-center gap-1.5">
                        <svg id="refresh-icon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Refresh</span>
                    </button>

                    <button type="button" id="btn-toggle-polling" title="Jeda atau lanjutkan polling otomatis"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 transition shadow-sm flex items-center gap-1.5">
                        <span id="polling-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span id="btn-polling-text">Live (15s)</span>
                    </button>

                    <button type="button" id="btn-center-indonesia" title="Pusatkan kembali ke Indonesia"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition shadow-sm">
                        🇮🇩 Fokus RI
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Banner (shown when error or rate-limited) -->
        <div id="flight-radar-alert" class="hidden px-4 py-2 text-xs border-b transition-all flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span id="alert-icon">⚠️</span>
                <span id="alert-message">Info status API</span>
            </div>
            <button type="button" onclick="document.getElementById('flight-radar-alert').classList.add('hidden')" class="text-xs font-bold px-1.5 py-0.5 hover:bg-black/10 rounded">×</button>
        </div>

        <!-- Map Container -->
        <div class="relative">
            <div id="flight-map" style="height: 500px; width: 100%;"></div>

            <!-- Subtle Radar Legend Overlay (Bottom Left) -->
            <div class="absolute bottom-4 left-4 z-[400] bg-white/95 dark:bg-slate-900/90 backdrop-blur-md rounded-xl p-3 shadow-lg border border-slate-200/80 dark:border-slate-700/80 text-[10px] space-y-1.5 pointer-events-auto">
                <div class="font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-1 flex items-center justify-between gap-3">
                    <span>Legenda Radar Pesawat</span>
                    <span class="text-[9px] font-normal text-slate-400">ADS-B OpenSky</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#0B5A9E] inline-flex items-center justify-center text-[8px] text-white">✈</span>
                    <span class="text-slate-600 dark:text-slate-300">Cruising (Jelajah Udara)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 inline-flex items-center justify-center text-[8px] text-white">↗</span>
                    <span class="text-slate-600 dark:text-slate-300">Climbing (Menanjak &gt; 1.5 m/s)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-amber-600 inline-flex items-center justify-center text-[8px] text-white">↘</span>
                    <span class="text-slate-600 dark:text-slate-300">Descending (Menurun &lt; -1.5 m/s)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-slate-500 inline-flex items-center justify-center text-[8px] text-white">●</span>
                    <span class="text-slate-600 dark:text-slate-300">On Ground (Di Darat / Apron)</span>
                </div>
            </div>

            <!-- Selected Plane Quick Card (Bottom Right Floating Widget) -->
            <div id="selected-plane-card" class="hidden absolute bottom-4 right-4 z-[400] bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-slate-200 dark:border-slate-700 w-72 text-xs space-y-2 pointer-events-auto transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="quick-callsign" class="font-bold font-mono text-sm text-[#0B5A9E] dark:text-sky-400">GIA123</span>
                    </div>
                    <button type="button" onclick="document.getElementById('selected-plane-card').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">×</button>
                </div>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between"><span class="text-slate-400">Maskapai:</span> <span id="quick-airline" class="font-semibold text-slate-700 dark:text-slate-200">Garuda Indonesia</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Ketinggian:</span> <span id="quick-altitude" class="font-mono font-semibold text-slate-700 dark:text-slate-200">32,000 ft</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Kecepatan:</span> <span id="quick-speed" class="font-mono font-semibold text-slate-700 dark:text-slate-200">460 kts</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Heading:</span> <span id="quick-heading" class="font-mono font-semibold text-slate-700 dark:text-slate-200">120°</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Status:</span> <span id="quick-status" class="font-semibold text-emerald-600 dark:text-emerald-400">Cruising</span></div>
                </div>
            </div>
        </div>
    </div>


    <!-- ====== FLIGHT TABLE ====== -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Data Penerbangan Terkini</h3>
            <a href="{{ route('flights.index', request()->query()) }}"
               class="text-xs text-[#0B5A9E] hover:text-[#084a82] font-medium transition-colors">
                Lihat Semua →
            </a>
        </div>

        @if($flights->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tidak ada data penerbangan untuk filter yang dipilih.</p>
            <a href="{{ route('dashboard') }}" class="mt-3 text-xs text-[#0B5A9E] hover:underline">Reset filter</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penerbangan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rute</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pesawat</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">GCD (km)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fuel (kg)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Load %</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total CO₂ (kg)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CO₂/Pax (kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($flights as $flight)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer"
                        onclick="window.location='{{ route('flights.show', $flight) }}'">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#0B5A9E]/10 text-[#0B5A9E] dark:bg-[#0B5A9E]/20 dark:text-blue-300">
                                {{ $flight->flight_number }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $flight->flight_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $flight->departureAirport?->iata_code ?? '?' }} <span class="text-[#0B5A9E]">→</span> {{ $flight->arrivalAirport?->iata_code ?? '?' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">{{ $flight->aircraft?->manufacturer }} {{ $flight->aircraft?->model }}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-300">{{ number_format($flight->distance_gcd_km, 1) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-300">{{ number_format($flight->total_fuel_kg) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold
                                {{ ($flight->passenger_load_factor ?? 0) >= 0.85 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                   (($flight->passenger_load_factor ?? 0) >= 0.70 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                   'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                {{ number_format(($flight->passenger_load_factor ?? 0) * 100, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-slate-800 dark:text-slate-200">{{ number_format($flight->co2_total_kg, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-[#0B5A9E]">{{ number_format($flight->co2_per_passenger_kg, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- DEMO disclaimer -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3 text-xs text-amber-700 dark:text-amber-300">
        <strong>⚠️ DEMO DATA:</strong> Data penerbangan, bandara, dan pesawat dalam sistem ini adalah data demonstrasi. Hasil kalkulasi emisi CO₂ adalah estimasi berdasarkan metodologi ICAO, bukan pengukuran langsung.
    </div>

    </div>{{-- end real content --}}

</div>
@endsection


@push('scripts')
<script>
const chartData = @json($charts);
const isDark = () => document.documentElement.classList.contains('dark');

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: isDark() ? '#1e293b' : '#fff',
            titleColor: isDark() ? '#e2e8f0' : '#1e293b',
            bodyColor: isDark() ? '#94a3b8' : '#64748b',
            borderColor: isDark() ? '#334155' : '#e2e8f0',
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
        }
    },
    scales: {
        x: {
            grid: { color: isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
            ticks: { color: isDark() ? '#94a3b8' : '#64748b', font: { size: 10 } }
        },
        y: {
            grid: { color: isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
            ticks: { color: isDark() ? '#94a3b8' : '#64748b', font: { size: 10 } }
        }
    }
};

// Charts instances collection
const dashboardCharts = [];

// 1. CO2 Trend Chart
if (chartData.trend.labels.length > 0) {
    const chartTrend = new Chart(document.getElementById('chart-trend'), {
        type: 'line',
        data: {
            labels: chartData.trend.labels,
            datasets: [{
                label: 'CO₂ (kg)',
                data: chartData.trend.co2,
                borderColor: '#0B5A9E',
                backgroundColor: 'rgba(11,90,158,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0B5A9E',
                pointRadius: 4,
            }]
        },
        options: { ...chartDefaults }
    });
    dashboardCharts.push(chartTrend);
}

// 2. CO2 by Aircraft
if (chartData.byAircraft.labels.length > 0) {
    const chartAircraft = new Chart(document.getElementById('chart-aircraft'), {
        type: 'doughnut',
        data: {
            labels: chartData.byAircraft.labels,
            datasets: [{
                data: chartData.byAircraft.co2,
                backgroundColor: ['#0B5A9E','#1976D2','#42A5F5','#1565C0','#0D47A1','#1E88E5','#90CAF9','#BBDEFB'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: isDark() ? '#94a3b8' : '#64748b', font: { size: 10 }, padding: 8 } },
                tooltip: chartDefaults.plugins.tooltip
            }
        }
    });
    dashboardCharts.push(chartAircraft);
}

// 3. CO2 by Route
if (chartData.byRoute.labels.length > 0) {
    const chartRoute = new Chart(document.getElementById('chart-route'), {
        type: 'bar',
        data: {
            labels: chartData.byRoute.labels,
            datasets: [{
                label: 'CO₂ (kg)',
                data: chartData.byRoute.co2,
                backgroundColor: 'rgba(210,34,40,0.8)',
                borderRadius: 6,
            }]
        },
        options: { ...chartDefaults, indexAxis: 'y' }
    });
    dashboardCharts.push(chartRoute);
}

// 4. Avg CO2 per Pax
if (chartData.avgPerPax.labels.length > 0) {
    const chartPax = new Chart(document.getElementById('chart-pax'), {
        type: 'bar',
        data: {
            labels: chartData.avgPerPax.labels,
            datasets: [{
                label: 'CO₂/Pax (kg)',
                data: chartData.avgPerPax.values,
                backgroundColor: 'rgba(16,185,129,0.8)',
                borderRadius: 6,
            }]
        },
        options: { ...chartDefaults }
    });
    dashboardCharts.push(chartPax);
}

// Dynamic theme change listener for charts
window.addEventListener('theme-changed', (e) => {
    const dark = e.detail?.dark ?? isDark();
    dashboardCharts.forEach(c => {
        if (c.options.scales?.x) {
            c.options.scales.x.grid.color = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            c.options.scales.x.ticks.color = dark ? '#94a3b8' : '#64748b';
        }
        if (c.options.scales?.y) {
            c.options.scales.y.grid.color = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            c.options.scales.y.ticks.color = dark ? '#94a3b8' : '#64748b';
        }
        if (c.options.plugins?.tooltip) {
            c.options.plugins.tooltip.backgroundColor = dark ? '#1e293b' : '#fff';
            c.options.plugins.tooltip.titleColor = dark ? '#e2e8f0' : '#1e293b';
            c.options.plugins.tooltip.bodyColor = dark ? '#94a3b8' : '#64748b';
            c.options.plugins.tooltip.borderColor = dark ? '#334155' : '#e2e8f0';
        }
        if (c.options.plugins?.legend?.labels) {
            c.options.plugins.legend.labels.color = dark ? '#94a3b8' : '#64748b';
        }
        c.update();
    });
});

// ====== LIVE FLIGHT RADAR & TRACKING MAP ======
let mapInstance = null;
let aircraftMarkers = {}; // flight.id (icao24) -> L.marker
let activePopupFlightId = null;
let pollingTimer = null;
let isPollingActive = true;
let isFetching = false;
const POLLING_INTERVAL_MS = 15000; // 15 seconds

function initMap() {
    const mapEl = document.getElementById('flight-map');
    if (!mapEl) return;

    // Center on Indonesia
    mapInstance = L.map('flight-map', {
        center: [-2.5, 118],
        zoom: 5,
        minZoom: 3,
        maxZoom: 18,
        zoomControl: true,
    });

    const isDark = document.documentElement.classList.contains('dark');
    mapEl.style.backgroundColor = isDark ? '#0b1329' : '#e0f2fe';

    // Clean OpenStreetMap tiles (100% watermark-free, dark mode handled via CSS filter)
    const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';

    let tileLayer = L.tileLayer(tileUrl, {
        subdomains: 'abc',
        maxZoom: 19,
        opacity: 0.95,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mapInstance);

    // Local GeoJSON vector layer (offline capability)
    fetch('{{ asset('data/indonesia-provinces.geojson') }}')
        .then(r => r.json())
        .then(geoData => {
            L.geoJSON(geoData, {
                style: {
                    fillColor: isDark ? '#1e293b' : '#ffffff',
                    fillOpacity: isDark ? 0.35 : 0.45,
                    color: isDark ? '#0284c7' : '#0B5A9E',
                    weight: 1.2,
                    dashArray: '2, 3'
                }
            }).addTo(mapInstance);
        }).catch(() => {});

    setTimeout(() => { mapInstance.invalidateSize(); }, 150);
    setTimeout(() => { mapInstance.invalidateSize(); }, 400);

    if (window.ResizeObserver) {
        new ResizeObserver(() => { if (mapInstance) mapInstance.invalidateSize(); }).observe(mapEl);
    }

    // Sync with theme toggle
    window.addEventListener('theme-changed', (e) => {
        mapEl.style.backgroundColor = e.detail.dark ? '#0b1329' : '#e0f2fe';
    });


    // Reset View button handler
    const btnReset = document.getElementById('btn-center-indonesia');
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            mapInstance.flyTo([-2.5, 118], 5, { duration: 1.2 });
        });
    }

    // Manual Refresh button handler
    const btnRefresh = document.getElementById('btn-refresh-flights');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', () => {
            fetchLiveFlights(true);
        });
    }

    // Polling Toggle handler
    const btnToggle = document.getElementById('btn-toggle-polling');
    if (btnToggle) {
        btnToggle.addEventListener('click', togglePolling);
    }

    // Load static reference airports & operational routes
    loadReferenceAirportsAndRoutes();

    // Initial Live Flight Fetch & start polling loop
    fetchLiveFlights(true);
    startPolling();
}

function fetchLiveFlights(isManual = false) {
    if (isFetching) return;
    isFetching = true;

    const refreshIcon = document.getElementById('refresh-icon');
    if (refreshIcon) refreshIcon.classList.add('animate-spin');

    const statusBadge = document.getElementById('flight-status-badge');
    const statusText = document.getElementById('flight-status-text');
    const alertBox = document.getElementById('flight-radar-alert');

    fetch('{{ route("dashboard.live-flights") }}')
        .then(r => r.json())
        .then(data => {
            isFetching = false;
            if (refreshIcon) refreshIcon.classList.remove('animate-spin');

            const countEl = document.getElementById('aircraft-count-display');
            const updatedEl = document.getElementById('last-updated-display');

            if (updatedEl && data.last_updated) {
                updatedEl.textContent = 'Update: ' + data.last_updated;
            }

            if (data.status === 'live') {
                if (statusBadge) {
                    statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300';
                }
                const prov = data.provider || 'Live ADS-B';
                if (statusText) statusText.textContent = '● ' + prov;
                if (alertBox) alertBox.classList.add('hidden');

                if (countEl) countEl.textContent = `${data.total_count} Pesawat Aktif`;

                updateAircraftMarkers(data.flights || []);

                if (data.total_count === 0 && alertBox) {
                    showAlert('ℹ️', 'Tidak ada pesawat dengan transponder aktif terdeteksi di koordinat wilayah ini saat ini.', 'bg-blue-50 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800');
                }
            } else if (data.status === 'rate_limited') {
                if (statusBadge) {
                    statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
                }
                if (statusText) statusText.textContent = 'Rate Limited (OpenSky)';
                showAlert('⏳', data.message || 'API OpenSky sedang mencapai batas frekuensi rate limit. Menunggu jeda...', 'bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800');

                if (data.flights && data.flights.length > 0) {
                    updateAircraftMarkers(data.flights);
                    if (countEl) countEl.textContent = `${data.total_count} Pesawat (Cache)`;
                }
            } else if (data.status === 'network_error' || data.status === 'error') {
                if (statusBadge) {
                    statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';
                }
                if (statusText) statusText.textContent = 'Koneksi API Gagal';
                showAlert('⚠️', data.message || 'Gagal terhubung ke API live flight tracking. Memeriksa kembali...', 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800');

                if (data.flights && data.flights.length > 0) {
                    updateAircraftMarkers(data.flights);
                }
            }
        })
        .catch(err => {
            isFetching = false;
            if (refreshIcon) refreshIcon.classList.remove('animate-spin');
            console.error('Live flights fetch error:', err);
            showAlert('⚠️', 'Gagal memuat API data penerbangan: ' + err.message, 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800');
        });
}

function showAlert(icon, message, classes) {
    const alertBox = document.getElementById('flight-radar-alert');
    const alertIcon = document.getElementById('alert-icon');
    const alertMessage = document.getElementById('alert-message');
    if (!alertBox) return;

    alertIcon.textContent = icon;
    alertMessage.textContent = message;
    alertBox.className = `px-4 py-2 text-xs border-b transition-all flex items-center justify-between ${classes}`;
    alertBox.classList.remove('hidden');
}

function createPlaneIcon(flight) {
    let color = '#0B5A9E';
    if (flight.status === 'Climbing') color = '#059669';
    else if (flight.status === 'Descending') color = '#d97706';
    else if (flight.status === 'On Ground') color = '#64748b';

    const heading = flight.heading || 0;
    const callsign = (flight.callsign && flight.callsign !== 'N/A') ? flight.callsign : '';

    return L.divIcon({
        className: 'plane-custom-icon',
        html: `
            <div id="plane-marker-${flight.id}" class="plane-marker-container" style="position:relative;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                <div class="plane-rotator" style="transform: rotate(${heading}deg); transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); width:24px; height:24px; display:flex; align-items:center; justify-content:center;">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="${color}" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.45));">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>
                ${callsign ? `
                    <div class="plane-label" style="position:absolute;top:22px;left:50%;transform:translateX(-50%);white-space:nowrap;font-size:9px;font-weight:800;font-family:Inter,sans-serif;background:rgba(15,23,42,0.88);color:#38bdf8;padding:1px 4px;border-radius:3px;box-shadow:0 1px 3px rgba(0,0,0,0.3);pointer-events:none;border:0.5px solid rgba(56,189,248,0.4);">
                        ${callsign}
                    </div>
                ` : ''}
            </div>
        `,
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -14]
    });
}

function buildFlightPopup(flight) {
    let statusBadgeColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-sky-300';
    if (flight.status === 'Climbing') statusBadgeColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
    else if (flight.status === 'Descending') statusBadgeColor = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
    else if (flight.status === 'On Ground') statusBadgeColor = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';

    const altText = flight.altitude_ft != null
        ? `${flight.altitude_ft.toLocaleString()} ft (${flight.altitude_m?.toLocaleString()} m)`
        : 'N/A';
    const speedText = flight.speed_kts != null
        ? `${flight.speed_kts} kts (${flight.speed_kmh} km/h)`
        : 'N/A';
    const headingText = flight.heading != null ? `${flight.heading}°` : 'N/A';
    const vertRateText = flight.vertical_rate != null
        ? `${flight.vertical_rate > 0 ? '+' : ''}${flight.vertical_rate} m/s`
        : 'N/A';

    return `
        <div style="font-family:Inter,sans-serif;min-width:230px;padding:2px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:6px;margin-bottom:8px;">
                <div>
                    <span style="font-size:15px;font-weight:800;color:#0B5A9E;font-family:monospace;">${flight.callsign || 'N/A'}</span>
                    <span style="font-size:10px;color:#64748b;margin-left:4px;">${flight.airline !== 'N/A' ? flight.airline : ''}</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold ${statusBadgeColor}">${flight.status}</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:11px;margin-bottom:8px;">
                <div style="background:#f8fafc;padding:4px 6px;border-radius:6px;border:1px solid #f1f5f9;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;">Ketinggian</div>
                    <div style="font-weight:700;color:#1e293b;font-family:monospace;">${altText}</div>
                </div>
                <div style="background:#f8fafc;padding:4px 6px;border-radius:6px;border:1px solid #f1f5f9;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;">Kecepatan</div>
                    <div style="font-weight:700;color:#1e293b;font-family:monospace;">${speedText}</div>
                </div>
                <div style="background:#f8fafc;padding:4px 6px;border-radius:6px;border:1px solid #f1f5f9;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;">Heading</div>
                    <div style="font-weight:700;color:#1e293b;font-family:monospace;">${headingText}</div>
                </div>
                <div style="background:#f8fafc;padding:4px 6px;border-radius:6px;border:1px solid #f1f5f9;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;">Vert. Speed</div>
                    <div style="font-weight:700;color:#1e293b;font-family:monospace;">${vertRateText}</div>
                </div>
            </div>

            <div style="font-size:10px;color:#64748b;border-top:1px solid #f1f5f9;padding-top:6px;display:flex;justify-content:space-between;">
                <span>ICAO24: <strong style="font-family:monospace;color:#334155;">${flight.icao24?.toUpperCase()}</strong></span>
                <span>Asal: <strong>${flight.origin_country || 'N/A'}</strong></span>
            </div>
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">
                Pos: ${flight.lat.toFixed(4)}, ${flight.lng.toFixed(4)} • Kontak: ${flight.last_contact}
            </div>
        </div>
    `;
}

function updateAircraftMarkers(flights) {
    const currentFlightIds = new Set();

    flights.forEach(flight => {
        currentFlightIds.add(flight.id);

        if (aircraftMarkers[flight.id]) {
            // Update existing marker position smoothly
            const marker = aircraftMarkers[flight.id];
            marker.setLatLng([flight.lat, flight.lng]);

            // Update rotation of plane icon
            const markerEl = document.getElementById(`plane-marker-${flight.id}`);
            if (markerEl) {
                const rotator = markerEl.querySelector('.plane-rotator');
                if (rotator) {
                    rotator.style.transform = `rotate(${flight.heading || 0}deg)`;
                }
            } else {
                marker.setIcon(createPlaneIcon(flight));
            }

            // Update popup content without closing if currently open
            const popup = marker.getPopup();
            if (popup) {
                popup.setContent(buildFlightPopup(flight));
            }
        } else {
            // Create new marker
            const marker = L.marker([flight.lat, flight.lng], {
                icon: createPlaneIcon(flight),
                riseOnHover: true,
            }).addTo(mapInstance);

            marker.bindPopup(buildFlightPopup(flight), { maxWidth: 280 });

            marker.on('click', () => {
                showQuickCard(flight);
            });

            aircraftMarkers[flight.id] = marker;
        }
    });

    // Remove planes no longer in active response
    for (const id in aircraftMarkers) {
        if (!currentFlightIds.has(id)) {
            mapInstance.removeLayer(aircraftMarkers[id]);
            delete aircraftMarkers[id];
        }
    }
}

function showQuickCard(flight) {
    const card = document.getElementById('selected-plane-card');
    if (!card) return;

    document.getElementById('quick-callsign').textContent = flight.callsign || flight.icao24.toUpperCase();
    document.getElementById('quick-airline').textContent = flight.airline !== 'N/A' ? flight.airline : (flight.origin_country || 'N/A');
    document.getElementById('quick-altitude').textContent = flight.altitude_ft ? `${flight.altitude_ft.toLocaleString()} ft` : 'N/A';
    document.getElementById('quick-speed').textContent = flight.speed_kts ? `${flight.speed_kts} kts` : 'N/A';
    document.getElementById('quick-heading').textContent = flight.heading != null ? `${flight.heading}°` : 'N/A';
    document.getElementById('quick-status').textContent = flight.status;

    card.classList.remove('hidden');
}

function startPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = setInterval(() => {
        if (isPollingActive) {
            fetchLiveFlights(false);
        }
    }, POLLING_INTERVAL_MS);
}

function togglePolling() {
    isPollingActive = !isPollingActive;
    const btnText = document.getElementById('btn-polling-text');
    const indicator = document.getElementById('polling-dot');

    if (isPollingActive) {
        if (btnText) btnText.textContent = 'Live (15s)';
        if (indicator) {
            indicator.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-ping';
        }
        fetchLiveFlights(true);
    } else {
        if (btnText) btnText.textContent = 'Jeda';
        if (indicator) {
            indicator.className = 'w-2 h-2 rounded-full bg-slate-400';
        }
    }
}

function loadReferenceAirportsAndRoutes() {
    const url = '{{ route("dashboard.map-data") }}?' + new URLSearchParams({{ json_encode(request()->query()) }}).toString();
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const isDark = document.documentElement.classList.contains('dark');
            const airportIcon = L.divIcon({
                html: '<div style="width:8px;height:8px;background:#0B5A9E;border:2px solid white;border-radius:50%;box-shadow:0 0 5px rgba(11,90,158,0.7)"></div>',
                iconSize: [8, 8],
                iconAnchor: [4, 4],
                className: '',
            });

            // Add subtle airport pins as background reference
            if (data.airports) {
                data.airports.forEach(a => {
                    if (!a.lat || !a.lng) return;
                    L.marker([a.lat, a.lng], { icon: airportIcon, interactive: false }).addTo(mapInstance);
                    L.tooltip({ permanent: true, direction: 'top', offset: [0, -6], className: 'airport-bg-tooltip' })
                        .setContent(`<span style="font-size:8px;font-weight:700;color:${isDark ? '#94a3b8' : '#475569'};background:${isDark ? 'rgba(15,23,42,0.85)' : 'rgba(255,255,255,0.85)'};padding:1px 3px;border-radius:3px;border:1px solid ${isDark ? '#334155' : '#cbd5e1'};pointer-events:none;">${a.iata_code}</span>`)
                        .setLatLng([a.lat, a.lng])
                        .addTo(mapInstance);
                });
            }

            // Add operational routes as reference lines
            if (data.operationalRoutes) {
                data.operationalRoutes.forEach(r => {
                    if (!r.waypoints || r.waypoints.length < 2) return;
                    const latlngs = r.waypoints.map(w => [w.lat, w.lng]);
                    L.polyline(latlngs, {
                        color: '#0B5A9E',
                        weight: 1.5,
                        opacity: 0.35,
                        dashArray: '3, 4'
                    }).addTo(mapInstance);
                });
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', initMap);


function toggleDateRange(period) {
    const wrapper = document.getElementById('date-range-wrapper');
    if (period === 'custom') {
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
    } else {
        wrapper.classList.add('hidden');
        wrapper.classList.remove('flex');
    }
}
</script>
@endpush
