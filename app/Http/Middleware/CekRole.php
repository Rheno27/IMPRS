<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // 1. Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['login' => 'Silahkan login terlebih dahulu']);
        }

        $user = Auth::user();

        // 2. Logika Pengecekan Role
        if ($role == 'superadmin') {
            if ($user->isSuperadmin()) {
                return $next($request); 
            }
        } elseif ($role == 'admin') {
            if ($user->isAdminRuangan()) {
                return $next($request); 
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}