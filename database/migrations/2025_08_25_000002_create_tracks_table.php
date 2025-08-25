<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
	public function up(): void
	{
		Schema::create('tracks', function (Blueprint $table) {
		$table->id();
		$table->string('name');
		$table->string('slug')->nullable();
		$table->text('description')->nullable();
		$table->unsignedInteger('order')->default(0);
		$table->timestamps();
		$table->unique(['name']);
		});
	}


	public function down(): void
	{
	 Schema::dropIfExists('tracks');
	}
};