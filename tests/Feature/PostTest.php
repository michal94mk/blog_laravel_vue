<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Create another user for authorization tests
        $this->otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com',
        ]);
    }

    /** @test */
    public function guests_can_view_posts_index()
    {
        $response = $this->get('/posts');
        
        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
    }

    /** @test */
    public function guests_can_view_individual_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response = $this->get("/posts/{$post->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertSee('Test Post');
        $response->assertSee('This is a test post content.');
    }

    /** @test */
    public function authenticated_users_can_create_posts()
    {
        $this->actingAs($this->user);

        $postData = [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
        ];

        $response = $this->post('/posts', $postData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function guests_cannot_create_posts()
    {
        $postData = [
            'title' => 'New Test Post',
            'content' => 'This is the content of the new test post.',
        ];

        $response = $this->post('/posts', $postData);
        
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('posts', [
            'title' => 'New Test Post',
        ]);
    }

    /** @test */
    public function post_creation_requires_valid_data()
    {
        $this->actingAs($this->user);

        // Test missing title
        $response = $this->post('/posts', [
            'content' => 'This is the content.',
        ]);
        $response->assertSessionHasErrors(['title']);

        // Test missing content
        $response = $this->post('/posts', [
            'title' => 'Test Title',
        ]);
        $response->assertSessionHasErrors(['content']);

        // Test title too long
        $response = $this->post('/posts', [
            'title' => str_repeat('a', 256),
            'content' => 'This is the content.',
        ]);
        $response->assertSessionHasErrors(['title']);

        // Test content too short
        $response = $this->post('/posts', [
            'title' => 'Test Title',
            'content' => 'Short',
        ]);
        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function post_owners_can_edit_their_posts()
    {
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);

        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ];

        $response = $this->put("/posts/{$post->id}", $updateData);
        $response->assertRedirect("/posts/{$post->id}");

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);
    }

    /** @test */
    public function users_cannot_edit_other_users_posts()
    {
        $this->actingAs($this->otherUser);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);

        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(403);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ];

        $response = $this->put("/posts/{$post->id}", $updateData);
        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Original Title',
            'content' => 'Original content.',
        ]);
    }

    /** @test */
    public function post_owners_can_delete_their_posts()
    {
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post to Delete',
            'content' => 'This post will be deleted.',
        ]);

        $response = $this->delete("/posts/{$post->id}");
        $response->assertRedirect('/posts');

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function users_cannot_delete_other_users_posts()
    {
        $this->actingAs($this->otherUser);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post to Delete',
            'content' => 'This post will be deleted.',
        ]);

        $response = $this->delete("/posts/{$post->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function posts_are_displayed_in_chronological_order()
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

        $response = $this->get('/posts');
        
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Third Post', 'Second Post', 'First Post']);
    }

    /** @test */
    public function posts_show_page_displays_comments()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post with Comments',
            'content' => 'This post has comments.',
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $this->user->id,
            'comment' => 'First comment',
        ]);

        $comment2 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => null, // Guest comment
            'comment' => 'Guest comment',
        ]);

        $response = $this->get("/posts/{$post->id}");
        
        $response->assertStatus(200);
        $response->assertSee('First comment');
        $response->assertSee('Guest comment');
    }
}