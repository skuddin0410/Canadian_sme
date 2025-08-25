<?php
    namespace Database\Factories;


	use App\Models\GeneralNotification;
	use App\Models\User;
	use Illuminate\Database\Eloquent\Factories\Factory;


class GeneralNotificationFactory extends Factory
{
		
	protected $model = GeneralNotification::class;


	public function definition(): array
	{
		return [
			'user_id' => User::factory(),
			'title' => fake()->sentence(4),
			'body' => fake()->paragraph(),
			'related_type' => null,
			'related_id' => null,
			'related_name' => null,
			'scheduled_at' => null,
			'delivered_at' => now(),
			'read_at' => null,
			'meta' => ['priority' => 'normal'],
		];
	}
}