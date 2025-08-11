<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  
use App\Http\Controllers\ExhibitorAdmin\BoothController;
use App\Http\Controllers\ExhibitorAdmin\CompanyController;
use App\Http\Controllers\ExhibitorAdmin\PricingController;
use App\Http\Controllers\ExhibitorAdmin\ProductController;
use App\Http\Controllers\ExhibitorAdmin\ServiceController;
use App\Http\Controllers\ExhibitorAdmin\TrainingController;
use App\Http\Controllers\ExhibitorAdmin\CompanyContactController;
use App\Http\Controllers\ExhibitorAdmin\ProductCategoryController;
use App\Http\Controllers\ExhibitorAdmin\ServiceCategoryController;
use App\Http\Controllers\ExhibitorAdmin\ProductTechnicalSpecController;
use App\Http\Controllers\ExhibitorAdmin\PublicProductServiceController;


Route::group(['middleware' => ['webauth', 'role:Exhibitor Admin']], function () {
    Route::get('company/details', [CompanyController::class, 'details'])->name('company.details');


    Route::get('company/contacts', [CompanyContactController::class, 'index'])->name('company.contacts.index');
    Route::get('company/contacts/create', [CompanyContactController::class, 'create'])->name('company.contacts.create');
    Route::post('company/contacts', [CompanyContactController::class, 'store'])->name('company.contacts.store');
    Route::delete('company/contacts/{contact}', [CompanyContactController::class, 'destroy'])->name('company.contacts.destroy');


    
    Route::get('/company/media-gallery', [CompanyController::class, 'mediaGallery'])->name('company.media.gallery');
    Route::post('/company/media-upload', [CompanyController::class, 'uploadMedia'])->name('company.media.upload');
    Route::delete('/company/media/{id}', [CompanyController::class, 'deleteMedia'])->name('company.media.delete');

    Route::get('/company/videos', [CompanyController::class, 'videoGallery'])->name('company.videos.gallery');
    Route::post('/company/videos/upload', [CompanyController::class, 'uploadVideo'])->name('company.videos.upload');
    Route::delete('/company/videos/{id}', [CompanyController::class, 'deleteVideo'])->name('company.videos.delete');


    Route::resource('trainings', TrainingController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('booths', BoothController::class);

    // Product Categories
    Route::resource('product-categories', ProductCategoryController::class);
    
    // Service Categories
    Route::resource('service-categories', ServiceCategoryController::class);
    
    // Products
    Route::resource('products', ProductController::class);

    // Product Technical Specifications
    Route::prefix('products/{product}')->name('products.')->group(function () {
        Route::post('technical-specs', [ProductTechnicalSpecController::class, 'store'])->name('specs.store');
        Route::put('technical-specs/{spec}', [ProductTechnicalSpecController::class, 'update'])->name('specs.update');
        Route::delete('technical-specs/{spec}', [ProductTechnicalSpecController::class, 'destroy'])->name('specs.destroy');
    });
    // Services
    Route::resource('services', ServiceController::class);

});

// Public Routes
Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('products', [PublicProductServiceController::class, 'products'])->name('products');
    Route::get('products/{slug}', [PublicProductServiceController::class, 'productDetail'])->name('products.show');
    Route::get('services', [PublicProductServiceController::class, 'services'])->name('services');
    Route::get('services/{slug}', [PublicProductServiceController::class, 'serviceDetail'])->name('services.show');
});

Route::group(['middleware' => ['webauth', 'role:Exhibitor Representative']], function () {

	
});