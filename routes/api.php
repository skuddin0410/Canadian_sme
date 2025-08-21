<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\JWTAuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\JWTAuthController::class, 'login']);
Route::post('/login-by-user', [App\Http\Controllers\Api\JWTAuthController::class, 'loginByUser']);
Route::post('/social', [App\Http\Controllers\Api\JWTAuthController::class, 'social']);

Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\OtpController::class, 'generate']);
    Route::post('verify-otp', [App\Http\Controllers\Api\OtpController::class, 'verify']);
});

Route::prefix('password')->group(function () {
    Route::post('/reset', [App\Http\Controllers\Api\JWTAuthController::class, 'resetPassword']);
});

Route::middleware(['auth:api', 'jwtauth'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getUser']);
        Route::put('/', [App\Http\Controllers\Api\JWTAuthController::class, 'updateUser']);
      
    });
    Route::get('/exhibitors/{exhibitorId}', [App\Http\Controllers\Api\JWTAuthController::class, 'getExhibitor']);
    Route::get('/speakers', [App\Http\Controllers\Api\JWTAuthController::class, 'getSpeaker']);
    Route::get('/tags', [App\Http\Controllers\Api\JWTAuthController::class, 'getTags']);

    Route::prefix('password')->group(function () {
        Route::post('/change', [App\Http\Controllers\Api\JWTAuthController::class, 'changePassword']);
    });

    Route::get('/refresh-token', [App\Http\Controllers\Api\JWTAuthController::class, 'refreshToken']);
    Route::get('/logout', [App\Http\Controllers\Api\JWTAuthController::class, 'logout']);
    //Route::resource('events', EventController::class);
   
});

