<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'token' => ['required','string'],
        ]);
        $admin_token=env('ADMIN_TOKEN');
        if (hash_equals($admin_token,$credentials['token'])) {
            session(['auth_admin'=>true]);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'token' => 'Error de token.',
        ]);
    }
}