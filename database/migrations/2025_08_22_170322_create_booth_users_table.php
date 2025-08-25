<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booth_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('booth_id');
            $table->timestamps();

            // Foreign keys (if you have related tables)
            $table->foreign('session_id')->references('id')->on('event_sessions')->onDelete('cascade');
            $table->foreign('booth_id')->references('id')->on('booths')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booth_users');
    }
};
