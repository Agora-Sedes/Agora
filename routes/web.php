<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MercadoPagoWebhookController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\IntranetConferenceAttendantController;
use App\Http\Controllers\IntranetConferenceController;
use App\Http\Middleware\IntranetAuth;

Route::pattern('id', '[0-9]+');

Route::get('/', [ConferenceController::class, 'list'])->name('conferences.list');
Route::get('/conferences/{id}', [ConferenceController::class, 'show'])->name('conferences.show');

//// Intranet

/// Auth
Route::view('/intranet/login', 'intranet.login')->name('intranet.login');
Route::post('/intranet/login', LoginController::class);
Route::post('/intranet/logout', LogoutController::class)->name('intranet.logout');

/// Administrator actions (requires login)
Route::middleware(IntranetAuth::class)->group(function () {
    Route::controller(IntranetConferenceController::class)->group(function () {
        /// General
        Route::get('/intranet/conferences/list', 'list')->name('intranet.conferences.list');
        Route::get('/intranet/conferences/{id}/dashboard', 'dashboard')->name('intranet.conferences.dashboard');
        Route::get('/intranet/conferences/{id}/qr-scan', 'qrScan')->name('intranet.conferences.qr-scan');
    });

    Route::controller(IntranetConferenceAttendantController::class)->group(function () {
        /// Conference attendant management
        Route::view('/intranet/conferences/{id}/attendants/list', 'list')->name('intranet.conferences.attendants.list');
        Route::view('/intranet/conferences/{id}/attendants/new', 'new')->name('intranet.conferences.attendants.new');
        Route::post('/intranet/conferences/{id}/attendants/new', 'store');
    });
});

Route::get('/inscription', [App\Http\Controllers\InscriptionController::class, 'index']);
Route::post('/inscription', [App\Http\Controllers\InscriptionController::class, 'store']);

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
