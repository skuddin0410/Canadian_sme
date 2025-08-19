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
        Schema::create('newsletter_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'opened', 'clicked'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->json('click_data')->nullable(); // Track which links clicked
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['newsletter_id', 'status']);
            $table->index(['email', 'status']);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_sends');
    }
};
