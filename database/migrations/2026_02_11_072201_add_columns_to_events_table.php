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
        Schema::table('events', function (Blueprint $table) {
            // terms$condition , privacy polciy, about 
                $table->longText('terms_condition')->nullable();
                $table->longText('privacy_policy')->nullable();
                $table->longText('about')->nullable();
                $table->longText('help_support')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
                $table->dropColumn('terms_condition');
                $table->dropColumn('privacy_policy');
                $table->dropColumn('about');
                $table->dropColumn('help_support');
        });
    }
};
