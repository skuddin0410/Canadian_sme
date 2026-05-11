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
        // 1. Find the actual unique key name on this DB (may vary between environments)
        $uniqueKeyName = $this->getIndexName('user_connections', ['user_id', 'connection_id']);

        if ($uniqueKeyName) {
            // Add a plain index on user_id first so the FK can survive the unique drop
            if (!$this->indexExists('user_connections', 'user_connections_user_id_index')) {
                Schema::table('user_connections', function (Blueprint $table) {
                    $table->index('user_id', 'user_connections_user_id_index');
                });
            }

            Schema::table('user_connections', function (Blueprint $table) use ($uniqueKeyName) {
                $table->dropUnique($uniqueKeyName);
            });
        }

        // 2. Add the new composite unique (if not already present)
        if (!$this->indexExists('user_connections', 'user_connections_user_connection_event_unique')) {
            Schema::table('user_connections', function (Blueprint $table) {
                $table->unique(['user_id', 'connection_id', 'event_id'], 'user_connections_user_connection_event_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('user_connections', 'user_connections_user_connection_event_unique')) {
            Schema::table('user_connections', function (Blueprint $table) {
                $table->dropUnique('user_connections_user_connection_event_unique');
            });
        }

        if (!$this->getIndexName('user_connections', ['user_id', 'connection_id'])) {
            Schema::table('user_connections', function (Blueprint $table) {
                $table->unique(['user_id', 'connection_id'], 'user_connections_user_id_connection_id_unique');
            });
        }

        if ($this->indexExists('user_connections', 'user_connections_user_id_index')) {
            Schema::table('user_connections', function (Blueprint $table) {
                $table->dropIndex('user_connections_user_id_index');
            });
        }
    }

    /**
     * Check if a specific index name exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Find the name of a unique index that covers exactly the given columns.
     */
    private function getIndexName(string $table, array $columns): ?string
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Non_unique = 0");

        $grouped = [];
        foreach ($indexes as $idx) {
            $grouped[$idx->Key_name][] = $idx->Column_name;
        }

        foreach ($grouped as $name => $cols) {
            if ($cols === $columns) {
                return $name;
            }
        }

        return null;
    }
};
