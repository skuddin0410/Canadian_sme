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
        Schema::create('splash_screens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->string('ios_iphone_image')->nullable();
            $table->string('ios_ipad_image')->nullable();
            $table->string('android_hdpi_image')->nullable();
            $table->string('android_mdpi_image')->nullable();
            $table->string('android_xhdpi_image')->nullable();
            $table->string('android_xxhdpi_image')->nullable();
            
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('splash_screens');
    }
};
