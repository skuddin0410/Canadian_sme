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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
             $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->json('preferences')->nullable(); // Email preferences
            $table->json('tags')->nullable(); // Subscriber tags
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced'])->default('subscribed');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('subscription_source')->nullable(); // How they subscribed
            $table->timestamps();
            
            $table->index(['email', 'status']);
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
