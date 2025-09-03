<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FormBuilderController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

Route::get('/page/{slug}', [PageController::class, 'publicPage'])->name('public.page');
Route::get('/registration', [FormBuilderController::class, 'showFrontendForm'])->name('registration');

Route::prefix('form-builder')->group(function () {
  Route::post('/forms/{id}/submit', [FormBuilderController::class, 'submitForm'])->name('forms.submit');
});