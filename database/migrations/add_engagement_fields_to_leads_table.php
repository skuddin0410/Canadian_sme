<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'page_views')) {
                $table->integer('page_views')->default(0);
            }
            if (!Schema::hasColumn('leads', 'time_on_site')) {
                $table->integer('time_on_site')->default(0); // in minutes
            }
            if (!Schema::hasColumn('leads', 'email_opens')) {
                $table->integer('email_opens')->default(0);
            }
            if (!Schema::hasColumn('leads', 'downloads')) {
                $table->integer('downloads')->default(0);
            }
            if (!Schema::hasColumn('leads', 'form_submissions')) {
                $table->integer('form_submissions')->default(0);
            }
            if (!Schema::hasColumn('leads', 'property_inquiries')) {
                $table->integer('property_inquiries')->default(0);
            }
            if (!Schema::hasColumn('leads', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'page_views', 'time_on_site', 'email_opens', 
                'downloads', 'form_submissions', 'property_inquiries', 'last_activity_at'
            ]);
        });
    }
};
