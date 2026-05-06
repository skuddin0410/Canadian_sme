<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('favorite_connections', 'event_id')) {
            Schema::table('favorite_connections', function (Blueprint $table) {
                $table->unsignedBigInteger('event_id')
                    ->default(1)
                    ->after('connection_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('favorite_connections', 'event_id')) {
            Schema::table('favorite_connections', function (Blueprint $table) {
                $table->dropColumn('event_id');
            });
        }
    }
};
