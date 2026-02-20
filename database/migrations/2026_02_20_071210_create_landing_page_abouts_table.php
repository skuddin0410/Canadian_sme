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
        Schema::create('landing_page_abouts', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('sub_heading')->nullable();
            $table->text('description')->nullable();
            $table->text('desc_points')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('banner_button_link')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('exp_text')->nullable();
            $table->string('bg_banner')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('front_image')->nullable();
            $table->string('banner_button_image')->nullable();
            $table->string('exp_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_abouts');
    }
};
