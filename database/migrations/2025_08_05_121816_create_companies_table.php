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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Basic Company Details
            $table->string('name')->nullable();
            $table->foreignId('booth_id')->nullable()->constrained('booths')->onDelete('cascade');
            $table->boolean('is_sponsor')->default(false);
            $table->string('industry')->nullable();
            $table->string('size')->nullable();
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Rich Text Description
            $table->text('description')->nullable();

            // Website & Social Links
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();

            // Certifications (can be comma-separated or structured later)
            $table->text('certifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
