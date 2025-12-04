<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Officer;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkDays>
 */
class WorkDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\WorkDays::class;
    public function definition(): array
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']; 

        return [
            'officer_id' => Officer::factory(), // or link to existing officer
            'day_of_week' => $this->faker->randomElement($days),
        ];
    }
}
