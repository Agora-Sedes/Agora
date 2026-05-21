<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->session()->forget('auth_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}