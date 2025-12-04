<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Officer>
 */
class OfficerFactory extends Factory
{
    protected $model = \App\Models\Officer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'status' => 'Active',
            'WorkStartTime' => '09:00:00',
            'WorkEndTime' => '17:00:00',
            'post_id' => Post::inRandomOrder()->first()->post_id ?? 1,
        ];
    }
}
