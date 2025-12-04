<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Officer;
use App\Models\Visitor;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = \App\Models\Appointment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $officer = Officer::inRandomOrder()->first();
        $visitor = Visitor::inRandomOrder()->first();
        $date = $this->faker->dateTimeBetween('now', '+1 month');
    
        return [
            'officer_id' => $officer->officer_id,
            'visitor_id' => $visitor->visitor_id,
            'name' => $this->faker->sentence(3),
            'date' => $date->format('Y-m-d'),
            'StartTime' => $date->format('H:i'),
            'EndTime' => $date->modify('+1 hour')->format('H:i'),
            'status' => 'Active',
            'AddedOn' => now(),
        ];
    }
}
