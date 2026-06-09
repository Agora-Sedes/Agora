<?php

use App\Http\Controllers\AttendantRegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ExternalMercadoPagoController;
use App\Http\Controllers\IntranetConferenceAttendantController;
use App\Http\Controllers\IntranetConferenceController;
use App\Http\Middleware\IntranetAuth;

Route::pattern('id', '[0-9]+');

//// Normal user flow

Route::get('/', [ConferenceController::class, 'list'])->name('conferences.list');
Route::get('/conferences/{id}', [ConferenceController::class, 'show'])->name('conferences.show');

Route::controller(AttendantRegistrationController::class)->group(function () {
    Route::get('/conferences/{id}/register-1', 'register1')->name('conferences.register-1');
    Route::post('/conferences/{id}/register-2', 'register2')->name('conferences.register-2');
    Route::post('/conferences/{id}/register-3', 'register3')->name('conferences.register-3');
});

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

Route::controller(ExternalMercadoPagoController::class)->group(function () {
   Route::get('/external/mercado-pago/callback', 'callback')->name('external.mercado-pago.callback');
   Route::post('/external/webhooks/mercado-pago/successful-payment', 'successfulPaymentWebhook')->name("external.webhooks.mercado-pago.successful-payment");
});
