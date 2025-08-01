<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  
use App\Http\Controllers\ExhibitorAdmin\CompanyController;
use App\Http\Controllers\ExhibitorAdmin\CompanyContactController;

Route::group(['middleware' => ['webauth', 'role:Exhibitor Admin']], function () {
    Route::get('company/details', [CompanyController::class, 'details'])->name('company.details');
    // Handles showing the description editor
Route::get('company/description', [CompanyController::class, 'editDescription'])->name('company.description');

// Handles updating the description
Route::post('company/description/update', [CompanyController::class, 'updateDescription'])->name('company.description.update');
Route::get('company/websites', [CompanyController::class, 'websites'])->name('company.websites');
    Route::put('company/websites', [CompanyController::class, 'updateWebsites'])->name('company.websites.update');
    Route::get('company/certifications', [CompanyController::class, 'certifications'])->name('company.certifications');
    Route::put('company/certifications', [CompanyController::class, 'updateCertifications'])->name('company.certifications.update');
    Route::get('company/contacts', [CompanyContactController::class, 'index'])->name('company.contacts');
Route::post('company/contacts', [CompanyContactController::class, 'store'])->name('company.contacts.store');
Route::delete('company/contacts/{contact}', [CompanyContactController::class, 'destroy'])->name('company.contacts.destroy');


     Route::resource('company', CompanyController::class);

	
});

Route::group(['middleware' => ['webauth', 'role:Exhibitor Representative']], function () {

	
});