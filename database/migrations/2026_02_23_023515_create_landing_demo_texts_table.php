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
        Schema::create('landing_demo_texts', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('subtitle1')->nullable();
            $table->string('subtitle2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_demo_texts');
    }
};
