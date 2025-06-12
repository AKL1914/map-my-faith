<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulkPinsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create 350 fake users
        //with faker emails and names
        // the area field will be randomly assigned with 1,2,3,4,5,6
        //activated field will be 1
        //cfo field will be  randomly assigned with BUKLOD,KADIWA,BINHI
//        \App\Models\User::factory(350)->create()->each(function ($user) {
//            $user->update([
//                'area' => rand(1, 6),
//                'is_activated' => 1,
//                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
//            ]);
//        });
        //area1
        //get only ids of the created users
        \App\Models\User::factory(7)->create()->each(function ($user) {
            $user->update([
                'area' => 1,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });
        \App\Models\User::factory(6)->create()->each(function ($user) {
            $user->update([
                'area' => 2,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });
        \App\Models\User::factory(4)->create()->each(function ($user) {
            $user->update([
                'area' => 3,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });

        \App\Models\User::factory(9)->create()->each(function ($user) {
            $user->update([
                'area' => 4,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });
        \App\Models\User::factory(5)->create()->each(function ($user) {
            $user->update([
                'area' => 5,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });
        \App\Models\User::factory(4)->create()->each(function ($user) {
            $user->update([
                'area' => 6,
                'is_activated' => 1,
                'cfo' => collect(['BUKLOD', 'KADIWA', 'BINHI'])->random(),
            ]);
        });
        $usersIds = User::where('area',1)->pluck('id')->toArray();
        //create 1500 pin
        //pins will be randomly assigned to the users created above
        //with random lat and lng values from Auckland, New Zealand
        //the created_at field will be randomly assigned within the last 30 days
        for ($i = 0; $i < 480; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }

        $usersIds = User::where('area',2)->pluck('id')->toArray();
        for ($i = 0; $i < 729; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }
        $usersIds = User::where('area',3)->pluck('id')->toArray();
        for ($i = 0; $i < 58; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }

        $usersIds = User::where('area',4)->pluck('id')->toArray();
        for ($i = 0; $i < 291; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }

        $usersIds = User::where('area',5)->pluck('id')->toArray();
        for ($i = 0; $i < 144; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }

        $usersIds = User::where('area',6)->pluck('id')->toArray();
        for ($i = 0; $i < 139; $i++) {
            DB::table('pins')->insert([
                'user_id' => collect($usersIds)->random(), // Randomly assign a user from the created users
                'latitude' => rand(-37.0, -36.5), // Random latitude in Auckland
                'longitude' => rand(174.5, 175.5), // Random longitude in Auckland
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'is_accepted' => 1,
                'campaign_id' => 1, // Assuming you want to assign all pins to campaign with ID 1
            ]);

        }


    }
}
