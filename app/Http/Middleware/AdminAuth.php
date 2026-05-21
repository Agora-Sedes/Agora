<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth 
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_admin')) {
            return redirect('/login');
        }

        return $next($request);
    }
}