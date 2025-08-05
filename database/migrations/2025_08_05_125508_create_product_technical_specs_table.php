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
        Schema::create('product_technical_specs', function (Blueprint $table) {
            $table->id();
              $table->unsignedBigInteger('product_id');
            $table->string('spec_name');
            $table->text('spec_value');
            $table->string('spec_unit')->nullable();
            $table->string('spec_category')->nullable();
            $table->boolean('is_important')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'is_important']);
            $table->index(['product_id', 'spec_category']);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_technical_specs');
    }
};
