<?php

use App\Http\Controllers\BadgeController;


Route::post('/badges/pdf', [BadgeController::class, 'generateBadges'])->name('badges.print');
Route::resource('badges', BadgeController::class);
Route::get('badges/{badge}/download', [BadgeController::class, 'download'])->name('badges.download');