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
            [
                'title' => 'Laravel Authentication and Authorization',
                'content' => 'Learn how to implement secure authentication and authorization in your Laravel applications using built-in features and custom policies.',
            ],
            [
                'title' => 'Vue.js Component Architecture',
                'content' => 'Understanding component architecture in Vue.js is essential for building scalable frontend applications. Let\'s dive into best practices.',
            ],
            [
                'title' => 'API Testing with Laravel',
                'content' => 'Testing your API endpoints is crucial for maintaining code quality. Learn how to write comprehensive tests for your Laravel API.',
            ],
            [
                'title' => 'State Management with Pinia',
                'content' => 'Pinia is the modern state management solution for Vue.js applications. Discover how to manage complex application state effectively.',
            ],
            [
                'title' => 'Laravel Blade Templates',
                'content' => 'Blade templating engine provides a powerful way to create dynamic views in Laravel. Explore advanced features and best practices.',
            ],
            [
                'title' => 'Vue Router Navigation',
                'content' => 'Master Vue Router to create seamless navigation experiences in your single-page applications with proper route guards and lazy loading.',
            ],
            [
                'title' => 'Laravel Queue System',
                'content' => 'Queues in Laravel allow you to defer time-consuming tasks, improving your application\'s performance and user experience.',
            ],
            [
                'title' => 'Responsive Design with Tailwind CSS',
                'content' => 'Create beautiful, responsive designs using Tailwind CSS utility classes. Learn advanced techniques for modern web development.',
            ],
            [
                'title' => 'Laravel Validation Rules',
                'content' => 'Comprehensive guide to Laravel validation rules and how to create custom validation logic for your application requirements.',
            ],
            [
                'title' => 'Vue.js Performance Optimization',
                'content' => 'Optimize your Vue.js applications for better performance using advanced techniques like lazy loading, code splitting, and virtual scrolling.',
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
