@extends('layouts.app')

@section('title', 'Edit User — ' . $user->name)
@section('page-title', 'Edit User')
@section('page-subtitle', $user->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 dark:hover:text-sky-400 transition-colors">Kelola User</a>
            <span class="text-slate-300 dark:text-slate-600">/</span>
            <span class="text-slate-700 dark:text-slate-200 font-medium">Edit: {{ $user->name }}</span>
        </div>

        {{-- Session Flash Alerts --}}
        @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-lg px-4 py-3 text-xs text-emerald-800 dark:text-emerald-200 font-medium">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-lg px-4 py-3 text-xs text-rose-800 dark:text-rose-200 font-medium">
            {{ session('error') }}
        </div>
        @endif

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

        {{-- Profile Info Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Informasi Akun</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ubah data dasar akun, role, dan status.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="px-5 py-5 space-y-4" id="form-update-user">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Nama lengkap"
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
                           value="{{ old('username', $user->username) }}"
                           placeholder="username"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('username') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none font-mono transition">
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
                           value="{{ old('email', $user->email) }}"
                           placeholder="email@domain.com"
                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('email') border-rose-400 dark:border-rose-500 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role + Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <select id="role"
                                name="role"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border @error('role') border-rose-400 @else border-slate-200 dark:border-slate-600 @enderror rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                            <option value="admin"    {{ old('role', $user->role) === 'admin'    ? 'selected' : '' }}>Administrator</option>
                            <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="viewer"   {{ old('role', $user->role) === 'viewer'   ? 'selected' : '' }}>Viewer</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Akun</label>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="status" value="1">
                            <label class="flex items-center gap-3 mt-2 select-none opacity-75">
                                <input type="checkbox" checked disabled class="w-4 h-4 text-blue-600 border-slate-300 dark:border-slate-600 rounded">
                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                    Akun aktif <span class="text-[11px] text-slate-400 ml-1">(tidak dapat menonaktifkan akun sendiri)</span>
                                </span>
                            </label>
                        @else
                            <label class="flex items-center gap-3 mt-2 cursor-pointer select-none">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox"
                                       id="status"
                                       name="status"
                                       value="1"
                                       {{ old('status', $user->status ? '1' : '0') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500">
                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                    Akun aktif (dapat login ke sistem)
                                </span>
                            </label>
                        @endif
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Reset Password</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Biarkan kosong jika tidak ingin mengubah password user ini.</p>
            </div>

            <form method="POST"
                  action="{{ route('admin.users.update-password', $user) }}"
                  class="px-5 py-5 space-y-4"
                  id="form-update-password">
                @csrf
                @method('PUT')

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
                               class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
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
                        Reset Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        @if($user->id !== auth()->id())
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-rose-200 dark:border-rose-800/60 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-rose-100 dark:border-rose-800/50">
                <h2 class="text-sm font-semibold text-rose-700 dark:text-rose-400">Zona Berbahaya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300">Hapus User Ini</p>
                    <p class="text-xs text-slate-400 mt-0.5">Akun <strong class="text-slate-600 dark:text-slate-300">{{ $user->name }}</strong> akan dihapus permanen.</p>
                </div>
                <form method="POST"
                      action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Yakin hapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                        Hapus User
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
