<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one if none exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $posts = [
            [
                'title' => 'Welcome to Laravel Blog',
                'content' => 'This is my first blog post using Laravel. I\'m excited to share my thoughts and experiences with you all.',
            ],
            [
                'title' => 'Understanding Eloquent Relationships',
                'content' => 'Eloquent relationships in Laravel make it easy to work with related data. Today I\'ll explain how to set up one-to-many relationships between users, posts, and comments.',
            ],
            [
                'title' => 'Building a RESTful API with Laravel',
                'content' => 'Laravel makes it incredibly easy to build RESTful APIs. In this post, I\'ll show you how to create endpoints for CRUD operations and implement proper authentication.',
            ],
            [
                'title' => 'Frontend Integration with Vue.js',
                'content' => 'Combining Laravel with Vue.js creates a powerful full-stack application. Let\'s explore how to integrate these technologies for a modern web experience.',
            ],
            [
                'title' => 'Database Migrations Best Practices',
                'content' => 'Migrations are a crucial part of Laravel development. Here are some best practices for writing clean, maintainable database migrations.',
            ],
        ];

        foreach ($posts as $postData) {
            Post::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
            ]);
        }
    }
}
