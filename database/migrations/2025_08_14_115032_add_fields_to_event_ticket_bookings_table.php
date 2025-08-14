<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_ticket_bookings', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_price');
            $table->string('discount_type')->nullable()->after('discount_amount'); // early_bird, group, promo
            $table->string('promo_code')->nullable()->after('discount_type');
            $table->json('attendee_details')->nullable()->after('promo_code'); // Store attendee info
            $table->json('metadata')->nullable()->after('attendee_details');
            $table->timestamp('confirmed_at')->nullable()->after('booked_at');
            $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_ticket_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price', 'discount_amount', 'discount_type', 'promo_code',
                'attendee_details', 'metadata', 'confirmed_at', 'cancelled_at', 'cancellation_reason'
            ]);
        });
    }
};
