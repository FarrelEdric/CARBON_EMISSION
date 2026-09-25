<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ACE — Aviation Carbon Emission Tracking. Sistem monitoring dan analisis estimasi emisi karbon penerbangan nasional.">
    <title>@yield('title', 'Dashboard') — ACE AirNav</title>

    {{-- Anti-flicker theme script --}}
    <script>
        (function() {
            var s = localStorage.getItem('ace_theme');
            var d = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (s === 'dark' || (!s && d)) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        })();

        function toggleDarkMode() {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('ace_theme', isDark ? 'dark' : 'light');
            syncThemeIcons();
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));
        }
        function syncThemeIcons() {
            var isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('.icon-sun').forEach(function(el) { el.style.display = isDark ? 'block' : 'none'; });
            document.querySelectorAll('.icon-moon').forEach(function(el) { el.style.display = isDark ? 'none' : 'block'; });
        }
        document.addEventListener('DOMContentLoaded', syncThemeIcons);
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; box-sizing: border-box; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }

        /* Sidebar transition */
        #sidebar { transition: transform 0.2s ease; }

        /* Leaflet */
        .leaflet-container { z-index: 1; font-family: inherit; }
        .dark .leaflet-tile-pane {
            filter: brightness(0.6) invert(1) contrast(1.7) hue-rotate(205deg) saturate(0.3);
        }

        /* Skeleton */
        @keyframes sk { 0% { background-position: -400px 0; } 100% { background-position: 400px 0; } }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 800px 100%;
            animation: sk 1.4s ease-in-out infinite;
            border-radius: 4px;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 800px 100%;
            animation: sk 1.4s ease-in-out infinite;
        }

        /* Content fade in */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        .ace-content-ready { animation: fadeIn 0.3s ease forwards; }

        /* Pulse for live indicator */
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .animate-pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        /* Alpine cloak */
        [x-cloak] { display: none !important; }

        /* Table row transition */
        tbody tr { transition: background-color 0.1s; }
    </style>

    @stack('head')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased">

