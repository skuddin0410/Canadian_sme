<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('id');
            $table->index(['event_id', 'file_type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'file_type', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
