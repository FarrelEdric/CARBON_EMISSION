@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen Akun & Akses')

@section('content')
<div class="p-4 md:p-6 space-y-4"
     x-data="{ loading: true }"
     x-init="setTimeout(() => loading = false, 400)">

    {{-- ===== SKELETON ===== --}}
    <div x-show="loading" x-cloak>
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-2">
                <div class="skeleton h-9 w-52 rounded-lg"></div>
                <div class="skeleton h-9 w-36 rounded-lg"></div>
                <div class="skeleton h-9 w-20 rounded-lg"></div>
            </div>
            <div class="skeleton h-9 w-32 rounded-lg"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="flex gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                @foreach([120, 160, 200, 100, 80, 80] as $w)
                    <div class="skeleton h-4 rounded" style="width:{{ $w }}px; flex-shrink:0"></div>
                @endforeach
            </div>
            @for($i = 0; $i < 8; $i++)
            <div class="flex gap-4 items-center px-4 py-3.5 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                <div class="flex items-center gap-3" style="width:160px; flex-shrink:0">
                    <div class="skeleton h-8 w-8 rounded-full"></div>
                    <div class="skeleton h-4 w-24 rounded"></div>
                </div>
                <div class="skeleton h-4 flex-1 rounded"></div>
                <div class="skeleton h-4 w-32 rounded" style="flex-shrink:0"></div>
                <div class="skeleton h-5 w-20 rounded-full" style="flex-shrink:0"></div>
                <div class="skeleton h-5 w-14 rounded-full" style="flex-shrink:0"></div>
                <div class="flex gap-1" style="flex-shrink:0">
                    <div class="skeleton h-6 w-12 rounded"></div>
                    <div class="skeleton h-6 w-10 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ===== REAL CONTENT ===== --}}
    <div x-show="!loading" class="space-y-4">

        {{-- Filter & Action Bar --}}
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2 items-center">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, username, email..."
                       class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:outline-none w-60 shadow-sm">

                <select name="role" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 shadow-sm">
                    <option value="">Semua Role</option>
                    <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Administrator</option>
                    <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="viewer"   {{ request('role') === 'viewer'   ? 'selected' : '' }}>Viewer</option>
                </select>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Cari
                </button>

                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}"
                       class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm whitespace-nowrap">
                + Tambah User
            </a>
        </div>

        {{-- Summary Strip --}}
        <div class="flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400">
            <span>Total: <strong class="text-slate-700 dark:text-slate-200">{{ $users->total() }}</strong> user</span>
        </div>

        {{-- Table Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden">
            @if($users->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">Tidak ada user ditemukan.</p>
                    <a href="{{ route('admin.users.create') }}"
                       class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Tambah User Pertama
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">
                                    Username
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($users as $user)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                {{-- Nama + Avatar --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-semibold text-slate-600 dark:text-slate-300 flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100 truncate">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span class="ml-1.5 text-[10px] font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 rounded">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300 text-xs">
                                    {{ $user->email }}
                                </td>

                                {{-- Username --}}
                                <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400 hidden md:table-cell">
                                    @{{ $user->username }}
                                </td>

                                {{-- Role Badge --}}
                                <td class="px-4 py-3 text-center">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-100 dark:bg-violet-900/40 text-violet-800 dark:text-violet-300">
                                            Admin
                                        </span>
                                    @elseif($user->role === 'operator')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                                            Operator
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            Viewer
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-4 py-3 text-center">
                                    @if($user->status)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="px-2.5 py-1 text-xs font-medium text-blue-600 dark:text-sky-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors">
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="px-2.5 py-1 text-xs text-slate-300 dark:text-slate-600 cursor-not-allowed">Hapus</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>{{-- end real content --}}
</div>
@endsection
