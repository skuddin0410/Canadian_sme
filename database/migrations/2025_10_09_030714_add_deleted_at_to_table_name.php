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

        Schema::table('companies', function (Blueprint $table) {
             $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
             $table->softDeletes();
        });

        Schema::table('event_guides', function (Blueprint $table) {
             $table->softDeletes();
        });

        Schema::table('leads', function (Blueprint $table) {
             $table->softDeletes();
        });

        Schema::table('event_sessions', function (Blueprint $table) {
             $table->softDeletes();
        });

        Schema::table('email_templates', function (Blueprint $table) {
             $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('event_guides', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('event_sessions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });


    }
};
