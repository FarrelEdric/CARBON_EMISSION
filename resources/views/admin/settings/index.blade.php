@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Profil & Preferensi')

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Validation Errors (global) --}}
        @if($errors->any())
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-lg px-4 py-3">
            <p class="text-xs font-semibold text-rose-800 dark:text-rose-200 mb-1.5">Terdapat kesalahan pada input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-rose-700 dark:text-rose-300">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- === SECTION 1: Profil === --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Profil Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ubah nama tampilan dan alamat email Anda.</p>
            </div>

            {{-- Current user info strip --}}
            <div class="px-5 pt-4 pb-0 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-600 dark:text-slate-300 flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $user->name }}</div>
                    <div class="text-xs text-slate-400 font-mono">@{{ $user->username }}
                        &nbsp;&middot;&nbsp;
                        <span class="@if($user->role === 'admin') text-violet-600 dark:text-violet-400 @elseif($user->role === 'operator') text-blue-600 dark:text-blue-400 @else text-slate-500 @endif">{{ $user->getRoleLabel() }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update-profile') }}" class="px-5 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="profile_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="profile_name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Nama lengkap Anda"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('name') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile_email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="profile_email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="email@domain.com"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('email') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- === SECTION 2: Ubah Password === --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Ubah Password</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pastikan menggunakan password yang kuat dan unik.</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update-password') }}" class="px-5 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Password Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           placeholder="Masukkan password saat ini"
                           autocomplete="current-password"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('current_password') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('current_password')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password"
                               id="new_password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('password') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Ulangi password baru"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition-colors">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

        {{-- === SECTION 3: Tampilan === --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Tampilan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Atur preferensi tema antarmuka sistem.</p>
            </div>

            <div class="px-5 py-5 space-y-4">
                {{-- Live Theme Switcher --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">Mode Tampilan</label>
                    <div class="grid grid-cols-3 gap-3">
                        {{-- Light --}}
                        <button type="button"
                                id="theme-btn-light"
                                onclick="setTheme('light')"
                                class="theme-option-btn flex flex-col items-center gap-2 p-3 rounded-lg border-2 transition-all text-center cursor-pointer">
                            <div class="w-full h-12 rounded-md bg-white border border-slate-200 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Terang</span>
                        </button>

                        {{-- Dark --}}
                        <button type="button"
                                id="theme-btn-dark"
                                onclick="setTheme('dark')"
                                class="theme-option-btn flex flex-col items-center gap-2 p-3 rounded-lg border-2 transition-all text-center cursor-pointer">
                            <div class="w-full h-12 rounded-md bg-slate-900 border border-slate-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Gelap</span>
                        </button>

                        {{-- System --}}
                        <button type="button"
                                id="theme-btn-system"
                                onclick="setTheme('system')"
                                class="theme-option-btn flex flex-col items-center gap-2 p-3 rounded-lg border-2 transition-all text-center cursor-pointer">
                            <div class="w-full h-12 rounded-md overflow-hidden border border-slate-200 dark:border-slate-700 flex">
                                <div class="w-1/2 h-full bg-white flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                </div>
                                <div class="w-1/2 h-full bg-slate-900 flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                </div>
                            </div>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Sistem</span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-700">
                    <form method="POST" action="{{ route('admin.settings.update-theme') }}" id="theme-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="theme" id="theme-input" value="{{ session('ace_theme', 'system') }}">
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors">
                            Simpan Tema
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- === SECTION 4: Info Sistem === --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Informasi Sistem</h2>
            </div>
            <div class="px-5 py-4">
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Aplikasi</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">ACE — Aviation Carbon Emission Tracking</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Organisasi</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">AirNav Indonesia</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Framework</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200 font-mono">Laravel {{ app()->version() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">PHP</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200 font-mono">{{ PHP_VERSION }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Lingkungan</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">{{ ucfirst(config('app.env')) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Waktu Server</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200 tabular-nums">{{ now()->format('d M Y, H:i:s') }} WIB</dd>
                    </div>
                </dl>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
(function() {
    const currentTheme = '{{ session("ace_theme", "system") }}';
    markActive(currentTheme);

    function markActive(theme) {
        document.querySelectorAll('.theme-option-btn').forEach(btn => {
            btn.classList.remove('border-blue-600', 'bg-blue-50', 'dark:bg-blue-900/20');
            btn.classList.add('border-slate-200', 'dark:border-slate-700');
        });
        const active = document.getElementById('theme-btn-' + theme);
        if (active) {
            active.classList.remove('border-slate-200', 'dark:border-slate-700');
            active.classList.add('border-blue-600', 'bg-blue-50', 'dark:bg-blue-900/20');
        }
    }

    window.setTheme = function(theme) {
        document.getElementById('theme-input').value = theme;
        markActive(theme);

        // Apply immediately for preview
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('ace_theme', 'dark');
        } else if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('ace_theme', 'light');
        } else {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            localStorage.removeItem('ace_theme');
        }

        if (typeof syncThemeIcons === 'function') syncThemeIcons();
    };
})();
</script>
@endpush
@endsection
