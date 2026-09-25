@extends('layouts.app')

@section('title', 'Tambah Bandara')
@section('page-title', 'Tambah Bandara Baru')
@section('page-subtitle', 'Master Data Bandara')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Form Tambah Bandara</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan data titik bandara, kode IATA/ICAO, serta koordinat geografis.</p>
        </div>
        <a href="{{ route('airports.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition border border-slate-200 dark:border-slate-700">
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
        <form method="POST" action="{{ route('airports.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- IATA Code -->
                <div>
                    <label for="iata_code" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Kode IATA (3 Karakter)
                    </label>
                    <input type="text"
                           id="iata_code"
                           name="iata_code"
                           value="{{ old('iata_code') }}"
                           placeholder="Contoh: CGK"
                           maxlength="10"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- ICAO Code -->
                <div>
                    <label for="icao_code" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Kode ICAO (4 Karakter)
                    </label>
                    <input type="text"
                           id="icao_code"
                           name="icao_code"
                           value="{{ old('icao_code') }}"
                           placeholder="Contoh: WIII"
                           maxlength="10"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Airport Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama Bandara <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: Bandara Internasional Soekarno-Hatta"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Kota
                    </label>
                    <input type="text"
                           id="city"
                           name="city"
                           value="{{ old('city') }}"
                           placeholder="Contoh: Tangerang / Jakarta"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Province -->
                <div>
                    <label for="province" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Provinsi / Daerah
                    </label>
                    <input type="text"
                           id="province"
                           name="province"
                           value="{{ old('province') }}"
                           placeholder="Contoh: Banten"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Negara (Kode ISO / Nama)
                    </label>
                    <input type="text"
                           id="country"
                           name="country"
                           value="{{ old('country', 'ID') }}"
                           placeholder="Contoh: ID"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 uppercase focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Elevation -->
                <div>
                    <label for="elevation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Elevasi (Kaki / Feet)
                    </label>
                    <input type="number"
                           id="elevation"
                           name="elevation"
                           value="{{ old('elevation', 0) }}"
                           placeholder="Contoh: 34"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Latitude (-90 s/d 90)
                    </label>
                    <input type="number"
                           step="0.000001"
                           id="latitude"
                           name="latitude"
                           value="{{ old('latitude') }}"
                           placeholder="Contoh: -6.1256"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Longitude (-180 s/d 180)
                    </label>
                    <input type="number"
                           step="0.000001"
                           id="longitude"
                           name="longitude"
                           value="{{ old('longitude') }}"
                           placeholder="Contoh: 106.6558"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Status -->
                <div class="md:col-span-2 flex items-center gap-3 pt-2">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox"
                           id="status"
                           name="status"
                           value="1"
                           {{ old('status', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#0B5A9E] focus:ring-[#0B5A9E] border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <label for="status" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Bandara Aktif (Dapat dipilih dalam kalkulator & log penerbangan)
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('airports.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Bandara
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
