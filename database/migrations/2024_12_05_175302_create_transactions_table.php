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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('table_id')->nullable()->index();
            $table->enum('table_type', ['payments', 'withdrawals'])->nullable()->index();
            $table->decimal('amount', 10, 2)->default(0.00)->nullable()->index();
            $table->string('table_type')->comment('wallets, payments, withdrawals')->nullable()->change();
            $table->string('purpose')->default('winning')->comment('winning, referral, redeem, pay, deposit, withdraw')->nullable()->index();

            $table->string('journal_type')->default('debit')->comment('debit, credit')->nullable()->index();
          

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
