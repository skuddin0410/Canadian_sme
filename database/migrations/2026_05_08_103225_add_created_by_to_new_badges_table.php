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
        Schema::table('new_badges', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_badges', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
