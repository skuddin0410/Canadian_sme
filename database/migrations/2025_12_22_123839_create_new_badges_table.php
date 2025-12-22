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
        Schema::create('new_badges', function (Blueprint $table) {
            $table->id();
            $table->string('badge_name');
            $table->string('target')->nullable();
            $table->string('printer')->nullable();
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->json('layout')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_badges');
    }
};
