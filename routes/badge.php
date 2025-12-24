<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\NewBadgeController;


Route::post('/badges/pdf', [BadgeController::class, 'generateBadges'])->name('badges.print');
Route::resource('badges', BadgeController::class);
Route::get('badges/{badge}/download', [BadgeController::class, 'download'])->name('badges.download');


Route::post('/new/badges/pdf',[NewBadgeController::class, 'generateBadgePdf'])->name('new.badges.print');
Route::get('/new/badges/preview/{template_name}',[NewBadgeController::class, 'generateBadgePdfPreview'])->name('new.badges.preview');

Route::resource('newbadges', NewBadgeController::class);
Route::post(
    'newbadges/{newbadge}/save-layout',
    [NewBadgeController::class, 'saveLayout']
);



