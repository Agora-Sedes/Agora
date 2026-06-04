<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Http\Request;

Route::get('/inscription', [App\Http\Controllers\InscriptionController::class, 'index']);
Route::post('/inscription', [App\Http\Controllers\InscriptionController::class, 'store']);

Route::view('/login', 'adminlogin')->name('login');
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware(AdminAuth::class)->group(function () {
Route::get('/dashboard', function (Request $request) {
    $conferenceName = $request->query('name');
    
    if (!$conferenceName) {
        return redirect('/conference-selector');
    }
    
    return view('dashboard', ['conference_name' => $conferenceName]);
})->name('dashboard');
    Route::view('/addattendant', 'admin.addattendant')->name('addattendant');
    Route::view('/attendants', 'admin.attendants')->name('attendants');
    Route::view('/qrscan', 'admin.qrscan')->name('qrscan');
    Route::view('/conference-selector', 'admin.conference-selector')->name('conference-selector');
});


Route::post('/buy', function (\Illuminate\Http\Request $request) {
    $quantity = $request->input('entry', 1);
    $method = $request->input('payment_method', 'value');
    return redirect('/inscription?quantity=' . $quantity . '&method=' . $method);
});
Route::get('/buy', function () {
    return view('buy-amount');
});