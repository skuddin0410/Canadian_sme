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
        Schema::create('ticket_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['increase', 'decrease', 'reserve', 'release']);
            $table->integer('quantity')->nullable();
            $table->integer('previous_quantity')->nullable();
            $table->integer('new_quantity')->nullable();
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_inventory_logs');
    }
};
