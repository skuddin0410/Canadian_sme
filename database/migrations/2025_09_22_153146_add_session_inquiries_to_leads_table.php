<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add the column if it doesn't already exist
            if (!Schema::hasColumn('leads', 'session_inquiries')) {
                $table->unsignedInteger('session_inquiries')
                      ->default(0)
                      ->after('form_submissions'); // adjust position as needed
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'session_inquiries')) {
                $table->dropColumn('session_inquiries');
            }
        });
    }
};
