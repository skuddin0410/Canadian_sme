<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

