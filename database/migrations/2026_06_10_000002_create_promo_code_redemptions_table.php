<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->nullable()->constrained('ticket_types')->nullOnDelete();
            $table->foreignId('ticket_order_id')->nullable()->constrained('ticket_orders')->nullOnDelete();
            $table->foreignId('pending_registration_id')->nullable()->constrained('pending_registrations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('code');
            $table->unsignedInteger('attendee_count')->default(1);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_total', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_redemptions');
    }
};
