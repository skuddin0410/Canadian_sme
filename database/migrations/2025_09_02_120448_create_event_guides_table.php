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
        Schema::create('event_guides', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable(); 
            $table->string('title');  
            $table->string('type')->nullable();                   
            $table->string('weblink')->nullable();  
            $table->string('doc')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_guides');
    }
};
