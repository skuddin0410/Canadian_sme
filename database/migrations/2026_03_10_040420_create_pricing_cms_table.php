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
        Schema::create('pricing_cms', function (Blueprint $table) {
            $table->id();
            $table->string('main_heading')->nullable();
            $table->text('main_description')->nullable();
            $table->string('Feature_heading')->nullable();
            $table->text('Feature_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_cms');
    }
};
