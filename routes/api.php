<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/auth/csrf', function () {
    $minutes = 120; // Cookie duration

    $token = csrf_token();
    $callbackUrl = config('app.frontend_url');

    $cookieCsrf = cookie('next-auth.csrf-token', $token, 0, '/', '', false, true, false, 'None', false);
    $cookieUrl  = cookie('next-auth.callback-url', $callbackUrl, 0, '/', '', false, true, false, 'None', 0);

    return response()->json(['csrfToken' => $token])->withCookie($cookieCsrf)->withCookie($cookieUrl);
});

Route::get('/auth/session', [AuthController::class, 'session']);
Route::get('/auth/providers', [AuthController::class, 'providers']);
Route::post('/auth/google/login', [AuthController::class, 'googleLogin'])->name('google.login');
Route::post('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');
//Route::get('/auth/csrf', [AuthController::class, 'csrf']);
Route::post('/auth/callback/{provider}', [AuthController::class, 'callbackProvider']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
