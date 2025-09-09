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
        Schema::table('user_connections', function (Blueprint $table) {
            $table->enum('rating', ['Cold', 'Normal', 'Warm'])
                  ->default('Normal')
                  ->after('connection_id');
            $table->string('note', 500)->nullable()->after('rating');
            $table->index('rating');
        });

         Schema::table('users', function (Blueprint $table) {
            $table->string('title', 6)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_connections', function (Blueprint $table) {
            $table->dropIndex(['rating']);
            $table->dropColumn(['rating', 'note']);
        });

         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['title']);
        });
    }
};
