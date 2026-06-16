<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IntranetAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_admin')) {
            return redirect()->route('intranet.login');
        }

        return $next($request);
    }
}
