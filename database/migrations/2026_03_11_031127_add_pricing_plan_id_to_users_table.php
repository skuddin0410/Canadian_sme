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
        Schema::table('users', function (Blueprint $row) {
            $row->unsignedBigInteger('pricing_plan_id')->nullable();
            $row->foreign('pricing_plan_id')->references('id')->on('pricing')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $row) {
            $row->dropForeign(['pricing_plan_id']);
            $row->dropColumn('pricing_plan_id');
        });
    }
};
