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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('journal_type')->after('type')->default('debit')->comment('debit, credit')->nullable()->index();
            $table->dropIndex(['type']);
            $table->renameColumn('type', 'purpose');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('journal_type');
            $table->dropIndex(['purpose']);
            $table->renameColumn('purpose', 'type');
            $table->index('type');
        });
    }
};
