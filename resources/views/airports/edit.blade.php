@extends('layouts.app')

@section('title', 'Edit Airport — ' . $airport->name)
@section('page-title', 'Edit Airport')
@section('page-subtitle', 'Airport Master Data')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Edit Airport Data: {{ $airport->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">IATA Code: {{ $airport->iata_code ?? '-' }} | ICAO: {{ $airport->icao_code ?? '-' }}</p>
        </div>
        <a href="{{ route('airports.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition border border-slate-200 dark:border-slate-700">
            &larr; Back
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/60 rounded-xl p-4 text-xs text-red-700 dark:text-red-300">
        <div class="font-bold mb-1">There were errors with your submission:</div>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card p-6">
        <form method="POST" action="{{ route('airports.update', $airport) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- IATA Code -->
                <div>
                    <label for="iata_code" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        IATA Code (3 Characters)
                    </label>
                    <input type="text"
                           id="iata_code"
                           name="iata_code"
                           value="{{ old('iata_code', $airport->iata_code) }}"
                           placeholder="e.g. CGK"
                           maxlength="10"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- ICAO Code -->
                <div>
                    <label for="icao_code" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        ICAO Code (4 Characters)
                    </label>
                    <input type="text"
                           id="icao_code"
                           name="icao_code"
                           value="{{ old('icao_code', $airport->icao_code) }}"
                           placeholder="e.g. WIII"
                           maxlength="10"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Airport Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Airport Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $airport->name) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        City
                    </label>
                    <input type="text"
                           id="city"
                           name="city"
                           value="{{ old('city', $airport->city) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Province -->
                <div>
                    <label for="province" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Province / State
                    </label>
                    <input type="text"
                           id="province"
                           name="province"
                           value="{{ old('province', $airport->province) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Country (ISO Code / Name)
                    </label>
                    <input type="text"
                           id="country"
                           name="country"
                           value="{{ old('country', $airport->country) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Elevation -->
                <div>
                    <label for="elevation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Elevation (Feet)
                    </label>
                    <input type="number"
                           id="elevation"
                           name="elevation"
                           value="{{ old('elevation', $airport->elevation) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Latitude (-90 to 90)
                    </label>
                    <input type="number"
                           step="0.000001"
                           id="latitude"
                           name="latitude"
                           value="{{ old('latitude', $airport->latitude) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Longitude (-180 to 180)
                    </label>
                    <input type="number"
                           step="0.000001"
                           id="longitude"
                           name="longitude"
                           value="{{ old('longitude', $airport->longitude) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Status -->
                <div class="md:col-span-2 flex items-center gap-3 pt-2">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox"
                           id="status"
                           name="status"
                           value="1"
                           {{ old('status', $airport->status) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#0B5A9E] focus:ring-[#0B5A9E] border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <label for="status" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Active Airport (Available in calculator & flight logs)
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('airports.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Update Airport
                </button>
            </div>
        </form>
    </div>
        </form>
    </div>

</div>
@endsection
