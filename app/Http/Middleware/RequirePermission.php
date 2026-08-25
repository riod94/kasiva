<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    /**
     * Handle an incoming request and verify user RBAC permissions.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Sesi login diperlukan untuk mengakses halaman ini.');
        }

        // Owner has absolute bypass
        if ($user->isOwner()) {
            return $next($request);
        }

        // If no permissions specified, just allow authenticated user
        if (empty($permissions)) {
            return $next($request);
        }

        // Check if user has at least one of the required permissions
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Akses Ditolak: Peran akun Anda ('.($user->role?->name ?? 'Kasir').') tidak memiliki izin untuk mengakses modul ini.');
    }
}
