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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->nullable()->index();
            $table->string('lastname')->nullable()->index();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('website_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->boolean('is_approve')->default(false)->comment('Approval status by admin');
            $table->date('dob')->nullable()->index();
            $table->string('gender')->default('male')->comment('male, female, other')->nullable()->index();
            $table->string('place')->nullable()->index();
            $table->string('street')->nullable()->index();
            $table->string('zipcode')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->string('company_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
