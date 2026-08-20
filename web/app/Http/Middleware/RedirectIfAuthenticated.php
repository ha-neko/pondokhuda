<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('owner')->check())
        {
            return redirect()->route('owner.dashboard');
        }
        else if (Auth::guard('super-owner')->check())
        {
            return redirect()->route('super-owner.dashboard');
        }
        else if (Auth::guard('admin')->check())
        {
            return redirect()->route('admin.dashboard');
        }
        else if (Auth::guard('penyewa')->check())
        {
            return redirect()->route('penyewa.dashboard');
        }


        return $next($request);
    }
}
