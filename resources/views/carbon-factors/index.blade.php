@extends('layouts.app')

@section('title', 'Faktor Karbon')
@section('page-title', 'Master Faktor Karbon')
@section('page-subtitle', 'Koefisien Emisi & Standar Konversi ICAO')

@section('content')
<div class="p-4 md:p-6 space-y-4">

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('carbon-factors.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari parameter, key, sumber..."
                   class="form-input-base w-64">

            <select name="status" class="form-select-base">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="btn-primary">
                Cari
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('carbon-factors.index') }}" class="btn-secondary">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap gap-2">
            <!-- Export Excel -->
            <a href="{{ route('carbon-factors.export-excel') }}" class="btn-success">
                Export Excel
            </a>

            <!-- Create Factor -->
            <a href="{{ route('carbon-factors.create') }}" class="btn-primary">
                + Tambah Faktor Karbon
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card overflow-hidden">
        @if($factors->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data faktor karbon ditemukan.</p>
                <a href="{{ route('carbon-factors.create') }}" class="mt-3 btn-primary">
                    Tambah Faktor Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700/80">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama & Kunci Faktor</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai Koefisien</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Satuan Unit</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sumber Acuan</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($factors as $factor)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('carbon-factors.show', $factor) }}" class="font-semibold text-slate-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                                    {{ $factor->name }}
                                </a>
                                <div class="font-mono text-xs text-slate-400 mt-0.5">{{ $factor->factor_key }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 dark:text-slate-100">
                                {{ number_format($factor->factor_value, 4) }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono text-xs">
                                {{ $factor->unit ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">
                                {{ $factor->source ?? 'Standar ICAO' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($factor->is_active)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/80 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('carbon-factors.show', $factor) }}" class="px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition">
                                        Lihat
                                    </a>
                                    <a href="{{ route('carbon-factors.edit', $factor) }}" class="px-2 py-1 text-xs font-medium text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('carbon-factors.destroy', $factor) }}" onsubmit="return confirm('Hapus faktor karbon {{ $factor->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($factors->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    {{ $factors->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
