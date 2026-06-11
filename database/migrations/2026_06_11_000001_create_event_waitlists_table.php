<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->nullable()->constrained('ticket_types')->nullOnDelete();
            $table->foreignId('form_id')->nullable()->constrained('forms')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('registration_mode')->default('single');
            $table->unsignedInteger('attendee_count')->default(1);
            $table->boolean('coordinator_attending')->default(false);
            $table->json('team_members')->nullable();
            $table->json('request')->nullable();
            $table->string('status')->default('waiting');
            $table->text('notes')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['email', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_waitlists');
    }
};
