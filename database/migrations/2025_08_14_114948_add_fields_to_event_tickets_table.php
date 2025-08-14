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
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable()->after('session_id')->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->after('ticket_type_id')->constrained('ticket_categories')->onDelete('set null');
            $table->string('sku')->nullable()->after('name');
            $table->text('description')->nullable()->after('sku');
            $table->json('features')->nullable()->after('description'); // Ticket features/benefits
            $table->enum('status', ['active', 'inactive', 'sold_out', 'archived'])->default('active')->after('group_size');
            $table->datetime('sale_start_date')->nullable()->after('status');
            $table->datetime('sale_end_date')->nullable()->after('sale_start_date');
            $table->integer('sort_order')->default(0)->after('sale_end_date');
            $table->json('metadata')->nullable()->after('sort_order'); // Additional flexible data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'ticket_type_id', 'category_id', 'sku', 'description', 'features',
                'status', 'sale_start_date', 'sale_end_date', 'sort_order', 'metadata'
            ]);
        });
    }
};
