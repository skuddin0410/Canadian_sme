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
        Schema::dropIfExists('session_speakers');
        Schema::create('session_speakers', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('session_id');
        $table->unsignedBigInteger('speaker_id');
        $table->string('role')->nullable();
        $table->timestamps();

        $table->foreign('session_id')->references('id')->on('event_sessions')->onDelete('cascade');
        //$table->foreign('speaker_id')->references('id')->on('speakers')->onDelete('cascade');

        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_speakers');
    }
};
