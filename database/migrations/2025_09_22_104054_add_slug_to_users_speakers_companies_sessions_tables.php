<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Example of adding slug
        if (!Schema::hasColumn('users', 'slug')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('speakers', 'slug')) {
            Schema::table('speakers', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('companies', 'slug')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('event_sessions', 'slug')) {
            Schema::table('event_sessions', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'slug')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('speakers', 'slug')) {
            Schema::table('speakers', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('companies', 'slug')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('event_sessions', 'slug')) {
            Schema::table('event_sessions', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};