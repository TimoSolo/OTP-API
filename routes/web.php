<?php

use App\Http\Controllers\OTPController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// OTP
Route::get('otp', function () {
    return view('otp-send');
})->name('otp');

Route::get('otp/verify', function () {
    return view('otp-verify');
})->name('otp.verify');

Route::get('otp/confirmed', function () {
    return view('otp-confirmed');
})->name('otp.confirmed');

Route::post('otp', [OTPController::class, 'sendOTP'])
                //->middleware('throttle:6,1')
                ->name('otp.send');

Route::post('otp/verify', [OTPController::class, 'verifyOTP'])
                //->middleware('throttle:6,1')
                ->name('otp.check');

require __DIR__.'/auth.php';