{{-- Mobile overlay --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
     onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')">
</div>

{{-- ===== SIDEBAR ===== --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-56 bg-slate-900 border-r border-slate-800 z-30 flex flex-col -translate-x-full lg:translate-x-0">

    {{-- Brand --}}
    <div class="flex items-center gap-2.5 px-4 py-3.5 border-b border-slate-800 flex-shrink-0">
        <div class="w-7 h-7 rounded-md bg-blue-700 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <div class="text-white text-sm font-semibold tracking-tight leading-none">ACE</div>
            <div class="text-slate-500 text-[10px] mt-0.5 truncate">AirNav Carbon Emission</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

        {{-- Operasional --}}
        <div class="px-2.5 pt-2 pb-1">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Operasional</span>
        </div>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('carbon-calculator.index') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('carbon-calculator.*') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Kalkulator Emisi
        </a>

        <a href="{{ route('flights.index') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('flights.*') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Penerbangan
        </a>

        <a href="{{ route('reports.index') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Laporan
        </a>

        {{-- Master Data --}}
        <div class="px-2.5 pt-4 pb-1">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Master Data</span>
        </div>

        <div x-data="{ open: {{ request()->routeIs('airports.*','aircraft.*','operational-routes.*','carbon-factors.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-colors">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="flex-1 text-left">Basis Data Aviasi</span>
                <svg class="w-3 h-3 transition-transform text-slate-500" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="pl-4 mt-0.5 space-y-0.5">
                <a href="{{ route('airports.index') }}"
                   class="block px-2.5 py-1.5 rounded-md text-xs transition-colors
                          {{ request()->routeIs('airports.*') ? 'text-sky-400 font-medium bg-slate-800/80' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    Bandara
                </a>
                <a href="{{ route('aircraft.index') }}"
                   class="block px-2.5 py-1.5 rounded-md text-xs transition-colors
                          {{ request()->routeIs('aircraft.*') ? 'text-sky-400 font-medium bg-slate-800/80' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    Pesawat
                </a>
                <a href="{{ route('carbon-factors.index') }}"
                   class="block px-2.5 py-1.5 rounded-md text-xs transition-colors
                          {{ request()->routeIs('carbon-factors.*') ? 'text-sky-400 font-medium bg-slate-800/80' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    Faktor Karbon
                </a>
                <a href="{{ route('operational-routes.index') }}"
                   class="block px-2.5 py-1.5 rounded-md text-xs transition-colors
                          {{ request()->routeIs('operational-routes.*') ? 'text-sky-400 font-medium bg-slate-800/80' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    Rute Operasional
                </a>
            </div>
        </div>

        {{-- Administrasi --}}
        <div class="px-2.5 pt-4 pb-1">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Administrasi</span>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Kelola User
        </a>

        <!-- <a href="{{ route('admin.settings.index') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs transition-colors
                  {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white font-medium border-l-2 border-[#0B5A9E]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-l-2 border-transparent' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pengaturan
        </a> -->

    </nav>

    {{-- User footer --}}
    <div class="px-3 py-3 border-t border-slate-800 flex-shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-[11px] font-semibold text-slate-300 flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-medium text-slate-200 truncate">{{ auth()->user()->name }}</div>
                <div class="text-[10px] text-slate-500 truncate">{{ auth()->user()->getRoleLabel() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1 text-slate-500 hover:text-rose-400 rounded transition-colors" title="Keluar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ===== MAIN CONTENT ===== --}}
<div class="lg:pl-56 min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="sticky top-0 z-10 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 h-12 flex items-center px-4 sm:px-5 gap-3">

        {{-- Mobile hamburger --}}
        <button class="lg:hidden p-1.5 rounded text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebar-overlay').classList.toggle('hidden')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page title --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-sm font-semibold text-slate-800 dark:text-white truncate">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                    <span class="text-slate-300 dark:text-slate-700 hidden sm:inline text-xs">/</span>
                    <span class="hidden sm:block text-xs text-slate-400 truncate">@yield('page-subtitle')</span>
                @endif
            </div>
        </div>

        {{-- Right controls --}}
        <div class="flex items-center gap-1.5">

            {{-- Live status --}}
            <div class="hidden sm:flex items-center gap-1.5 px-2 py-1 text-[11px] text-slate-500 dark:text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-dot inline-block"></span>
                <span>ADS-B</span>
            </div>

            {{-- Theme toggle --}}
            <button onclick="toggleDarkMode()"
                    type="button"
                    class="p-1.5 rounded text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    title="Toggle tema">
                <svg class="icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="icon-sun w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center gap-1.5 px-2 py-1 rounded text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:block font-medium truncate max-w-24">{{ auth()->user()->name }}</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-1 z-50">

                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    </div>

                    <!-- <a href="{{ route('admin.settings.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Pengaturan Akun
                    </a> -->

                    <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash notifications --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mx-4 sm:mx-5 mt-4 flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-3.5 py-2.5 rounded-md text-xs">
        <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-sm leading-none">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
         class="mx-4 sm:mx-5 mt-4 flex items-center gap-2.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 px-3.5 py-2.5 rounded-md text-xs">
        <svg class="w-4 h-4 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="flex-1">{{ session('error') }}</span>
        <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-sm leading-none">&times;</button>
    </div>
    @endif

    @if(session('import_errors'))
    <div x-data="{ show: true, expanded: false }" x-show="show"
         class="mx-4 sm:mx-5 mt-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 px-3.5 py-2.5 rounded-md text-xs">
        <div class="flex items-center justify-between">
            <span class="font-medium">{{ count(session('import_errors')) }} baris gagal diimpor.</span>
            <button @click="expanded = !expanded" class="text-amber-600 dark:text-amber-400 underline">Lihat Detail</button>
        </div>
        <ul x-show="expanded" class="mt-2 space-y-0.5 list-disc pl-4 text-amber-700 dark:text-amber-300">
            @foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1">
        <div class="max-w-screen-xl mx-auto w-full">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 sm:px-5 py-3">
        <div class="max-w-screen-xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400">
            <span>&copy; {{ date('Y') }} AirNav Indonesia &mdash; ACE Aviation Carbon Emission Tracking</span>
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                ADS-B Gateway Terhubung
            </span>
        </div>
    </footer>
</div>

{{-- Vendor scripts --}}
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<script defer src="{{ asset('vendor/alpine/collapse.min.js') }}"></script>

@stack('scripts')
</body>
</html>
