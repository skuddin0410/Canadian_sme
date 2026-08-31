<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropUnique('promo_codes_code_unique');
            $table->unique(['event_id', 'code'], 'promo_codes_event_id_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropUnique('promo_codes_event_id_code_unique');
            $table->unique('code', 'promo_codes_code_unique');
        });
    }
};
