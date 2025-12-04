<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        // List of realistic post names
        $realPosts = [
            'Manager',
            'Receptionist',
            'Security',
            'Clerk',
            'Supervisor'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($realPosts),
            'status' => 'Active',
        ];
    }
}
