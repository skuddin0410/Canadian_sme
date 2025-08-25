<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('booth_id')->constrained('booths')->onDelete('cascade');
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('track')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->text('keynote')->nullable();
            $table->text('demoes')->nullable();
            $table->text('panels')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            $table->enum('type', ['presentation', 'workshop', 'panel', 'break', 'networking'])->default('presentation');
            $table->integer('capacity')->nullable();
            $table->json('metadata')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('event_sessions'); // only drop this table
        Schema::enableForeignKeyConstraints();
    }
}
