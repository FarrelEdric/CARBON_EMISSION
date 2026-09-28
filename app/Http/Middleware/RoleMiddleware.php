<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Flatten roles if passed with commas like 'admin,operator'
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($user->role, $allowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Akses ditolak.',
                    'message' => 'Peran Anda (' . $user->getRoleLabel() . ') tidak memiliki izin untuk melakukan aksi ini.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Peran Anda (' . $user->getRoleLabel() . ') tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
