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
        Schema::create('landing_home_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('status')->default(1); // 1: Active, 0: Inactive
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_home_reviews');
    }
};
