<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => env('ADMIN_NAME1'),
            'email' => env('ADMIN_EMAIL1'),
            'password' => bcrypt(env('ADMIN_PASSWORD1')),
            'is_activated' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => env('ADMIN_NAME2'),
            'email' => env('ADMIN_EMAIL2'),
            'password' => bcrypt(env('ADMIN_PASSWORD2')),
            'is_activated' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => env('ADMIN_NAME3'),
            'email' => env('ADMIN_EMAIL3'),
            'password' => bcrypt(env('ADMIN_PASSWORD3')),
            'is_activated' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => env('ADMIN_NAME4'),
            'email' => env('ADMIN_EMAIL4'),
            'password' => bcrypt(env('ADMIN_PASSWORD4')),
            'is_activated' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => env('ADMIN_NAME5'),
            'email' => env('ADMIN_EMAIL5'),
            'password' => bcrypt(env('ADMIN_PASSWORD5')),
            'is_activated' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => config('app.admin_name'),
            'email' => config('app.admin_email'),
            'password' => bcrypt(env('ADMIN_PASSWORD5')),
            'is_activated' => false,
            'is_admin' => false,
        ]);

    }
}
