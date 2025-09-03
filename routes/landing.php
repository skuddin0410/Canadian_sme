<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

 Route::get('/page/{slug}', [PageController::class, 'publicPage'])->name('public.page');