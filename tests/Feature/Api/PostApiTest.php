<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com',
        ]);
    }

    /** @test */
    public function guests_can_view_posts_via_api()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response = $this->getJson('/api/v1/posts');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'user',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Posts retrieved successfully'
            ]);
    }

    /** @test */
    public function guests_can_view_individual_post_via_api()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response = $this->getJson("/api/v1/posts/{$post->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'content',
                    'user',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $post->id,
                    'title' => 'Test Post',
                    'content' => 'This is a test post content.',
                ],
                'message' => 'Post retrieved successfully'
            ]);
    }

    /** @test */
    public function authenticated_users_can_create_posts_via_api()
    {
        Sanctum::actingAs($this->user);

        $postData = [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
        ];

        $response = $this->postJson('/api/v1/posts', $postData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'content',
                    'user',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'New Test Post',
                    'content' => 'This is the content of the new test post.',
                ],
                'message' => 'Post created successfully'
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function guests_cannot_create_posts_via_api()
    {
        $postData = [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
        ];

        $response = $this->postJson('/api/v1/posts', $postData);
        
        $response->assertStatus(401);
        $this->assertDatabaseMissing('posts', [
            'title' => 'New Test Post',
        ]);
    }

    /** @test */
    public function post_creation_requires_valid_data_via_api()
    {
        Sanctum::actingAs($this->user);

        // Test missing title
        $response = $this->postJson('/api/v1/posts', [
            'content' => 'This is the content.',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Test missing content
        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Title',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);

        // Test title too long
        $response = $this->postJson('/api/v1/posts', [
            'title' => str_repeat('a', 256),
            'content' => 'This is the content.',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Test content too short
        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Title',
            'content' => 'Short',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function post_owners_can_update_their_posts_via_api()
    {
        Sanctum::actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ];

        $response = $this->putJson("/api/v1/posts/{$post->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $post->id,
                    'title' => 'Updated Title',
                    'content' => 'Updated content.',
                ],
                'message' => 'Post updated successfully'
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);
    }

    /** @test */
    public function users_cannot_update_other_users_posts_via_api()
    {
        Sanctum::actingAs($this->otherUser);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ];

        $response = $this->putJson("/api/v1/posts/{$post->id}", $updateData);
        
        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);
    }

    /** @test */
    public function post_owners_can_delete_their_posts_via_api()
    {
        Sanctum::actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post to Delete',
            'content' => 'This post will be deleted.',
        ]);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");
        
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function users_cannot_delete_other_users_posts_via_api()
    {
        Sanctum::actingAs($this->otherUser);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post to Delete',
            'content' => 'This post will be deleted.',
        ]);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");
        
        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function posts_are_displayed_in_chronological_order_via_api()
    {
        $post1 = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'First Post',
            'created_at' => now()->subDays(2),
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Second Post',
            'created_at' => now()->subDay(),
        ]);

        $post3 = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Third Post',
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200);
        
        $posts = $response->json('data');
        $this->assertEquals('Third Post', $posts[0]['title']);
        $this->assertEquals('Second Post', $posts[1]['title']);
        $this->assertEquals('First Post', $posts[2]['title']);
    }
}