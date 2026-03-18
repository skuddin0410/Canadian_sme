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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('amount')->nullable();
            $table->text('description')->nullable();
            $table->integer('attendee_count')->default(0);
            $table->string('timespan')->nullable()->comment('e.g., 2 months, 3 months');
            $table->boolean('mostpopular')->default(false);
            $table->integer('event_no')->default(1);
            $table->boolean('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
