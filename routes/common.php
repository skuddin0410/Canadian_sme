<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 

Route::get('account-information', [App\Http\Controllers\HomeController::class, 'accountInfo'])->name('change.account.information');
Route::post('account-information', [App\Http\Controllers\HomeController::class, 'accountInformation'])->name('change.account.information.post');

Route::get('change-password', [App\Http\Controllers\HomeController::class, 'changeAccountPassword'])->name('admin.change.password');
Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('admin.change.password.post');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('change-user-password', [App\Http\Controllers\HomeController::class, 'changeUserPassword'])->name('change.user.password');

Route::any('brand', [App\Http\Controllers\HomeController::class, 'brand'])->name('brand');
Route::any('splash', [App\Http\Controllers\HomeController::class, 'splash'])->name('splash');

Route::any('registration-settings', [App\Http\Controllers\HomeController::class, 'registrationSettings'])->name('registration-settings');