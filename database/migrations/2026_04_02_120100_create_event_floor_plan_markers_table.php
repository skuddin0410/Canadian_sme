<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_floor_plan_markers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booth_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->decimal('x_percent', 8, 3)->default(10);
            $table->decimal('y_percent', 8, 3)->default(10);
            $table->decimal('width_percent', 8, 3)->default(12);
            $table->decimal('height_percent', 8, 3)->default(8);
            $table->string('color', 20)->default('#4361ee');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_floor_plan_markers');
    }
};
