<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('email_branding_type')->nullable()->after('youtube_link');
            $table->string('email_branding_name')->nullable()->after('email_branding_type');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['email_branding_type', 'email_branding_name']);
        });
    }
};
