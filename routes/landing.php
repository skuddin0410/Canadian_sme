<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FormBuilderController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

Route::get('/page/{slug}', [PageController::class, 'publicPage'])->name('public.page');
Route::get('/registration', [FormBuilderController::class, 'showFrontendForm'])->name('registration');
Route::get('app/page/{slug}', [PageController::class, 'appPage'])->name('public.page');

Route::prefix('form-builder')->group(function () {
  Route::post('/forms/{id}/submit', [FormBuilderController::class, 'submitForm'])->name('forms.submit');
});

Route::get('/profile', [LandingController::class, 'profile'])->name('profile');

Route::get('/session', [LandingController::class, 'session'])->name('session');