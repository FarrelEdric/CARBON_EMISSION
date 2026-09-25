@extends('layouts.app')

@section('title', 'Detail Rute Operasional')
@section('page-title', 'Detail Rute Operasional')
@section('page-subtitle', ($operationalRoute->departureAirport?->iata_code ?? '???') . ' → ' . ($operationalRoute->arrivalAirport?->iata_code ?? '???'))

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                    {{ $operationalRoute->departureAirport?->iata_code }} &rarr; {{ $operationalRoute->arrivalAirport?->iata_code }}
                </span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $operationalRoute->source === 'official' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                    {{ $operationalRoute->source_label }}
                </span>
            </div>
            <h1 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mt-1">
                {{ $operationalRoute->route_name ?? 'Rute Operasional' }}
            </h1>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('operational-routes.edit', $operationalRoute) }}" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Edit Rute
            </a>
            <a href="{{ route('operational-routes.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                &larr; Daftar Rute
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Route Specs -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Parameter Jalur</h3>
            <div>
                <span class="text-xs text-slate-400 block">Bandara Asal</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ $operationalRoute->departureAirport?->iata_code }} — {{ $operationalRoute->departureAirport?->name }}
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Bandara Tujuan</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ $operationalRoute->arrivalAirport?->iata_code }} — {{ $operationalRoute->arrivalAirport?->name }}
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Jarak Tempuh Operasional</span>
                <span class="text-xl font-bold font-mono text-brand-600 dark:text-brand-400">
                    {{ $operationalRoute->distance_km ? number_format($operationalRoute->distance_km, 1) . ' km' : '-' }}
                </span>
            </div>
        </div>

        <!-- Waypoints List -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Waypoints Jalur</h3>
            @if(is_array($operationalRoute->waypoints) && count($operationalRoute->waypoints) > 0)
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach($operationalRoute->waypoints as $idx => $wp)
                        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 dark:border-slate-700/60 font-mono">
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ is_array($wp) ? ($wp['name'] ?? 'WP ' . ($idx+1)) : $wp }}</span>
                            <span class="text-slate-400">{{ is_array($wp) && isset($wp['lat']) ? number_format($wp['lat'], 3) . ', ' . number_format($wp['lng'], 3) : '' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 italic">Rute ini menggunakan kalkulasi jarak total tanpa rincian waypoint spesifik.</p>
            @endif
        </div>

        <!-- Route Leaflet Map -->
        <div class="card overflow-hidden flex flex-col">
            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700/80 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-800/50">
                Peta Visual Rute
            </div>
            <div id="route-map" class="flex-1 min-h-[220px]"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const depLat = {{ $operationalRoute->departureAirport?->latitude ?? 0 }};
        const depLng = {{ $operationalRoute->departureAirport?->longitude ?? 0 }};
        const arrLat = {{ $operationalRoute->arrivalAirport?->latitude ?? 0 }};
        const arrLng = {{ $operationalRoute->arrivalAirport?->longitude ?? 0 }};

        if (depLat !== 0 && arrLat !== 0) {
            const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
            L.tileLayer(tileUrl, { subdomains: 'abc', maxZoom: 19 }).addTo(map);


            const bounds = L.latLngBounds([[depLat, depLng], [arrLat, arrLng]]);
            map.fitBounds(bounds, { padding: [30, 30] });
            setTimeout(() => { map.invalidateSize(); }, 150);

            L.marker([depLat, depLng]).addTo(map).bindPopup('Asal: {{ $operationalRoute->departureAirport?->iata_code }}');
            L.marker([arrLat, arrLng]).addTo(map).bindPopup('Tujuan: {{ $operationalRoute->arrivalAirport?->iata_code }}');

            // Draw line
            L.polyline([[depLat, depLng], [arrLat, arrLng]], {
                color: '#0B5A9E',
                weight: 3,
                opacity: 0.8
            }).addTo(map);
        }
    });
</script>
@endpush
