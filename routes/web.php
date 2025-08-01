<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('account-information', [App\Http\Controllers\HomeController::class, 'accountInfo'])->name('change.account.information');
    Route::post('account-information', [App\Http\Controllers\HomeController::class, 'accountInformation'])->name('change.account.information.post');

    Route::get('change-password', [App\Http\Controllers\HomeController::class, 'changeAccountPassword'])->name('admin.change.password');
    Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('admin.change.password.post');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

   require __DIR__.'/admin.php';
   require __DIR__.'/exhibitor.php';
   require __DIR__.'/speaker.php';
   require __DIR__.'/attendee.php';
   require __DIR__.'/helpdesk.php';

   

});
