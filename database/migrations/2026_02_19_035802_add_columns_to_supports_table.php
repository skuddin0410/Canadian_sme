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
        Schema::table('supports', function (Blueprint $table) {
            // add phone after email and name before email and location after phone and subject after location
             $table->string('name')->after('id')->nullable();
            //  $table->string('email')->after('name');
             $table->string('phone')->after('email')->nullable();
             $table->string('location')->after('phone')->nullable();
            //  $table->string('subject')->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone', 'location']);
        });
    }
};
