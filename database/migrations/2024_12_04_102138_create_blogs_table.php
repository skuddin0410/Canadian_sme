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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->integer('category')->nullable()->index();
            $table->string('tags')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable()->index();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable()->index();
            $table->unsignedBigInteger('created_by')->after('meta_keywords')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
