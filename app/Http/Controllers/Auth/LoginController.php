<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
        ]);

        if (hash_equals(config('auth.token'), $credentials['token'])) {
            session(['auth_admin' => true]);
            $request->session()->regenerate();

            return redirect()->intended(route('intranet.conferences.list'));
        }

        return back()->withErrors([
            'token' => 'Token incorrecto',
        ]);
    }
}
