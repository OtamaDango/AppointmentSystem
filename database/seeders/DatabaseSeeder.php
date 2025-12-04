<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Officer;
use App\Models\Visitor;
use App\Models\Appointment;
use App\Models\Activity;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create  Posts
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

        // Create 5 Officers and assign random Posts
        $postIds = Post::pluck('post_id')->toArray(); // get all post IDs

        Officer::factory()->count(5)->create()->each(function($officer) use ($postIds) {
            $officer->post_id = $postIds[array_rand($postIds)];
            $officer->save();
        });

        //  Create 10 Visitors
        Visitor::factory()->count(10)->create();

        //  Create 15 Appointments with corresponding Activities
        Appointment::factory()->count(15)->create()->each(function($appointment) {
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

        //  Create random Leave/Break/Busy activities for Officers
        $officers = Officer::all();
        foreach ($officers as $officer) {
            Activity::factory()->count(3)->create([
                'officer_id' => $officer->officer_id,
            ]);
        }
    }
}
?>