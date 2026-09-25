@extends('layouts.app')

@section('title', 'Data Bandara')
@section('page-title', 'Master Data Bandara')
@section('page-subtitle', 'Bandara & Titik Koordinat')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: false }">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        {{-- Filter bar skeleton --}}
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-56 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
                <div class="skeleton h-9 w-20 rounded-md"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-32 rounded-md"></div>
            </div>
        </div>
        {{-- Table card skeleton --}}
        <div class="card overflow-hidden">
            {{-- Table header --}}
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                @foreach([80, 200, 140, 120, 80, 60, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            {{-- 7 skeleton rows --}}
            @for($i = 0; $i < 7; $i++)
            <div class="flex gap-4 items-center px-4 py-3 border-b border-slate-100 dark:border-slate-800/60 last:border-0">
                <div class="flex flex-col gap-1" style="width:80px; flex-shrink:0">
                    <div class="skeleton h-4 w-14 rounded"></div>
                    <div class="skeleton h-3 w-10 rounded"></div>
                </div>
                <div class="skeleton h-4 rounded flex-1"></div>
                <div class="flex flex-col gap-1" style="width:140px; flex-shrink:0">
                    <div class="skeleton h-4 w-24 rounded"></div>
                    <div class="skeleton h-3 w-16 rounded"></div>
                </div>
                <div class="skeleton h-4 w-28 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-6 w-14 rounded-full mx-auto" style="flex-shrink:0"></div>
                <div class="flex gap-1" style="flex-shrink:0">
                    <div class="skeleton h-6 w-12 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                    <div class="skeleton h-6 w-12 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="ace-content-ready space-y-4">

    <!-- Filter & Action Bar -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('airports.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari IATA, ICAO, nama, kota..."
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
                <a href="{{ route('airports.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Import Excel Modal -->
            <div x-data="{ open: false }">
                <button @click="open = true" class="btn-secondary">
                    Import Excel
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 w-full max-w-md shadow-lg border border-slate-200 dark:border-slate-800">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-1.5">Import Data Bandara</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unggah file format .xlsx atau .xls dengan kolom kode IATA, ICAO, Nama, Latitude, Longitude.</p>
                        <form method="POST" action="{{ route('airports.import-excel') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#0B5A9E] file:text-white hover:file:bg-[#084a82] mb-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="btn-secondary">
                                    Batal
                                </button>
                                <button type="submit" class="btn-primary">
                                    Upload & Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Export Excel -->
            <a href="{{ route('airports.export-excel') }}" class="btn-success">
                Export Excel
            </a>

            <!-- Create Airport -->
            <a href="{{ route('airports.create') }}" class="btn-primary">
                + Tambah Bandara
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card overflow-hidden">
        @if($airports->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data bandara ditemukan.</p>
                <a href="{{ route('airports.create') }}" class="mt-3 px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">
                    Tambah Bandara Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-4 py-2.5 text-left text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">IATA / ICAO</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Bandara</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kota / Lokasi</th>
                            <th class="px-4 py-2.5 text-right text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Koordinat</th>
                            <th class="px-4 py-2.5 text-right text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Elevasi</th>
                            <th class="px-4 py-2.5 text-center text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2.5 text-center text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach($airports as $airport)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-2.5 font-mono">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $airport->iata_code ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $airport->icao_code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-2.5">
                                <a href="{{ route('airports.show', $airport) }}" class="font-medium text-slate-800 dark:text-slate-200 hover:text-[#0B5A9E] dark:hover:text-sky-400">
                                    {{ $airport->name }}
                                </a>
                            </td>
                            <td class="px-4 py-2.5 text-slate-600 dark:text-slate-300">
                                <div>{{ $airport->city ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $airport->province ?? $airport->country }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono text-xs tabular-nums text-slate-600 dark:text-slate-300">
                                {{ number_format($airport->latitude, 4) }}, {{ number_format($airport->longitude, 4) }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono text-xs tabular-nums text-slate-600 dark:text-slate-300">
                                {{ $airport->elevation ? number_format($airport->elevation) . ' ft' : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($airport->status)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('airports.show', $airport) }}" class="px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition">
                                        Lihat
                                    </a>
                                    <a href="{{ route('airports.edit', $airport) }}" class="px-2 py-1 text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('airports.destroy', $airport) }}" onsubmit="return confirm('Hapus bandara {{ $airport->name }}?')" class="inline">
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

            @if($airports->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                    {{ $airports->links() }}
                </div>
            @endif
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
