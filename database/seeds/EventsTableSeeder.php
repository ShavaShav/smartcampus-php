<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Event;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Delete existing events
        Event::truncate();

        $faker = \Faker\Factory::create();

        // List of real user ids
        $userIDs= DB::table('users')->lists('id');

        // Create a few events in our database:
        for ($i = 0; $i < 50; $i++) {

            // Event created in past month
            $timestampDate = Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 month', 'now')->getTimestamp());

            // Actual event occurs within couple months
            $eventDate = $timestampDate->copy()
                            ->addDays($faker->numberBetween(0, 60))
                            ->addHours($faker->numberBetween(0, 24));

            Event::create([
                'title' => $faker->sentence,
                'time' => $eventDate->toDateTimeString(), 
                'location' => $faker->address,
                'link' => $faker->url,
                'body' => $faker->paragraph,
                'author_id' => $faker->randomElement($userIDs),
                'created_at' => $timestampDate->toDateTimeString(),
                'updated_at' => $timestampDate->toDateTimeString()
            ]);
        }
    }
}
