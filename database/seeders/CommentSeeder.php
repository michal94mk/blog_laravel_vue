<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and posts
        $users = User::all();
        $posts = Post::all();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        $comments = [
            [
                'post_id' => 1,
                'user_id' => $users->first()->id,
                'comment' => 'Great first post! Looking forward to more content.',
            ],
            [
                'post_id' => 1,
                'user_id' => null, // Guest comment
                'comment' => 'Thanks for sharing this. Very helpful!',
            ],
            [
                'post_id' => 2,
                'user_id' => $users->first()->id,
                'comment' => 'Eloquent relationships are indeed powerful. Great explanation!',
            ],
            [
                'post_id' => 2,
                'user_id' => null, // Guest comment
                'comment' => 'Could you provide more examples of many-to-many relationships?',
            ],
            [
                'post_id' => 3,
                'user_id' => $users->first()->id,
                'comment' => 'API development with Laravel is so straightforward. Thanks for the tutorial!',
            ],
            [
                'post_id' => 4,
                'user_id' => null, // Guest comment
                'comment' => 'Vue.js integration looks promising. When will you cover the frontend part?',
            ],
            [
                'post_id' => 5,
                'user_id' => $users->first()->id,
                'comment' => 'These migration best practices are gold. Bookmarking this!',
            ],
        ];

        foreach ($comments as $commentData) {
            // Only create comment if the post exists
            if ($posts->where('id', $commentData['post_id'])->isNotEmpty()) {
                Comment::create($commentData);
            }
        }
    }
}
