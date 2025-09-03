<?php

use Illuminate\Http\Request;

use App\Http\Controllers\FormBuilderController;

Route::prefix('form-builder')->group(function () {
    Route::get('/', [FormBuilderController::class, 'index'])->name('form-builder.index');
    Route::post('/forms', [FormBuilderController::class, 'store'])->name('forms.store');
    Route::get('/forms', [FormBuilderController::class, 'getForms'])->name('forms.index');
    Route::get('/forms/{id}', [FormBuilderController::class, 'show'])->name('forms.show');
    Route::put('/forms/{id}', [FormBuilderController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{id}', [FormBuilderController::class, 'destroy'])->name('forms.destroy');
    

});