<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Same user+connection pair can now exist across different events.
     * Old: unique(user_id, connection_id)
     * New: unique(user_id, connection_id, event_id)
     */
    public function up(): void
    {
        // MySQL won't let us drop a unique index used by a FK.
        // Add a plain index on user_id first so the FK can use that,
        // then drop the old unique, then add the new composite unique.
        Schema::table('user_connections', function (Blueprint $table) {
            $table->index('user_id', 'user_connections_user_id_index');
        });

        Schema::table('user_connections', function (Blueprint $table) {
            $table->dropUnique('user_connections_user_id_connection_id_unique');
        });

        Schema::table('user_connections', function (Blueprint $table) {
            $table->unique(['user_id', 'connection_id', 'event_id'], 'user_connections_user_connection_event_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_connections', function (Blueprint $table) {
            $table->dropUnique('user_connections_user_connection_event_unique');
            $table->unique(['user_id', 'connection_id'], 'user_connections_user_id_connection_id_unique');
        });

        Schema::table('user_connections', function (Blueprint $table) {
            $table->dropIndex('user_connections_user_id_index');
        });
    }
};
