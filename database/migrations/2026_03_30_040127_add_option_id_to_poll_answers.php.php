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
        Schema::table('poll_answers', function (Blueprint $table) {
            $table->foreignId('option_id')
                ->nullable()
                ->constrained('poll_question_options')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poll_answers', function (Blueprint $table) {
            $table->dropForeign(['option_id']);
            $table->dropColumn('option_id');
        });
    }
};
