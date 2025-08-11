<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

Route::post('/register', [App\Http\Controllers\Api\JWTAuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\JWTAuthController::class, 'login']);
Route::post('/login-by-user', [App\Http\Controllers\Api\JWTAuthController::class, 'loginByUser']);
Route::post('/social', [App\Http\Controllers\Api\JWTAuthController::class, 'social']);

Route::prefix('otp')->group(function () {
    Route::post('generate', [App\Http\Controllers\Api\OtpController::class, 'generate']);
    Route::post('verify', [App\Http\Controllers\Api\OtpController::class, 'verify']);
});

Route::prefix('password')->group(function () {
    Route::post('/reset', [App\Http\Controllers\Api\JWTAuthController::class, 'resetPassword']);
});

Route::middleware(['auth:api', 'jwtauth'])->group(function () {
    Route::prefix('me')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getUser']);
        Route::post('/update', [App\Http\Controllers\Api\JWTAuthController::class, 'updateUser']);
        Route::post('/kyc', [App\Http\Controllers\Api\KycController::class, 'store']);
        Route::post('/bank', [App\Http\Controllers\Api\KycController::class, 'update']);
    });

    Route::prefix('password')->group(function () {
        Route::post('/change', [App\Http\Controllers\Api\JWTAuthController::class, 'changePassword']);
    });

    Route::get('/refresh-token', [App\Http\Controllers\Api\JWTAuthController::class, 'refreshToken']);
    Route::get('/logout', [App\Http\Controllers\Api\JWTAuthController::class, 'logout']);
    //Route::resource('events', EventController::class);
   
});



Route::prefix('calendar')->group(function () {
    Route::get('/data', [CalendarController::class, 'getCalendarData']);
    Route::get('/sessions', [CalendarController::class, 'getSessions']);
    Route::post('/sessions', [CalendarController::class, 'createSession']);
    Route::get('/sessions/{session}', [CalendarController::class, 'getSessionDetails']);
    Route::put('/sessions/{session}', [CalendarController::class, 'updateSession']);
    Route::delete('/sessions/{session}', [CalendarController::class, 'deleteSession']);
    Route::post('/sessions/bulk-update', [CalendarController::class, 'bulkUpdateSessions']);
});
