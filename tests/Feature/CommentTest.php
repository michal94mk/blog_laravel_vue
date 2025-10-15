<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
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

        // Create a test post
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ]);
    }

    /** @test */
    public function authenticated_users_can_add_comments()
    {
        $this->actingAs($this->user);

        $commentData = [
            'comment' => 'This is a test comment from an authenticated user.',
        ];

        $response = $this->post("/posts/{$this->post->id}/comments", $commentData);
        
        $response->assertRedirect("/posts/{$this->post->id}");
        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'This is a test comment from an authenticated user.',
        ]);
    }

    /** @test */
    public function guests_can_add_comments()
    {
        $commentData = [
            'comment' => 'This is a test comment from a guest user.',
        ];

        $response = $this->post("/posts/{$this->post->id}/comments", $commentData);
        
        $response->assertRedirect("/posts/{$this->post->id}");
        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'user_id' => null, // Guest comment
            'comment' => 'This is a test comment from a guest user.',
        ]);
    }

    /** @test */
    public function comment_creation_requires_valid_data()
    {
        $this->actingAs($this->user);

        // Test missing comment
        $response = $this->post("/posts/{$this->post->id}/comments", []);
        $response->assertSessionHasErrors(['comment']);

        // Test comment too short
        $response = $this->post("/posts/{$this->post->id}/comments", [
            'comment' => 'Hi',
        ]);
        $response->assertSessionHasErrors(['comment']);

        // Test comment too long
        $response = $this->post("/posts/{$this->post->id}/comments", [
            'comment' => str_repeat('a', 1001),
        ]);
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function comment_owners_can_delete_their_comments()
    {
        $this->actingAs($this->user);

        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'Comment to delete',
        ]);

        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$this->post->id}");

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function post_owners_can_delete_any_comment_on_their_posts()
    {
        $this->actingAs($this->user);

        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->otherUser->id, // Comment by other user
            'comment' => 'Comment by other user',
        ]);

        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$this->post->id}");

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function users_cannot_delete_other_users_comments()
    {
        $this->actingAs($this->otherUser);

        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id, // Comment by post owner
            'comment' => 'Comment by post owner',
        ]);

        $response = $this->delete("/comments/{$comment->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function guests_cannot_delete_comments()
    {
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'Comment to delete',
        ]);

        $response = $this->delete("/comments/{$comment->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function comments_are_displayed_in_chronological_order()
    {
        $comment1 = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'First comment',
            'created_at' => now()->subDays(2),
        ]);

        $comment2 = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->otherUser->id,
            'comment' => 'Second comment',
            'created_at' => now()->subDay(),
        ]);

        $comment3 = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => null,
            'comment' => 'Third comment',
            'created_at' => now(),
        ]);

        $response = $this->get("/posts/{$this->post->id}");
        
        $response->assertStatus(200);
        $response->assertSeeInOrder(['First comment', 'Second comment', 'Third comment']);
    }

    /** @test */
    public function comments_show_author_information()
    {
        $this->actingAs($this->user);

        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'Comment with author info',
        ]);

        $response = $this->get("/posts/{$this->post->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Test User'); // Author name
        $response->assertSee('Comment with author info');
    }

    /** @test */
    public function guest_comments_show_guest_label()
    {
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => null,
            'comment' => 'Guest comment',
        ]);

        $response = $this->get("/posts/{$this->post->id}");

        $response->assertStatus(200);
        $response->assertSee('Guest'); // Guest label
        $response->assertSee('Guest comment');
    }
}