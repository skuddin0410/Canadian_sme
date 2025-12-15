<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;

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
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getAllExhibitor']);
        Route::get('/{exhibitorId}', [App\Http\Controllers\Api\JWTAuthController::class, 'getExhibitor']);
        //Route::put('/{exhibitorId}/files/{fileId}', [App\Http\Controllers\Api\JWTAuthController::class, 'deleteExhibitorFiles']);
        Route::post('/update', [App\Http\Controllers\Api\JWTAuthController::class, 'updateExhibitor']);
    });


    Route::delete('/{detailsID}/files', [App\Http\Controllers\Api\JWTAuthController::class, 'deleteExhibitorFiles']);


    Route::post('/{detailsID}/files', [App\Http\Controllers\Api\JWTAuthController::class, 'uploadExhibitorFiles']);
    
    Route::prefix('sponsors')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getAllSponsor']);
        Route::get('/{id}', [App\Http\Controllers\Api\JWTAuthController::class, 'getSponsor']);
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', [HomeController::class, 'getAllSession']);
        Route::get('/{sessionId}', [HomeController::class, 'getSession']);
        Route::get('/{sessionId}/favourite', [HomeController::class, 'addSessionToFavourite']);
        Route::post('/{sessionId}/agenda', [HomeController::class, 'createAgenda']);
    }); 
    
    Route::prefix('speakers')->group(function () {
       Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getSpeaker']);
      Route::get('/{id}', [App\Http\Controllers\Api\JWTAuthController::class, 'getSpeakerById']);
    });

    Route::prefix('attendees')->group(function () {
       Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getAttendee']);
      Route::get('/{id}', [App\Http\Controllers\Api\JWTAuthController::class, 'getAttendeeById']);
    });
    

    Route::get('/tags', [App\Http\Controllers\Api\JWTAuthController::class, 'getTags']);
    Route::post('/home', [HomeController::class, 'index']);

    Route::get('/get-notifications', [HomeController::class, 'getNotifications']);
    Route::get('/notification-read-all', [HomeController::class, 'readAllNotifications']);

    
    Route::prefix('connections')->group(function () {
        Route::get('/', [HomeController::class, 'getConnections']);
        Route::get('/{connectionId}', [HomeController::class, 'getConnectionsDetails']);
        Route::put('/{connectionId}', [HomeController::class, 'connectionUpdate']);
        Route::post('/scan', [HomeController::class, 'scanDetails']);
        Route::put('/update/scan', [HomeController::class, 'scanDetailsUpdate']);
        Route::post('/create', [HomeController::class, 'createConnection']);
        Route::get('/lead/export/{qr_code}', [HomeController::class, 'exportConnections']);

        Route::post('/favorite', [HomeController::class, 'addFavoriteConnection']);
        Route::get('/favorite/list', [HomeController::class, 'favoriteConnectionList']);
    });

    Route::prefix('agenda')->group(function () {
         Route::get('/', [HomeController::class, 'getAgenda']);
    });

  
    Route::prefix('password')->group(function () {
        Route::post('/change', [App\Http\Controllers\Api\JWTAuthController::class, 'changePassword']);
    });

    Route::get('/refresh-token', [App\Http\Controllers\Api\JWTAuthController::class, 'refreshToken']);
    Route::get('/logout', [App\Http\Controllers\Api\JWTAuthController::class, 'logout']);
    
    Route::post('/onesignal', [App\Http\Controllers\Api\HomeController::class, 'sendPushNotification']);
    
    Route::get('/delete-account', [App\Http\Controllers\Api\JWTAuthController::class, 'deleteAccount']);


  
});

 Route::post('/onesignal/test', [App\Http\Controllers\Api\HomeController::class, 'sendNotification']);

Route::post('/ios/test', [App\Http\Controllers\Api\HomeController::class, 'sendPushNotificationTest']);