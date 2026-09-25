@extends('layouts.app')

@section('title', 'Tambah Pesawat')
@section('page-title', 'Tambah Armada Pesawat')
@section('page-subtitle', 'Master Data Pesawat')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Form Tambah Pesawat</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan spesifikasi jenis pesawat dan parameter konsumsi bahan bakar ICAO.</p>
        </div>
        <a href="{{ route('aircraft.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition border border-slate-200 dark:border-slate-700">
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

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-6 shadow-sm">
        <form method="POST" action="{{ route('aircraft.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Manufacturer -->
                <div>
                    <label for="manufacturer" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Pabrikan Pesawat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="manufacturer"
                           name="manufacturer"
                           value="{{ old('manufacturer') }}"
                           required
                           placeholder="Contoh: Boeing, Airbus, ATR"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Model Pesawat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="model"
                           name="model"
                           value="{{ old('model') }}"
                           required
                           placeholder="Contoh: 737-800, A320-200"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- ICAO Type -->
                <div>
                    <label for="icao_type" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Tipe ICAO
                    </label>
                    <input type="text"
                           id="icao_type"
                           name="icao_type"
                           value="{{ old('icao_type') }}"
                           placeholder="Contoh: B738, A320"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- IATA Type -->
                <div>
                    <label for="iata_type" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Tipe IATA
                    </label>
                    <input type="text"
                           id="iata_type"
                           name="iata_type"
                           value="{{ old('iata_type') }}"
                           placeholder="Contoh: 738, 320"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Equivalent Aircraft -->
                <div>
                    <label for="equivalent_aircraft" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Equivalent Aircraft (Tipe Rujukan ICAO)
                    </label>
                    <input type="text"
                           id="equivalent_aircraft"
                           name="equivalent_aircraft"
                           value="{{ old('equivalent_aircraft') }}"
                           placeholder="Contoh: B738"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Y-Seats -->
                <div>
                    <label for="y_seats" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Kapasitas Kursi Standar (Y-Seats) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="y_seats"
                           name="y_seats"
                           value="{{ old('y_seats', 180) }}"
                           required
                           min="1"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Fuel Burn Factor -->
                <div>
                    <label for="fuel_burn_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Fuel Burn Factor (kg/km)
                    </label>
                    <input type="number"
                           step="0.0001"
                           id="fuel_burn_factor"
                           name="fuel_burn_factor"
                           value="{{ old('fuel_burn_factor') }}"
                           placeholder="Contoh: 3.2500"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Passenger to Freight Factor -->
                <div>
                    <label for="passenger_to_freight_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Passenger to Freight Factor (Default: 0.85)
                    </label>
                    <input type="number"
                           step="0.01"
                           id="passenger_to_freight_factor"
                           name="passenger_to_freight_factor"
                           value="{{ old('passenger_to_freight_factor', '0.85') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- CO2 Factor -->
                <div>
                    <label for="co2_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Faktor Emisi CO₂ ICAO (kg CO₂ / kg Fuel)
                    </label>
                    <input type="number"
                           step="0.01"
                           id="co2_factor"
                           name="co2_factor"
                           value="{{ old('co2_factor', '3.16') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Data Source -->
                <div>
                    <label for="data_source" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Sumber Data
                    </label>
                    <input type="text"
                           id="data_source"
                           name="data_source"
                           value="{{ old('data_source', 'ICAO Carbon Emissions Calculator Methodology') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Catatan Tambahan
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="2"
                              placeholder="Keterangan spesifikasi varian pesawat..."
                              class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('notes') }}</textarea>
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
                        Armada Aktif
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('aircraft.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Pesawat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
