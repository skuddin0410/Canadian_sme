<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;






  Route::resource('leads', LeadController::class);
  Route::get('leads/export/{format?}', [LeadController::class, 'export'])->name('leads.export');

//   Route::get('lead-capture', [LeadController::class, 'capture'])->name('leads.capture');
//         Route::post('lead-capture', [LeadController::class, 'storeCaptured'])->name('leads.store-captured');
//             Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('ai.leads.show');
//             Route::post('/leads/analyze', [LeadController::class, 'analyzeAll'])->name('ai.leads.analyze');