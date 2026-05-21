<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index (){
        return view('adminlogin');
    }

    public function store (Request $request){
        $credentials = $request->validate([
            'token' => ['required'],
        ]);
        $admin_token=env('ADMIN_TOKEN');
        if ( $credentials['token'] !== $admin_token) {
            return back()->withErrors([
                'token' => 'Token Incorrecto.',
            ]);
        }

        if (Auth::attempt($credentials)){
            return redirect()->intended('/dashboard');
        }
    }
}
