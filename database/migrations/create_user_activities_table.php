<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // For anonymous users
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // For logged-in users
            $table->string('email')->nullable(); // To link with leads
            $table->string('activity_type'); // page_view, email_open, download, etc.
            $table->string('page_url')->nullable();
            $table->string('page_title')->nullable();
            $table->integer('time_spent')->default(0); // seconds
            $table->json('metadata')->nullable(); // additional data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('activity_at');
            $table->timestamps();
            
            $table->index(['email', 'activity_type']);
            $table->index(['user_id', 'activity_type']);
            $table->index('activity_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
};