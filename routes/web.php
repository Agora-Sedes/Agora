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
use App\Http\Controllers\IntranetConferenceStreamController;
use App\Http\Controllers\StreamViewerController;
use App\Http\Middleware\IntranetAuth;

Route::pattern('id', '[0-9]+');
Route::pattern('qid', '[0-9]+');

//// Normal user flow

Route::get('/', [ConferenceController::class, 'list'])->name('conferences.list');
Route::get('/conferences/{id}', [ConferenceController::class, 'show'])->name('conferences.show');
Route::get('/conferences/{id}/stream', [StreamViewerController::class, 'show'])->name('conferences.stream');
Route::get('/api/conferences/{id}/manage/stream', [StreamViewerController::class, 'apiVideoId'])->name('api.conferences.stream');

// Questions API (public)
Route::post('/api/conferences/{id}/manage/questions', [IntranetConferenceStreamController::class, 'storeQuestion'])
    ->middleware('throttle:10,1')
    ->name('api.conferences.questions.store');

Route::controller(AttendantRegistrationController::class)->group(function () {
    Route::get('/conferences/{id}/register/step-1', 'amountAndPaymentMethodForm')->name('conferences.register.step-1');
    Route::post('/conferences/{id}/register/step-2', 'participantsForm')->name('conferences.register.step-2');
    Route::post('/conferences/{id}/register/success', 'completeRegistration')->name('conferences.register.step-3');
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
        Route::get('/intranet/conferences/new', 'new')->name('intranet.conferences.new');
        Route::post('/intranet/conferences/new', 'store')->name('intranet.conferences.store');
        Route::get('/intranet/conferences/{id}/edit', 'edit')->name('intranet.conferences.edit');
        Route::put('/intranet/conferences/{id}', 'update')->name('intranet.conferences.update');
        Route::delete('/intranet/conferences/{id}', 'destroy')->name('intranet.conferences.destroy');
        Route::get('/intranet/conferences/{id}/dashboard', 'dashboard')->name('intranet.conferences.dashboard');
        Route::get('/intranet/conferences/{id}/qr-scan', 'qrScan')->name('intranet.conferences.qr-scan');
    });

    Route::controller(IntranetConferenceAttendantController::class)->group(function () {
        /// Conference attendant management
        Route::get('/intranet/conferences/{id}/attendants/list', 'list')->name('intranet.conferences.attendants.list');
        Route::get('/intranet/conferences/{id}/attendants/new', 'new')->name('intranet.conferences.attendants.new');
        Route::post('/intranet/conferences/{id}/attendants/new', 'store')->name('intranet.conferences.attendants.store');
        Route::get('/intranet/conferences/{id}/attendants/{attendantId}/edit', 'edit')->name('intranet.conferences.attendants.edit');
        Route::put('/intranet/conferences/{id}/attendants/{attendantId}', 'update')->name('intranet.conferences.attendants.update');
        Route::post('/intranet/conferences/{id}/attendants/{attendantId}/resend-qr', 'resendQr')->name('intranet.conferences.attendants.resend-qr');
        Route::delete('/intranet/conferences/{id}/attendants/{attendantId}', 'destroy')->name('intranet.conferences.attendants.destroy');
    });

    Route::controller(IntranetConferenceStreamController::class)->group(function () {
        Route::get('/intranet/conferences/{id}/stream', 'edit')->name('intranet.conferences.stream.edit');
        Route::post('/intranet/conferences/{id}/stream', 'update')->name('intranet.conferences.stream.update');
        Route::post('/intranet/conferences/{id}/stream/stop', 'stop')->name('intranet.conferences.stream.stop');
    });

    Route::controller(IntranetConferenceStreamController::class)->group(function () {
        // Admin stream management
        Route::get('/intranet/conferences/{id}/manage', 'edit')->name('intranet.conferences.manage');
        Route::post('/api/conferences/{id}/manage/stream', 'update')->name('intranet.conferences.stream.update');
        Route::post('/api/conferences/{id}/manage/stream/stop', 'stop')->name('intranet.conferences.stream.stop');

        // Admin questions API
        Route::get('/api/conferences/{id}/manage/questions', 'adminQuestions')->name('api.conferences.questions.admin');
        Route::patch('/api/conferences/{id}/manage/questions/{qid}', 'updateQuestion')->name('api.conferences.questions.update');
        Route::delete('/api/conferences/{id}/manage/questions/{qid}', 'destroyQuestion')->name('api.conferences.questions.destroy');
    });
});

Route::controller(ExternalMercadoPagoController::class)->group(function () {
    Route::get('/external/mercado-pago/callback', 'callback')->name('external.mercado-pago.callback');
    Route::post('/webhooks/mercado-pago/successful-payment', 'successfulPaymentWebhook')->name("webhooks.mercado-pago.successful-payment");
});
