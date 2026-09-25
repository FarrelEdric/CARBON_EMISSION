<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ACE — AIRNAV CARBON EMMISION. Sistem monitoring dan analisis estimasi emisi karbon penerbangan.">
    <title>@yield('title', 'Dashboard') — ACE</title>

    <!-- Immediate Anti-Flicker & Theme Manager Script -->
    <script>
        (function() {
            const saved = localStorage.getItem('ace_theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('ace_theme', isDark ? 'dark' : 'light');
            syncThemeIcons();
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));
        }

        function syncThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('.theme-icon-sun').forEach(el => {
                el.style.display = isDark ? 'block' : 'none';
            });
            document.querySelectorAll('.theme-icon-moon').forEach(el => {
                el.style.display = isDark ? 'none' : 'block';
            });
        }

        document.addEventListener('DOMContentLoaded', syncThemeIcons);
        window.addEventListener('load', syncThemeIcons);
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" onerror="this.onerror=null;" />

    <!-- App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ace-primary: #0B5A9E;
            --ace-primary-dark: #084a82;
            --ace-primary-light: #1976D2;
            --ace-accent: #D22228;
            --ace-dark: #111111;
        }
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        #sidebar { transition: transform 0.3s ease; }
        .leaflet-container { z-index: 1; }
        tbody tr { transition: background-color 0.15s ease; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        .kpi-card { background: linear-gradient(135deg, rgba(11,90,158,0.08) 0%, rgba(11,90,158,0.02) 100%); }
        .dark .kpi-card { background: linear-gradient(135deg, rgba(11,90,158,0.2) 0%, rgba(11,90,158,0.05) 100%); }
        /* Clean watermark-free Dark Mode map styling */
        .dark .leaflet-tile-pane {
            filter: brightness(0.65) invert(1) contrast(2.2) hue-rotate(200deg) saturate(0.35) brightness(0.75);
        }
        .dark .leaflet-tile.osm-invert {
            filter: brightness(0.8) invert(1) contrast(1.2) hue-rotate(180deg) saturate(0.7);
        }

        /* =============================================
           SKELETON LOADING SYSTEM
           ============================================= */
        @keyframes skeleton-shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg,
                #e2e8f0 25%,
                #f1f5f9 50%,
                #e2e8f0 75%
            );
            background-size: 800px 100%;
            animation: skeleton-shimmer 1.4s ease-in-out infinite;
            border-radius: 6px;
        }
        .dark .skeleton {
            background: linear-gradient(90deg,
                #1e293b 25%,
                #273549 50%,
                #1e293b 75%
            );
            background-size: 800px 100%;
            animation: skeleton-shimmer 1.4s ease-in-out infinite;
        }
        /* Page-enter fade in */
        @keyframes ace-fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ace-content-ready {
            animation: ace-fade-in 0.35s ease forwards;
        }
        /* Hide skeleton once loaded */
        [x-cloak] { display: none !important; }
    </style>


    @stack('head')
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-200">

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
     onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"
></div>

<!-- ======= SIDEBAR ======= -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 bg-slate-900 dark:bg-slate-950 border-r border-slate-800 z-30
              flex flex-col overflow-hidden -translate-x-full lg:translate-x-0 shadow-2xl"
