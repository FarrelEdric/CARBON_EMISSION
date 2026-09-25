@extends('layouts.app')

@section('title', 'Tambah Faktor Karbon')
@section('page-title', 'Tambah Faktor Karbon')
@section('page-subtitle', 'Master Parameter ICAO')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Form Tambah Faktor Karbon</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tentukan koefisien konversi emisi atau faktor perhitungan ICAO.</p>
        </div>
        <a href="{{ route('carbon-factors.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition border border-slate-200 dark:border-slate-700">
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
        <form method="POST" action="{{ route('carbon-factors.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama Parameter <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: ICAO Jet Fuel CO2 Conversion Factor"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Factor Key -->
                <div>
                    <label for="factor_key" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Kunci Parameter (Unique Key) <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="factor_key"
                           name="factor_key"
                           value="{{ old('factor_key') }}"
                           required
                           placeholder="Contoh: icao_co2_factor"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Factor Value -->
                <div>
                    <label for="factor_value" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nilai Angka Koefisien <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           step="0.0001"
                           id="factor_value"
                           name="factor_value"
                           value="{{ old('factor_value') }}"
                           required
                           placeholder="Contoh: 3.16"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Satuan Unit
                    </label>
                    <input type="text"
                           id="unit"
                           name="unit"
                           value="{{ old('unit', 'kg CO2 / kg Fuel') }}"
                           placeholder="Contoh: kg CO2 / kg Fuel"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Source -->
                <div>
                    <label for="source" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Sumber Regulasi / Acuan
                    </label>
                    <input type="text"
                           id="source"
                           name="source"
                           value="{{ old('source', 'ICAO Annex 16 / CORSIA') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Effective Date -->
                <div>
                    <label for="effective_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Tanggal Efektif Berlaku
                    </label>
                    <input type="date"
                           id="effective_date"
                           name="effective_date"
                           value="{{ old('effective_date', date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Deskripsi Penjelasan
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="2"
                              placeholder="Keterangan cara kerja dan penggunaan parameter dalam rumus kalkulator..."
                              class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div class="md:col-span-2 flex items-center gap-3 pt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#0B5A9E] focus:ring-[#0B5A9E] border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Parameter Aktif (Digunakan dalam kalkulator emisi)
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('carbon-factors.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Faktor Karbon
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
