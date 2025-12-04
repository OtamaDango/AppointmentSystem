<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Officer;
use App\Models\WorkDays;
use App\Models\Visitor;
use App\Models\Appointment;
use App\Models\Activity;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //  Create Posts
        $posts = [
            ['name' => 'Manager', 'status' => 'Active'],
            ['name' => 'Receptionist', 'status' => 'Active'],
            ['name' => 'Security', 'status' => 'Active'],
            ['name' => 'Clerk', 'status' => 'Active'],
            ['name' => 'Supervisor', 'status' => 'Active'],
        ];

        foreach ($posts as $postData) {
            Post::create($postData);
        }

        // Create Officers and assign random Posts
        $postIds = Post::pluck('post_id')->toArray();

        Officer::factory()->count(5)->create()->each(function ($officer) use ($postIds) {
            $officer->post_id = $postIds[array_rand($postIds)];
            $officer->save();
        });

        $officers = Officer::all();

        //  Create WorkDays for each officer (Mon-Fri)
        foreach ($officers as $officer) {
            foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day) {
                WorkDays::create([
                    'officer_id' => $officer->officer_id,
                    'day_of_week' => $day,
                ]);
            }
        }

        //  Create Visitors
        Visitor::factory()->count(10)->create();

        //  Create Appointments + Appointment-type Activities
        Appointment::factory()->count(15)->create()->each(function ($appointment) {
            Activity::create([
                'officer_id' => $appointment->officer_id,
                'type' => 'Appointment',
                'start_date' => $appointment->date,
                'end_date' => $appointment->date,
                'start_time' => $appointment->StartTime,
                'end_time' => $appointment->EndTime,
                'status' => 'Active',
                'appointment_id' => $appointment->appointment_id,
            ]);
        });

        //  Create Leave/Break/Busy Activities for Officers
        foreach ($officers as $officer) {
            // Future Active activities
            Activity::factory()->count(2)->create([
                'officer_id' => $officer->officer_id,
                'status' => 'Active',
                'start_date' => Carbon::now()->addDays(rand(1,5))->toDateString(),
                'end_date' => Carbon::now()->addDays(rand(1,5))->toDateString(),
            ]);

            // Past Completed activities
            Activity::factory()->count(1)->create([
                'officer_id' => $officer->officer_id,
                'status' => 'Active', // display_status will show Completed
                'start_date' => Carbon::now()->subDays(rand(2,5))->toDateString(),
                'end_date' => Carbon::now()->subDays(rand(2,5))->toDateString(),
            ]);

            // Past Cancelled activities
            Activity::factory()->count(1)->create([
                'officer_id' => $officer->officer_id,
                'status' => 'Cancelled', // will stay Cancelled
                'start_date' => Carbon::now()->subDays(rand(6,10))->toDateString(),
                'end_date' => Carbon::now()->subDays(rand(6,10))->toDateString(),
            ]);
        }
    }
}
