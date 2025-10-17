<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed roles and permissions first
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Seed posts and comments
        $this->call([
            PostSeeder::class,
            CommentSeeder::class,
        ]);

        // Assign admin role to first user
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $firstUser = \App\Models\User::first();
        if ($adminRole && $firstUser) {
            $firstUser->roles()->attach($adminRole);
        }
    }
}
