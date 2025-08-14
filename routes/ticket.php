 <?php

    use Illuminate\Support\Facades\Route;

    Route::get('/tickets', function () {
        return view('tickets.index');
    })->name('ticket.dashboard');
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

        // Enhanced Event Tickets (existing table with new features)
        Route::resource('event-tickets', App\Http\Controllers\EventTicketController::class);
        Route::prefix('event-tickets')->name('event-tickets.')->group(function () {
            Route::post('{eventTicket}/duplicate', [App\Http\Controllers\EventTicketController::class, 'duplicate'])->name('duplicate');
            Route::patch('{eventTicket}/toggle-status', [App\Http\Controllers\EventTicketController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('export/{event}', [App\Http\Controllers\EventTicketController::class, 'export'])->name('export');
        });

        // Ticket Analytics & Reports
        // Route::prefix('ticket-reports')->name('ticket-reports.')->group(function () {
        //     Route::get('/', [App\Http\Controllers\TicketReportController::class, 'index'])->name('index');
        //     Route::get('/sales-summary', [App\Http\Controllers\TicketReportController::class, 'salesSummary'])->name('sales-summary');
        //     Route::get('/inventory-report', [App\Http\Controllers\TicketReportController::class, 'inventoryReport'])->name('inventory-report');
        //     Route::get('/pricing-analysis', [App\Http\Controllers\TicketReportController::class, 'pricingAnalysis'])->name('pricing-analysis');
        //     Route::get('/export/{type}', [App\Http\Controllers\TicketReportController::class, 'export'])->name('export');
        // });

        // // Promo Codes Management
        // Route::resource('promo-codes', App\Http\Controllers\PromoCodeController::class);
        // Route::prefix('promo-codes')->name('promo-codes.')->group(function () {
        //     Route::post('bulk-generate', [App\Http\Controllers\PromoCodeController::class, 'bulkGenerate'])->name('bulk-generate');
        //     Route::patch('{promoCode}/toggle', [App\Http\Controllers\PromoCodeController::class, 'toggle'])->name('toggle');
        //     Route::get('usage-report/{promoCode}', [App\Http\Controllers\PromoCodeController::class, 'usageReport'])->name('usage-report');
        // });
    });

    Route::prefix('admin')->group(function () {

        // Get ticket types by event
        Route::get('events/{event}/ticket-types', function (App\Models\Event $event) {
            return response()->json($event->ticketTypes()->active()->get(['id', 'name', 'base_price', 'available_quantity']));
        })->name('events.ticket-types');

        // Get pricing rules for ticket type
        Route::get('ticket-types/{ticketType}/pricing-rules', function (App\Models\TicketType $ticketType) {
            return response()->json($ticketType->pricingRules()->active()->get());
        })->name('ticket-types.pricing-rules');

        // Calculate ticket price
        Route::post('ticket-types/{ticketType}/calculate-price', function (
            Request $request,
            App\Models\TicketType $ticketType
        ) {
            $quantity = $request->input('quantity', 1);
            $promoCode = $request->input('promo_code');

            $price = $ticketType->getCurrentPrice($quantity, $promoCode);
            $applicableRules = $ticketType->getApplicablePricingRules($quantity, $promoCode);

            return response()->json([
                'price' => $price,
                'total' => $price * $quantity,
                'base_price' => $ticketType->base_price,
                'discount' => $ticketType->base_price - $price,
                'applicable_rules' => $applicableRules
            ]);
        })->name('ticket-types.calculate-price');

        // Validate promo code
        Route::post('promo-codes/validate', function (Request $request) {
            $code = $request->input('code');
            $ticketTypeId = $request->input('ticket_type_id');
            $quantity = $request->input('quantity', 1);

            $promoCode = App\Models\PromoCode::where('code', $code)->first();

            if (!$promoCode) {
                return response()->json(['valid' => false, 'message' => 'Invalid promo code']);
            }

            if (!$promoCode->canBeUsed($ticketTypeId, $quantity)) {
                return response()->json(['valid' => false, 'message' => 'Promo code cannot be used']);
            }

            return response()->json([
                'valid' => true,
                'discount_type' => $promoCode->discount_type,
                'discount_amount' => $promoCode->discount_amount,
                'discount_percentage' => $promoCode->discount_percentage
            ]);
        })->name('promo-codes.validate');

        // Get inventory stats
        Route::get('inventory/stats', function () {
            $stats = [
                'total_tickets' => App\Models\TicketType::sum('total_quantity'),
                'available_tickets' => App\Models\TicketType::sum('available_quantity'),
                'low_stock_count' => App\Models\TicketType::whereRaw('available_quantity <= (total_quantity * 0.1)')
                    ->where('available_quantity', '>', 0)
                    ->count(),
                'sold_out_count' => App\Models\TicketType::where('available_quantity', 0)->count(),
                'recent_bookings' => App\Models\EventTicketBooking::where('created_at', '>=', now()->subHours(24))->count()
            ];

            $stats['sold_tickets'] = $stats['total_tickets'] - $stats['available_tickets'];

            return response()->json($stats);
        })->name('inventory.stats');
    });


    Route::prefix('admin/dashboard')->name('admin.dashboard.')->group(function () {

        // Ticket sales widget data
        Route::get('ticket-sales-data', function () {
            $salesData = App\Models\EventTicketBooking::selectRaw('DATE(created_at) as date, COUNT(*) as bookings, SUM(total_amount) as revenue')
                ->where('created_at', '>=', now()->subDays(30))
                ->where('status', 'confirmed')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json($salesData);
        })->name('ticket-sales-data');

        // Top selling tickets
        Route::get('top-selling-tickets', function () {
            $topTickets = App\Models\EventTicket::select('event_tickets.*')
                ->selectRaw('SUM(event_ticket_bookings.quantity) as total_sold')
                ->leftJoin('event_ticket_bookings', 'event_tickets.id', '=', 'event_ticket_bookings.ticket_id')
                ->where('event_ticket_bookings.status', 'confirmed')
                ->groupBy('event_tickets.id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            return response()->json($topTickets);
        })->name('top-selling-tickets');

        // Recent ticket activity
        Route::get('recent-ticket-activity', function () {
            $recentActivity = App\Models\TicketInventoryLog::with(['ticketType', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json($recentActivity);
        })->name('recent-ticket-activity');
    });