>
    <!-- Logo -->
    <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-800">
        {{-- Circular emblem: globe gradient + white plane + leaves --}}
        <div class="flex-shrink-0 w-12 h-12">
            <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" width="48" height="48">
                <defs>
                    <radialGradient id="globeGrad" cx="40%" cy="35%" r="65%">
                        <stop offset="0%"   stop-color="#1976D2"/>
                        <stop offset="55%"  stop-color="#0B5A9E"/>
                        <stop offset="100%" stop-color="#063a6e"/>
                    </radialGradient>
                    <radialGradient id="shineGrad" cx="35%" cy="30%" r="50%">
                        <stop offset="0%"  stop-color="#fff" stop-opacity="0.18"/>
                        <stop offset="100%" stop-color="#fff" stop-opacity="0"/>
                    </radialGradient>
                </defs>

                {{-- Outer circular background --}}
                <circle cx="60" cy="60" r="58" fill="url(#globeGrad)" />
                <circle cx="60" cy="60" r="58" fill="url(#shineGrad)" />

                {{-- Globe grid lines (subtle) --}}
                <ellipse cx="60" cy="60" rx="38" ry="58" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="1.2"/>
                <ellipse cx="60" cy="60" rx="58" ry="28" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="1.2"/>
                <line x1="2" y1="60" x2="118" y2="60" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
                <line x1="60" y1="2"  x2="60"  y2="118" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>

                {{-- Left green leaf --}}
                <path d="M20 78 Q10 52 32 42 Q28 62 44 70 Q30 72 20 78Z"
                      fill="#3dba6f" opacity="0.92"/>
                <path d="M22 76 Q14 55 32 44" fill="none" stroke="#2e9e5a" stroke-width="1"/>

                {{-- Right green leaf --}}
                <path d="M100 78 Q110 52 88 42 Q92 62 76 70 Q90 72 100 78Z"
                      fill="#3dba6f" opacity="0.92"/>
                <path d="M98 76 Q106 55 88 44" fill="none" stroke="#2e9e5a" stroke-width="1"/>

                {{-- White airplane silhouette (center) --}}
                <g transform="translate(60,60) rotate(-30)" fill="white">
                    {{-- Fuselage --}}
                    <ellipse cx="0" cy="0" rx="22" ry="5.5" fill="white"/>
                    {{-- Main wings --}}
                    <path d="M-4,-4 L-16,-22 L-8,-18 L2,-6Z" fill="white"/>
                    <path d="M-4,4  L-16,22  L-8,18  L2,6Z"  fill="white"/>
                    {{-- Tail fins --}}
                    <path d="M18,-3 L22,-10 L20,-3Z" fill="white"/>
                    <path d="M18,3  L22,10  L20,3Z"  fill="white"/>
                    {{-- Nose --}}
                    <ellipse cx="-20" cy="0" rx="5" ry="3.5" fill="white"/>
                </g>

                {{-- Outer ring accent --}}
                <circle cx="60" cy="60" r="57" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="1.5"/>
            </svg>
        </div>

        {{-- Wordmark --}}
        <div class="leading-none">
            <div class="text-white font-black text-xl tracking-tight leading-none" style="font-family:'Space Grotesk',sans-serif;">ACE</div>
            <div class="text-[8.5px] text-slate-400 font-semibold tracking-widest leading-tight mt-0.5 uppercase">Airnav Carbon Emission</div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">

        <div class="mb-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('dashboard') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
        </div>

        <div class="mb-1">
            <a href="{{ route('carbon-calculator.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('carbon-calculator.*') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Kalkulator Emisi
            </a>
        </div>

        <div class="mb-1">
            <a href="{{ route('flights.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('flights.*') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Penerbangan
            </a>
        </div>

        <div class="mt-4 mb-2 px-3">
            <span class="text-[10px] font-semibold text-slate-600 uppercase tracking-widest">Master Data</span>
        </div>

        <div x-data="{ open: {{ request()->routeIs('airports.*','aircraft.*','operational-routes.*','carbon-factors.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-400 hover:text-white hover:bg-slate-800">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span class="flex-1 text-left">Master Data</span>
                <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-3 border-l border-slate-800 space-y-0.5">
                <a href="{{ route('airports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('airports.*') ? 'text-blue-400 bg-slate-800' : 'text-slate-500 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    Bandara
                </a>
                <a href="{{ route('aircraft.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('aircraft.*') ? 'text-blue-400 bg-slate-800' : 'text-slate-500 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Pesawat
                </a>
                <a href="{{ route('carbon-factors.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('carbon-factors.*') ? 'text-blue-400 bg-slate-800' : 'text-slate-500 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Faktor Karbon
                </a>
                <a href="{{ route('operational-routes.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('operational-routes.*') ? 'text-blue-400 bg-slate-800' : 'text-slate-500 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Rute Operasional
                </a>
            </div>
        </div>

        <div class="mb-1 mt-1">
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('reports.*') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
        </div>

        <div class="mt-4 mb-2 px-3">
            <span class="text-[10px] font-semibold text-slate-600 uppercase tracking-widest">Administrasi</span>
        </div>

        <div class="mb-1">
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('admin.users.*') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Kelola User
            </a>
        </div>

        <div class="mb-1">
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('admin.settings.*') ? 'bg-[#0B5A9E] text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
        </div>
    </nav>

    <!-- User Info -->
    <div class="px-4 py-4 border-t border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0B5A9E] to-[#1976D2] flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-[10px] text-slate-500 truncate">{{ auth()->user()->getRoleLabel() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-500 hover:text-[#D22228] transition-colors" title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ======= MAIN CONTENT ======= -->
<div class="lg:pl-64 min-h-screen flex flex-col">

    <!-- Top Header -->
    <header class="sticky top-0 z-10 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <button class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400"
                        onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebar-overlay').classList.toggle('hidden')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base font-semibold text-slate-800 dark:text-slate-200">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                    <span class="hidden sm:block text-xs text-slate-400 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-full">@yield('page-subtitle')</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <!-- Dark Mode Toggle Button -->
                <button onclick="toggleDarkMode()"
                        type="button"
                        class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-all border border-slate-200 dark:border-slate-700/60"
                        title="Beralih Mode Gelap/Terang">
                    <svg class="theme-icon-moon w-4 h-4 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="theme-icon-sun w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#0B5A9E] to-[#1976D2] flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden sm:block text-xs font-medium text-slate-700 dark:text-slate-300">{{ auth()->user()->name }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition
                         class="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50">
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <div class="border-t border-slate-200 dark:border-slate-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-[#D22228] hover:bg-red-50 dark:hover:bg-red-900/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mx-4 mt-4 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="ml-auto text-emerald-600 hover:text-emerald-900 text-lg leading-none">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
         class="mx-4 mt-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('error') }}</span>
        <button @click="show = false" class="ml-auto text-red-600 text-lg leading-none">&times;</button>
    </div>
    @endif

    @if(session('import_errors'))
    <div x-data="{ show: true, expanded: false }" x-show="show"
         class="mx-4 mt-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-300 px-4 py-3 rounded-xl text-sm">
        <div class="flex items-center justify-between">
            <span class="font-medium">{{ count(session('import_errors')) }} baris gagal diimport.</span>
            <button @click="expanded = !expanded" class="text-amber-600 text-xs underline">Detail</button>
        </div>
        <ul x-show="expanded" class="mt-2 space-y-1 text-xs list-disc pl-4">
            @foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <!-- Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 px-6 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
            <span>© {{ date('Y') }} ACE — AIRNAV CARBON EMMISION. Sistem Internal.</span>
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse-dot inline-block"></span>
                Sistem Aktif
            </span>
        </div>
    </footer>
</div>

<!-- Leaflet JS (Local with CDN fallback) -->
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<!-- Chart.js (Local) -->
<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<!-- Alpine Collapse Plugin (Local) -->
<script defer src="{{ asset('vendor/alpine/collapse.min.js') }}"></script>

@stack('scripts')
</body>
</html>
