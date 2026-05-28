<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\AdminAuth;
use App\Mail\VerifyAssistanceMail;
use Illuminate\Support\Facades\Mail;

Route::get('/inscription', [App\Http\Controllers\InscriptionController::class, 'index']);
Route::post('/inscription', [App\Http\Controllers\InscriptionController::class, 'store']);

Route::view('/login', 'adminlogin')->name('login');
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware(AdminAuth::class)->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

Route::post('/buy', function (\Illuminate\Http\Request $request) {
    $quantity = $request->input('entry', 1);
    $method = $request->input('payment_method', 'value');
    return redirect('/inscription?quantity=' . $quantity . '&method=' . $method);
});

Route::get('/buy', function () {
    return view('buy-amount');
}); 

Route::get("/mercado-pago/callback", function () {
    $paymentUrl = "/123";
    
    Mail::to("test@test.com")
    ->send(new VerifyAssistanceMail($paymentUrl));


})->name("inscription.mercado-pago.callback");
