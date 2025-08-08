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
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->longText('description');
    $table->string('features')->nullable();
    $table->string('benefits')->nullable();
    $table->unsignedBigInteger('category_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();       
    $table->unsignedBigInteger('company_id')->nullable();    
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('image_url')->nullable();
    $table->json('gallery_images')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Foreign Keys
    $table->foreign('category_id')->references('id')->on('products_categories')->onDelete('set null');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');          
    $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');  
    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

    // Indexes
    $table->index(['is_active', 'sort_order']);
    $table->index('category_id');
    $table->index('user_id');       // Optional for faster lookup
    $table->index('company_id');    // Optional for faster lookup
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
