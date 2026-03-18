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
        Schema::create('pricing_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order_by')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('pricing_feature_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')
                  ->constrained('pricing_features')
                  ->onDelete('cascade');
            $table->foreignId('pricing_id')
                  ->constrained('pricing')
                  ->onDelete('cascade');
            $table->boolean('value')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_feature_values');
        Schema::dropIfExists('pricing_features');
    }
};
