@extends('layouts.app')

@section('title', 'Edit Rute Operasional')
@section('page-title', 'Edit Rute Operasional')
@section('page-subtitle', 'Master Rute Navigasi')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Edit Rute: {{ $operationalRoute->departureAirport?->iata_code }} &rarr; {{ $operationalRoute->arrivalAirport?->iata_code }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Jalur: {{ $operationalRoute->route_name ?? 'Standar' }}</p>
        </div>
        <a href="{{ route('operational-routes.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition border border-slate-200 dark:border-slate-700">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/60 rounded-xl p-4 text-xs text-red-700 dark:text-red-300">
        <div class="font-bold mb-1">Terdapat kesalahan pengisian data:</div>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card p-6">
        <form method="POST" action="{{ route('operational-routes.update', $operationalRoute) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Departure Airport -->
                <div>
                    <label for="departure_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Bandara Keberangkatan (Asal) <span class="text-red-500">*</span>
                    </label>
                    <select id="departure_airport_id"
                            name="departure_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('departure_airport_id', $operationalRoute->departure_airport_id) == $ap->id ? 'selected' : '' }}>
                                {{ $ap->iata_code }} — {{ $ap->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Arrival Airport -->
                <div>
                    <label for="arrival_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Bandara Kedatangan (Tujuan) <span class="text-red-500">*</span>
                    </label>
                    <select id="arrival_airport_id"
                            name="arrival_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('arrival_airport_id', $operationalRoute->arrival_airport_id) == $ap->id ? 'selected' : '' }}>
                                {{ $ap->iata_code }} — {{ $ap->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Route Name -->
                <div>
                    <label for="route_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama / Identifikasi Rute
                    </label>
                    <input type="text"
                           id="route_name"
                           name="route_name"
                           value="{{ old('route_name', $operationalRoute->route_name) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Operational Distance (km) -->
                <div>
                    <label for="distance_km" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Jarak Tempuh Operasional (km)
                    </label>
                    <input type="number"
                           step="0.1"
                           id="distance_km"
                           name="distance_km"
                           value="{{ old('distance_km', $operationalRoute->distance_km) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Source -->
                <div>
                    <label for="source" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Sumber Rute <span class="text-red-500">*</span>
                    </label>
                    <select id="source"
                            name="source"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        <option value="official" {{ old('source', $operationalRoute->source) === 'official' ? 'selected' : '' }}>Rute Resmi (AirNav ATC Flight Plan)</option>
                        <option value="demo" {{ old('source', $operationalRoute->source) === 'demo' ? 'selected' : '' }}>Rute Demo (Interpolasi)</option>
                        <option value="imported" {{ old('source', $operationalRoute->source) === 'imported' ? 'selected' : '' }}>Data Import</option>
                    </select>
                </div>

                <!-- Waypoints JSON -->
                <div class="md:col-span-2">
                    <label for="waypoints" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Waypoints Koordinat (JSON Array Format)
                    </label>
                    <textarea id="waypoints"
                              name="waypoints"
                              rows="3"
                              class="w-full px-3.5 py-2.5 text-xs font-mono bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('waypoints', $operationalRoute->waypoints ? json_encode($operationalRoute->waypoints, JSON_PRETTY_PRINT) : '') }}</textarea>
                </div>

                <!-- Status -->
                <div class="md:col-span-2 flex items-center gap-3 pt-1">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox"
                           id="status"
                           name="status"
                           value="1"
                           {{ old('status', $operationalRoute->status) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#0B5A9E] focus:ring-[#0B5A9E] border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <label for="status" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Rute Aktif Digunakan
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('operational-routes.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Perbarui Rute
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
