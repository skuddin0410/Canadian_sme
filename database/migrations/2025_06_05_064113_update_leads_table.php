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
         Schema::table('leads', function($table) { 
            $table->integer('page_views')->default(0);
            $table->integer('time_on_site')->default(0);
            $table->integer('email_opens')->default(0);
            $table->integer('downloads')->default(0);
            $table->integer('form_submissions')->default(0);
            $table->integer('property_inquiries')->default(0);
            $table->integer('ai_score')->nullable();
            $table->text('ai_reasoning')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function($table) {
            $table->dropColumn('page_views');
            $table->dropColumn('time_on_site');
            $table->dropColumn('email_opens');
            $table->dropColumn('downloads');
            $table->dropColumn('form_submissions');
            $table->dropColumn('property_inquiries');
            $table->dropColumn('ai_score');
            $table->dropColumn('ai_reasoning');
        });
    }
};
