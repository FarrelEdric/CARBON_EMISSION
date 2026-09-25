@extends('layouts.app')

@section('title', 'Tambah User Baru')
@section('page-title', 'Tambah User Baru')
@section('page-subtitle', 'Kelola User')

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 dark:hover:text-sky-400 transition-colors">Kelola User</a>
            <span class="text-slate-300 dark:text-slate-600">/</span>
            <span class="text-slate-700 dark:text-slate-200 font-medium">Tambah User Baru</span>
        </div>

        {{-- Validation Errors --}}
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

        {{-- Form Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Informasi Akun</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Isi semua field yang diperlukan untuk membuat akun baru.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="px-5 py-5 space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Budi Santoso"
                           autocomplete="name"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('name') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Username <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="username"
                           name="username"
                           value="{{ old('username') }}"
                           placeholder="Contoh: budi_santoso"
                           autocomplete="username"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('username') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none font-mono transition">
                    <p class="mt-1 text-[11px] text-slate-400">Hanya huruf, angka, underscore, dan strip. Tidak ada spasi.</p>
                    @error('username')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Contoh: budi@airnav.id"
                           autocomplete="email"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('email') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Minimal 8 karakter"
                           autocomplete="new-password"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('password') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Ulangi password"
                           autocomplete="new-password"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-4">
                    <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Hak Akses & Status</h3>

                    {{-- Role + Status in a row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Role --}}
                        <div>
                            <label for="role" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Role <span class="text-rose-500">*</span>
                            </label>
                            <select id="role"
                                    name="role"
                                    class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('role') border-rose-400 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                                <option value="">Pilih Role...</option>
                                <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Administrator</option>
                                <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="viewer"   {{ old('role') === 'viewer'   ? 'selected' : '' }}>Viewer</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Akun</label>
                            <label class="flex items-center gap-3 mt-2 cursor-pointer select-none">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox"
                                       id="status"
                                       name="status"
                                       value="1"
                                       {{ old('status', '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Aktifkan akun ini</span>
                            </label>
                            <p class="mt-1 text-[11px] text-slate-400">Akun nonaktif tidak dapat login.</p>
                        </div>
                    </div>

                    {{-- Role description --}}
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-3 text-xs text-slate-500 dark:text-slate-400 space-y-1.5">
                        <div class="flex items-start gap-2">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 flex-shrink-0 mt-0.5">Admin</span>
                            <span>Akses penuh ke semua fitur termasuk kelola user dan pengaturan sistem.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex-shrink-0 mt-0.5">Operator</span>
                            <span>Dapat mengelola data penerbangan, bandara, pesawat, dan kalkulator emisi.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex-shrink-0 mt-0.5">Viewer</span>
                            <span>Hanya dapat melihat data dan laporan. Tidak dapat menambah atau mengubah data.</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
