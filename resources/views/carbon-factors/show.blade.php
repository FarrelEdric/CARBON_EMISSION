@extends('layouts.app')

@section('title', 'Detail Faktor Karbon — ' . $carbonFactor->name)
@section('page-title', 'Detail Faktor Karbon')
@section('page-subtitle', $carbonFactor->factor_key . ' — ' . $carbonFactor->name)

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $carbonFactor->name }}</span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-[#0B5A9E]/10 dark:bg-[#0B5A9E]/25 text-[#0B5A9E] dark:text-sky-300">
                    {{ $carbonFactor->factor_key }}
                </span>
                @if($carbonFactor->is_active)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                        Aktif Digunakan
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                        Nonaktif
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sumber Acuan: {{ $carbonFactor->source ?? 'Standar Metodologi ICAO' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('carbon-factors.edit', $carbonFactor) }}" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Edit Parameter
            </a>
            <form method="POST" action="{{ route('carbon-factors.destroy', $carbonFactor) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus faktor karbon {{ $carbonFactor->name }}?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 text-xs font-semibold rounded-lg transition border border-red-200 dark:border-red-800/50">
                    Hapus
                </button>
            </form>
            <a href="{{ route('carbon-factors.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                &larr; Daftar Faktor
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Parameter Details -->
        <div class="card p-5 space-y-4">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nilai Koefisien & Satuan</h3>
            <div>
                <span class="text-xs text-slate-400 block">Besaran Koefisien</span>
                <span class="text-3xl font-extrabold font-mono text-brand-600 dark:text-brand-400">
                    {{ number_format($carbonFactor->factor_value, 4) }}
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Satuan Unit Pengukuran</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100">{{ $carbonFactor->unit ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Tanggal Berlaku Efektif</span>
                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">
                    {{ $carbonFactor->effective_date ? $carbonFactor->effective_date->format('d F Y') : 'Berlaku Selamanya / Standar Tetap' }}
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-xs text-slate-400 block">Terakhir Diperbarui</span>
                <span class="text-xs font-mono text-slate-600 dark:text-slate-300">{{ $carbonFactor->updated_at?->format('d M Y H:i') }}</span>
            </div>
        </div>

        <!-- Formula Context -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peran Dalam Perhitungan ICAO</h3>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Koefisien ini digunakan oleh kalkulator emisi ACE untuk mengonversi massa bahan bakar (Aviation Fuel Jet A-1) menjadi estimasi emisi gas rumah kaca.
            </p>
            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300 space-y-1.5">
                <div class="font-bold text-brand-700 dark:text-brand-300">// Formulasi Emisi Karbon</div>
                <div>CO₂ (kg) = Fuel_Burn (kg) &times; {{ number_format($carbonFactor->factor_value, 4) }}</div>
                <div class="text-[11px] text-slate-400">Emisi per Penumpang = (CO₂ &times; P/F Ratio) / (Y_Seats &times; LF)</div>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400">
                Status Sistem: <strong class="text-slate-800 dark:text-slate-100">{{ $carbonFactor->is_active ? 'Terkoneksi ke Mesin Kalkulasi' : 'Dinonaktifkan Sementara' }}</strong>
            </div>
        </div>

        <!-- Explanation & Reference -->
        <div class="card p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Deskripsi & Dasar Regulasi</h3>
            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed space-y-2">
                @if($carbonFactor->description)
                    <p class="bg-slate-50/70 dark:bg-slate-900/40 p-3 rounded-lg border border-slate-200/80 dark:border-slate-700/60">
                        {{ $carbonFactor->description }}
                    </p>
                @else
                    <p class="italic text-slate-400">Tidak ada catatan deskripsi tambahan untuk parameter ini.</p>
                @endif
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                <span class="text-xs text-slate-400 block">Dokumen Regulasi / Acuan</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $carbonFactor->source ?? 'ICAO Doc 9889 & Annex 16 CORSIA' }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
