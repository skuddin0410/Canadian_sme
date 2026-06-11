<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->foreignId('ticket_order_id')
                ->nullable()
                ->after('id')
                ->constrained('ticket_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_order_id');
        });
    }
};
