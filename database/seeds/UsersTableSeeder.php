<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's clear the users table first
        User::truncate();

        $faker = \Faker\Factory::create();

        // Use same hash for all users, for speed.
        $password = Hash::make('toptal');

        // Create "admin"
        User::create([
            'username' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => $password,
        ]);

        // Create some regular users
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'username' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
            ]);
        }
    }
}
