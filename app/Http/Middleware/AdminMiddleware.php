<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

     
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request);
        }
        
        return redirect()->route('sistem_akademik.dashboard')
            ->with('status', 'error')
            ->with('title', 'Akses Ditolak')
            ->with('message', 'Anda tidak memiliki akses ke halaman ini');
    }
}