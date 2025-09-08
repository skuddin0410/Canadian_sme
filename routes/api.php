<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;

Route::post('/register', [App\Http\Controllers\Api\JWTAuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\JWTAuthController::class, 'login']);
Route::post('/login-by-user', [App\Http\Controllers\Api\JWTAuthController::class, 'loginByUser']);
Route::post('/social', [App\Http\Controllers\Api\JWTAuthController::class, 'social']);

Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\OtpController::class, 'generate']);
    Route::post('verify-otp', [App\Http\Controllers\Api\OtpController::class, 'verify']);
    Route::get('/check-session', [App\Http\Controllers\Api\JWTAuthController::class, 'checkSession']);
    Route::post('resend-otp', [App\Http\Controllers\Api\OtpController::class, 'generate']);
});

Route::prefix('password')->group(function () {
    Route::post('/reset', [App\Http\Controllers\Api\JWTAuthController::class, 'resetPassword']);
});

Route::middleware(['auth:api', 'jwtauth'])->group(function () {
       Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getUser']);
        Route::put('/', [App\Http\Controllers\Api\JWTAuthController::class, 'updateUser']);
        Route::post('/avatar', [App\Http\Controllers\Api\JWTAuthController::class, 'updateUserImage']);
      
    });
    
    Route::prefix('exhibitors')->group(function () {
        Route::get('/{exhibitorId}', [App\Http\Controllers\Api\JWTAuthController::class, 'getExhibitor']);
        Route::post('/{exhibitorId}/files', [App\Http\Controllers\Api\JWTAuthController::class, 'uploadExhibitorFiles']);
        Route::put('/{exhibitorId}/files/{fileId}', [App\Http\Controllers\Api\JWTAuthController::class, 'deleteExhibitorFiles']);
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', [HomeController::class, 'getAllSession']);
        Route::get('/{sessionId}', [HomeController::class, 'getSession']);
        Route::post('/{sessionId}', [HomeController::class, 'addSessionToFavourite']);
        Route::post('/{sessionId}/agenda', [HomeController::class, 'createAgenda']);
    }); 

    Route::get('/speakers', [App\Http\Controllers\Api\JWTAuthController::class, 'getSpeaker']);
    Route::get('/tags', [App\Http\Controllers\Api\JWTAuthController::class, 'getTags']);
    Route::post('/home', [HomeController::class, 'index']);

    Route::get('/get-notifications', [HomeController::class, 'getNotifications']);

    
    Route::prefix('connections')->group(function () {
        Route::get('/', [HomeController::class, 'getConnections']);
        Route::get('/{connectionId}', [HomeController::class, 'getConnectionsDetails']);
        Route::post('/scan', [HomeController::class, 'scanDetails']);
        Route::put('/{connectionId}', [HomeController::class, 'connectionUpdate']);
        Route::put('/create', [HomeController::class, 'scanNote']);
        
    });

    Route::prefix('agenda')->group(function () {
         Route::get('/', [HomeController::class, 'getAgenda']);
    });

  
    Route::prefix('password')->group(function () {
        Route::post('/change', [App\Http\Controllers\Api\JWTAuthController::class, 'changePassword']);
    });

    Route::get('/refresh-token', [App\Http\Controllers\Api\JWTAuthController::class, 'refreshToken']);
    Route::get('/logout', [App\Http\Controllers\Api\JWTAuthController::class, 'logout']);
    
   

});

 Route::get('/push', [App\Http\Controllers\Api\HomeController::class, 'sendPushNotification']);