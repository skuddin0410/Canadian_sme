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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('added_by')
              ->nullable()
             ->constrained('users')
             ->onDelete('cascade');
            $table->enum('status', ['pending', 'inprogress', 'completed'])
           ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};
