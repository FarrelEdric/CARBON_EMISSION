@extends('layouts.app')

@section('title', 'Data Pesawat')
@section('page-title', 'Master Data Pesawat')
@section('page-subtitle', 'Jenis Armada & Parameter Konsumsi ICAO')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: true }"
     x-init="setTimeout(() => loading = false, 600)">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-56 rounded-lg"></div>
                <div class="skeleton h-9 w-20 rounded-lg"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-32 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                @foreach([100, 120, 80, 80, 80, 80, 80, 60, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 6; $i++)
            <div class="flex gap-4 items-center px-4 py-4 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                <div class="skeleton h-4 w-24 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 flex-1 rounded"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-16 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-6 w-14 rounded-full" style="flex-shrink:0"></div>
                <div class="flex gap-1" style="flex-shrink:0">
                    <div class="skeleton h-6 w-10 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="ace-content-ready space-y-4">

    <!-- Filter & Action Bar -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('aircraft.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari pabrikan, model, ICAO..."
                   class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none w-64 shadow-sm">

            <button type="submit" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('aircraft.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap gap-2">
            <!-- Import Excel Modal -->
            <div x-data="{ open: false }">
                <button @click="open = true" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg transition-colors">
                    Import Excel
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-2">Import Data Pesawat</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unggah file format .xlsx atau .xls dengan parameter pabrikan, model, ICAO type, y-seats, dan fuel burn factor.</p>
                        <form method="POST" action="{{ route('aircraft.import-excel') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0B5A9E] file:text-white hover:file:bg-[#084a82] mb-5">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">
                                    Upload & Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Export Excel -->
            <a href="{{ route('aircraft.export-excel') }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Export Excel
            </a>

            <!-- Create Aircraft -->
            <a href="{{ route('aircraft.create') }}"
               class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                + Tambah Pesawat
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
        @if($aircrafts->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data armada pesawat ditemukan.</p>
                <a href="{{ route('aircraft.create') }}" class="mt-3 px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">
                    Tambah Pesawat Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pabrikan & Model</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode ICAO / IATA</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kapasitas Kursi</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fuel Burn Factor</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Faktor CO₂</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($aircrafts as $aircraft)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('aircraft.show', $aircraft) }}" class="font-semibold text-slate-900 dark:text-slate-100 hover:text-[#0B5A9E] dark:hover:text-sky-400">
                                    {{ $aircraft->manufacturer }} {{ $aircraft->model }}
                                </a>
                                @if($aircraft->equivalent_aircraft)
                                    <div class="text-[11px] text-slate-400">Eq: {{ $aircraft->equivalent_aircraft }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $aircraft->icao_type ?? '-' }}</span>
                                <span class="text-slate-400">/ {{ $aircraft->iata_type ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-medium text-slate-700 dark:text-slate-300">
                                {{ number_format($aircraft->y_seats) }} kursi
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-xs text-slate-600 dark:text-slate-400">
                                {{ $aircraft->fuel_burn_factor ? number_format($aircraft->fuel_burn_factor, 4) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-xs text-slate-600 dark:text-slate-400">
                                {{ $aircraft->co2_factor ? number_format($aircraft->co2_factor, 2) : '3.16' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($aircraft->status)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('aircraft.show', $aircraft) }}" class="px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition">
                                        Lihat
                                    </a>
                                    <a href="{{ route('aircraft.edit', $aircraft) }}" class="px-2 py-1 text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('aircraft.destroy', $aircraft) }}" onsubmit="return confirm('Hapus pesawat {{ $aircraft->manufacturer }} {{ $aircraft->model }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition">
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

            @if($aircrafts->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    {{ $aircrafts->links() }}
                </div>
            @endif
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
