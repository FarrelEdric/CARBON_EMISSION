@extends('layouts.app')

@section('title', 'Tambah Rute Operasional')
@section('page-title', 'Tambah Rute Operasional')
@section('page-subtitle', 'Master Rute Navigasi')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Form Tambah Rute Operasional</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tentukan bandara asal, tujuan, dan jarak operasional riil berdasarkan data flight plan.</p>
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
        <form method="POST" action="{{ route('operational-routes.store') }}" class="space-y-6">
            @csrf

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
                        <option value="">Pilih Bandara Asal</option>
                        @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('departure_airport_id') == $ap->id ? 'selected' : '' }}>
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
                        <option value="">Pilih Bandara Tujuan</option>
                        @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('arrival_airport_id') == $ap->id ? 'selected' : '' }}>
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
                           value="{{ old('route_name') }}"
                           placeholder="Contoh: W45 Airways Direct, CGK-DPS Main"
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
                           value="{{ old('distance_km') }}"
                           placeholder="Contoh: 1012.5"
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
                        <option value="official" {{ old('source') === 'official' ? 'selected' : '' }}>Rute Resmi (AirNav ATC Flight Plan)</option>
                        <option value="demo" {{ old('source', 'demo') === 'demo' ? 'selected' : '' }}>Rute Demo (Interpolasi)</option>
                        <option value="imported" {{ old('source') === 'imported' ? 'selected' : '' }}>Data Import</option>
                    </select>
                </div>

                <!-- Waypoints JSON -->
                <div class="md:col-span-2">
                    <label for="waypoints" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Waypoints Koordinat (JSON Array Format Opsional)
                    </label>
                    <textarea id="waypoints"
                              name="waypoints"
                              rows="3"
                              placeholder='Contoh: [{"name":"CA","lat":-6.2,"lng":106.8},{"name":"CL","lat":-6.5,"lng":107.4}]'
                              class="w-full px-3.5 py-2.5 text-xs font-mono bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('waypoints') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika hanya menggunakan jarak operasional total.</p>
                </div>

                <!-- Status -->
                <div class="md:col-span-2 flex items-center gap-3 pt-1">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox"
                           id="status"
                           name="status"
                           value="1"
                           {{ old('status', '1') == '1' ? 'checked' : '' }}
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
                    Simpan Rute
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
