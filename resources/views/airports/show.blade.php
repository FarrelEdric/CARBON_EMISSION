@extends('layouts.app')

@section('title', 'Detail Bandara — ' . $airport->name)
@section('page-title', 'Detail Bandara')
@section('page-subtitle', $airport->iata_code . ' — ' . $airport->name)

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black font-mono text-[#0B5A9E] dark:text-sky-400">{{ $airport->iata_code ?? '-' }}</span>
                <span class="text-sm font-mono text-slate-400">/ {{ $airport->icao_code ?? '-' }}</span>
                @if($airport->status)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                        Aktif
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                        Nonaktif
                    </span>
                @endif
            </div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $airport->name }}</h1>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('airports.edit', $airport) }}" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Edit Bandara
            </a>
            <a href="{{ route('airports.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                &larr; Daftar Bandara
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Details -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Informasi Wilayah</h3>
            <div>
                <span class="text-xs text-slate-400 block">Kota / Wilayah</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $airport->city ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Provinsi</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $airport->province ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Negara</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $airport->country ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Elevasi Bandara</span>
                <span class="text-sm font-mono text-slate-800 dark:text-slate-100">{{ $airport->elevation ? number_format($airport->elevation) . ' ft' : '-' }}</span>
            </div>
        </div>

        <!-- Coordinates & Stats -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Koordinat Geografis</h3>
            <div>
                <span class="text-xs text-slate-400 block">Latitude</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ number_format($airport->latitude, 6) }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Longitude</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ number_format($airport->longitude, 6) }}</span>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                <span class="text-xs text-slate-400 block">Aktivitas Penerbangan Terkait</span>
                <div class="flex items-center gap-4 mt-1 text-xs">
                    <div>Keberangkatan: <strong class="text-slate-800 dark:text-slate-100">{{ $airport->departureFlights->count() }}</strong></div>
                    <div>Kedatangan: <strong class="text-slate-800 dark:text-slate-100">{{ $airport->arrivalFlights->count() }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Mini Map -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 overflow-hidden shadow-sm flex flex-col">
            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400">
                Titik Lokasi Bandara
            </div>
            <div id="airport-map" class="flex-1 min-h-[180px]"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lat = {{ $airport->latitude ?? 0 }};
        const lng = {{ $airport->longitude ?? 0 }};
        if (lat !== 0 || lng !== 0) {
            const map = L.map('airport-map', {
                center: [lat, lng],
                zoom: 11,
                zoomControl: false,
            });
            const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
            L.tileLayer(tileUrl, { subdomains: 'abc', maxZoom: 19 }).addTo(map);

            setTimeout(() => { map.invalidateSize(); }, 150);
            L.marker([lat, lng]).addTo(map).bindPopup('<b>{{ $airport->iata_code }}</b><br>{{ $airport->name }}').openPopup();
        }
    });
</script>
@endpush
