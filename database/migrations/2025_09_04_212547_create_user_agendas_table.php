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
        Schema::create('user_agendas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id');
        $table->foreignId('session_id');
        $table->string('agenda_type')->nullable();
        $table->timestamps();

        $table->foreign('session_id')->references('id')->on('event_sessions')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_agendas');
    }
};
