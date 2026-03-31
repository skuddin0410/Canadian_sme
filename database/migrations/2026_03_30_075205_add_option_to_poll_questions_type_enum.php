<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('poll_questions_type_enum', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE poll_questions
            MODIFY COLUMN type ENUM('text','yes_no','rating','option') NOT NULL
        ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poll_questions_type_enum', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE poll_questions
            MODIFY COLUMN type ENUM('text','yes_no','rating') NOT NULL
        ");
        });
    }
};
