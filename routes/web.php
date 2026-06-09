<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MercadoPagoWebhookController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConferenceController;
use App\Http\Middleware\AdminAuth;

Route::get('/', [ConferenceController::class, 'list'])->name('home');
Route::get('/conferences/{id}', [ConferenceController::class, 'show'])->name('conferences.show');

Route::get('/inscription', [App\Http\Controllers\InscriptionController::class, 'index']);
Route::post('/inscription', [App\Http\Controllers\InscriptionController::class, 'store']);

Route::view('/login', 'adminlogin')->name('login');
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware(AdminAuth::class)->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/addattendant', 'admin.addattendant')->name('addattendant');
    Route::view('/attendants', 'admin.attendants')->name('attendants');
    Route::view('/qrscan', 'admin.qrscan')->name('qrscan');
});


Route::post('/buy', function (\Illuminate\Http\Request $request) {
    $quantity = $request->input('entry', 1);
    $method = $request->input('payment_method', 'value');
    return redirect('/inscription?quantity=' . $quantity . '&method=' . $method);
});

Route::get('/buy', function () {
    return view('buy-amount');
});

Route::get("/mercado-pago/callback", function (\Illuminate\Http\Request $request) {
    Log::info('Callback de Mercado Pago', $request->query());

    return view('mercado-pago.callback');
})->name("inscription.mercado-pago.callback");

Route::post("/webhooks/mercado-pago/successfull-payment", MercadoPagoWebhookController::class)
    ->name("webhooks.mercado-pago.successful-payment");

