<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('enable_team_registration')->default(true)->after('section_order');
            $table->boolean('enable_free_registration')->default(true)->after('enable_team_registration');
            $table->boolean('enable_paid_registration')->default(true)->after('enable_free_registration');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'enable_team_registration',
                'enable_free_registration',
                'enable_paid_registration',
            ]);
        });
    }
};
