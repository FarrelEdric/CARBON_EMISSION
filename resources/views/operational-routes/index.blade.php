@extends('layouts.app')

@section('title', 'Operational Routes')
@section('page-title', 'Operational Routes Master Data')
@section('page-subtitle', 'Flight Paths & Real Distance Corrections')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: false }">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-56 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
                <div class="skeleton h-9 w-20 rounded-md"></div>
            </div>
            <div class="flex gap-2">
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-28 rounded-md"></div>
                <div class="skeleton h-9 w-36 rounded-md"></div>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                @foreach([60, 200, 100, 90, 90, 70, 70, 60] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 7; $i++)
            <div class="flex gap-4 items-center px-4 py-3 border-b border-slate-100 dark:border-slate-800/60 last:border-0">
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
                   placeholder="Search IATA airport, city, route..."
                   class="form-input-base w-64">

            <select name="source" class="form-select-base">
                <option value="">All Sources</option>
                <option value="official" {{ request('source') === 'official' ? 'selected' : '' }}>Official Routes (ATC)</option>
                <option value="demo" {{ request('source') === 'demo' ? 'selected' : '' }}>Demo Routes</option>
                <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Imported Data</option>
            </select>

            <select name="status" class="form-select-base">
                <option value="">All Statuses</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="btn-primary">
                Search
            </button>
            @if(request()->hasAny(['search', 'source', 'status']))
                <a href="{{ route('operational-routes.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Export Excel -->
            <a href="{{ route('operational-routes.export-excel') }}" class="btn-success">
                Export Excel
            </a>

            @can('manage-routes')
            <!-- Create Route -->
            <a href="{{ route('operational-routes.create') }}" class="btn-primary">
                + Add Operational Route
            </a>
            @endcan
        </div>
    </div>

    <!-- Table Card -->
    <div class="card overflow-hidden">
        @if($routes->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 font-medium">No operational routes found.</p>
                @can('manage-routes')
                <a href="{{ route('operational-routes.create') }}" class="mt-3 px-4 py-2 bg-[#0B5A9E] text-white text-sm font-semibold rounded-lg hover:bg-[#084a82]">
                    Add First Route
                </a>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                            <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Route (Origin → Destination)</th>
                            <th class="px-4 py-2.5 font-medium uppercase tracking-wider text-[11px]">Route / Airway Name</th>
                            <th class="px-4 py-2.5 text-right font-medium uppercase tracking-wider text-[11px]">Operational Distance (km)</th>
                            <th class="px-4 py-2.5 text-center font-medium uppercase tracking-wider text-[11px]">Data Source</th>
                            <th class="px-4 py-2.5 text-center font-medium uppercase tracking-wider text-[11px]">Status</th>
                            <th class="px-4 py-2.5 text-center font-medium uppercase tracking-wider text-[11px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach($routes as $route)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-2.5">
                                <a href="{{ route('operational-routes.show', $route) }}" class="font-bold text-slate-900 dark:text-white hover:text-[#0B5A9E] dark:hover:text-sky-400 flex items-center gap-1.5">
                                    <span class="font-mono text-xs font-semibold">{{ $route->departureAirport?->iata_code ?? '???' }}</span>
                                    <span class="text-slate-400 font-normal">&rarr;</span>
                                    <span class="font-mono text-xs font-semibold">{{ $route->arrivalAirport?->iata_code ?? '???' }}</span>
                                </a>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $route->departureAirport?->city }} to {{ $route->arrivalAirport?->city }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300">
                                {{ $route->route_name ?? 'Direct Route / Standard' }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-800 dark:text-slate-200">
                                {{ $route->distance_km ? number_format($route->distance_km, 1) . ' km' : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($route->source === 'official')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Official Route
                                    </span>
                                @elseif($route->source === 'imported')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        Import
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Demo Route
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($route->status)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('operational-routes.show', $route) }}" class="px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition">
                                        View
                                    </a>
                                    @can('manage-routes')
                                    <a href="{{ route('operational-routes.edit', $route) }}" class="px-2 py-1 text-xs font-medium text-[#0B5A9E] dark:text-sky-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('operational-routes.destroy', $route) }}" onsubmit="return confirm('Delete this operational route? This action cannot be undone.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded transition">
                                            Delete
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($routes->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                    {{ $routes->links() }}
                </div>
            @endif
        @endif
    </div>

    </div>{{-- end real content --}}

</div>
@endsection
