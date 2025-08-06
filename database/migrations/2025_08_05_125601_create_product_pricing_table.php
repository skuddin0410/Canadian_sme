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
        Schema::create('product_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('tier_name');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('billing_period')->nullable(); // monthly, yearly, one-time
            $table->json('features')->nullable();
            $table->boolean('is_quote_based')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'is_popular']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricing');
    }
};
