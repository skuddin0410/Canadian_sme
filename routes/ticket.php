 <?php

    use Illuminate\Support\Facades\Route;

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        // Ticket Categories
        Route::resource('ticket-categories', App\Http\Controllers\TicketCategoryController::class);

        // Ticket Types
        Route::resource('ticket-types', App\Http\Controllers\TicketTypeController::class);
        Route::post('ticket-types/{ticketType}/update-inventory', [App\Http\Controllers\TicketTypeController::class, 'updateInventory'])
            ->name('ticket-types.update-inventory');

        Route::get('promo-codes/bulk-create', [App\Http\Controllers\PromoCodeController::class, 'bulkCreate'])
            ->name('promo-codes.bulk-create');
        Route::post('promo-codes/bulk-store', [App\Http\Controllers\PromoCodeController::class, 'bulkStore'])
            ->name('promo-codes.bulk-store');
        Route::get('promo-code-redemptions', [App\Http\Controllers\PromoCodeController::class, 'redemptions'])
            ->name('promo-codes.redemptions');
        Route::resource('promo-codes', App\Http\Controllers\PromoCodeController::class)->except('show');

        Route::get('waitlists', [App\Http\Controllers\EventWaitlistController::class, 'index'])
            ->name('waitlists.index');

        Route::get('ticket-purchases', [App\Http\Controllers\TicketPurchaseController::class, 'index'])
            ->name('ticket-purchases.index');
        Route::post('ticket-purchases/{ticketOrder}/refund', [App\Http\Controllers\TicketPurchaseController::class, 'refund'])
            ->name('ticket-purchases.refund');

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
