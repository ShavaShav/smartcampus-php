<?php

use Illuminate\Database\Seeder;
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

        // Create a few events in our database:
        for ($i = 0; $i < 50; $i++) {
            Event::create([
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
            ]);
        }
    }
}
