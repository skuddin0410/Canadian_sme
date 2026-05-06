<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_notifications', 'event_id')) {
            Schema::table('general_notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('event_id')
                    ->default(1)
                    ->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('general_notifications', 'event_id')) {
            Schema::table('general_notifications', function (Blueprint $table) {
                $table->dropColumn('event_id');
            });
        }
    }
};
