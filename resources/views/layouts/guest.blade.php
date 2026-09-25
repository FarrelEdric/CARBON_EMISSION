<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ACE') }}</title>

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-900 antialiased min-h-screen transition-colors duration-200">
        
        <!-- Top Theme Toggle Floating Button -->
        <div class="absolute top-4 right-4 z-50">
            <button onclick="toggleDarkMode()"
                    type="button"
                    class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm hover:shadow transition-all"
                    title="Beralih Mode Gelap/Terang">
                <svg class="theme-icon-moon w-4 h-4 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="theme-icon-sun w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0B5A9E] to-[#1976D2] flex items-center justify-center shadow-lg">
                        <span class="text-white font-black text-sm tracking-tight">ACE</span>
                    </div>
                    <span class="font-bold text-sm tracking-wide text-slate-800 dark:text-slate-200">AIRNAV CARBON EMISSION</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-6 py-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 shadow-xl overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
