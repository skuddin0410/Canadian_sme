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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->text('response')->nullable();
            $table->string('status')->default('init')->comment('init, success, failed')->nullable()->index();
            $table->dropIndex(['transaction_id']);
            $table->renameColumn('transaction_id', 'reference');
            $table->index('reference');
            $table->decimal('amount', 10, 2)->after('response')->default(0.00)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
