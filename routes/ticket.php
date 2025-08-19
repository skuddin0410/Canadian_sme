 <?php

    use Illuminate\Support\Facades\Route;

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        // Ticket Categories
        Route::resource('ticket-categories', App\Http\Controllers\TicketCategoryController::class);

        // Ticket Types
        Route::resource('ticket-types', App\Http\Controllers\TicketTypeController::class);
        Route::post('ticket-types/{ticketType}/update-inventory', [App\Http\Controllers\TicketTypeController::class, 'updateInventory'])
            ->name('ticket-types.update-inventory');

        // Ticket Pricing Rules
        Route::resource('ticket-pricing', App\Http\Controllers\TicketPricingController::class);
        Route::patch('ticket-pricing/{ticketPricing}/toggle', [App\Http\Controllers\TicketPricingController::class, 'toggle'])
            ->name('ticket-pricing.toggle');

        // Ticket Inventory Management
        Route::prefix('ticket-inventory')->name('ticket-inventory.')->group(function () {
            Route::get('/', [App\Http\Controllers\TicketInventoryController::class, 'index'])->name('index');
            Route::get('/logs', [App\Http\Controllers\TicketInventoryController::class, 'logs'])->name('logs');
            Route::post('/bulk-update', [App\Http\Controllers\TicketInventoryController::class, 'bulkUpdate'])->name('bulk-update');
        });



    });
