@extends('layouts.app')

@section('title', 'Rute Operasional')
@section('page-title', 'Master Rute Operasional')
@section('page-subtitle', 'Jalur Penerbangan & Koreksi Jarak Nyata')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: true }"
     x-init="setTimeout(() => loading = false, 600)">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-56 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
                <div class="skeleton h-9 w-20 rounded-lg"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                @foreach([60, 200, 100, 90, 90, 70, 70, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 7; $i++)
            <div class="flex gap-4 items-center px-4 py-4 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                <div class="skeleton h-5 w-12 rounded" style="flex-shrink:0"></div>
                <div class="flex flex-col gap-1 flex-1">
                    <div class="skeleton h-4 w-48 rounded"></div>
                    <div class="skeleton h-3 w-32 rounded"></div>
                </div>
                <div class="skeleton h-4 w-20 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-20 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-4 w-20 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-6 w-14 rounded-full" style="flex-shrink:0"></div>
                <div class="skeleton h-6 w-14 rounded-full" style="flex-shrink:0"></div>
                <div class="flex gap-1" style="flex-shrink:0">
                    <div class="skeleton h-6 w-10 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="ace-content-ready space-y-4">

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('operational-routes.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari bandara IATA, kota, rute..."
                   class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none w-64 shadow-sm">

            <select name="source" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E] shadow-sm">
                <option value="">Semua Sumber</option>
                <option value="official" {{ request('source') === 'official' ? 'selected' : '' }}>Rute Resmi (ATC)</option>
                <option value="demo" {{ request('source') === 'demo' ? 'selected' : '' }}>Rute Demo</option>
                <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Import Data</option>
            </select>

            <select name="status" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#0B5A9E] shadow-sm">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Cari
            </button>
            @if(request()->hasAny(['search', 'source', 'status']))
                <a href="{{ route('operational-routes.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap gap-2">
            <!-- Export Excel -->
            <a href="{{ route('operational-routes.export-excel') }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Export Excel
            </a>

            <!-- Create Route -->
            <a href="{{ route('operational-routes.create') }}"
               class="px-4 py-2 bg-[#0B5A9E] hover:bg-[#084a82] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                + Tambah Rute Operasional
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
        @if($routes->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada rute operasional ditemukan.</p>
                <a href="{{ route('operational-routes.create') }}" class="mt-3 px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">
                    Tambah Rute Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rute (Asal → Tujuan)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Rute / Jalur</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jarak Operasional (km)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sumber Data</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($routes as $route)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('operational-routes.show', $route) }}" class="font-bold text-slate-900 dark:text-white hover:text-[#0B5A9E] dark:hover:text-sky-400 flex items-center gap-2">
                                    <span class="font-mono text-sm">{{ $route->departureAirport?->iata_code ?? '???' }}</span>
                                    <span class="text-[#0B5A9E] font-normal">&rarr;</span>
                                    <span class="font-mono text-sm">{{ $route->arrivalAirport?->iata_code ?? '???' }}</span>
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $route->departureAirport?->city }} ke {{ $route->arrivalAirport?->city }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                {{ $route->route_name ?? 'Rute Langsung / Standar' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-800 dark:text-slate-200">
                                {{ $route->distance_km ? number_format($route->distance_km, 1) . ' km' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($route->source === 'official')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                                        Rute Resmi
                                    </span>
                                @elseif($route->source === 'imported')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300">
                                        Import
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                                        Demo Route
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($route->status)
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
                                    <a href="{{ route('operational-routes.show', $route) }}" class="px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition">
                                        Lihat
                                    </a>
                                    <a href="{{ route('operational-routes.edit', $route) }}" class="px-2 py-1 text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('operational-routes.destroy', $route) }}" onsubmit="return confirm('Hapus rute operasional ini?')" class="inline">
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

            @if($routes->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    {{ $routes->links() }}
                </div>
            @endif
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
