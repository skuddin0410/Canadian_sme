<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FormBuilderController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

Route::get('/page/{slug}', [PageController::class, 'publicPage'])->name('public.page');//Use on Frontend
Route::get('/registration', [FormBuilderController::class, 'showFrontendForm'])->name('registration');
Route::get('app/page/{slug}', [PageController::class, 'appPage'])->name('public.page'); // used on app

// Route::prefix('form-builder')->group(function () {
  Route::post('/forms/{id}/submit', [FormBuilderController::class, 'submitForm'])->name('forms.submit');
// });

Route::get('/profile/{id}', [LandingController::class, 'profile'])->name('profile');
Route::get('/attendees', [LandingController::class, 'attendees'])->name('attendees');
Route::get('/exhibitors', [LandingController::class, 'exhibitorIndex'])->name('exhibitor-index');
Route::get('/session/{id}', [LandingController::class, 'session'])->name('session');
Route::get('/exhibitor/{id}', [LandingController::class, 'exhibitor'])->name('exhibitor');
Route::get('/profile', [LandingController::class, 'profile'])->name('profile');

Route::get('/session', [LandingController::class, 'session'])->name('session');



Route::prefix('cms')->group(function () {
    Route::get('/{slug}', [PageController::class, 'appContent']);
});
