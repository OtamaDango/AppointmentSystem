<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = \App\Models\Activity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = (clone $startDate)->modify('+1 day');

        return [
            'type' => $this->faker->randomElement(['Leave', 'Break', 'Busy']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'start_time' => $startDate->format('H:i'),
            'end_time' => (clone $startDate)->modify('+2 hours')->format('H:i'),
            'status' => 'Active',
            'appointment_id' => null,
       ];
    }
}
