<?php
	
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


	return new class extends Migration {
		public function up(): void
		{
		Schema::create('general_notifications', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // null => broadcast

			// Message payload for mobile apps
			$table->string('title');
			$table->text('body')->nullable();


			$table->string('related_type')->nullable(); // e.g., App\\Models\\Booking
			$table->unsignedBigInteger('related_id')->nullable();
			$table->string('related_name')->nullable(); // e.g., "Booking #123" or "Laravel Summit"

			// States & scheduling
			$table->timestamp('scheduled_at')->nullable(); // when to send (optional)
			$table->timestamp('delivered_at')->nullable();
			$table->timestamp('read_at')->nullable();
            $table->boolean('is_read')->default(false);
			$table->json('meta')->nullable(); // arbitrary JSON for clients
			$table->timestamps();


			// Helpful indexes
			$table->index(['user_id', 'read_at']);
			$table->index(['scheduled_at']);
			$table->index(['related_type','related_id']);
		});
	}


public function down(): void
{
Schema::dropIfExists('general_notifications');
}
};