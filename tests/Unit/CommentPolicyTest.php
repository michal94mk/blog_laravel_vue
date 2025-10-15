<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
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

        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ]);

        $this->comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'comment' => 'This is a test comment.',
        ]);

        $this->policy = new CommentPolicy();
    }

    /** @test */
    public function anyone_can_view_any_comment()
    {
        // Authenticated user can view
        $this->assertTrue($this->policy->view($this->user, $this->comment));
        
        // Other user can view
        $this->assertTrue($this->policy->view($this->otherUser, $this->comment));
        
        // Guest can view (null user)
        $this->assertTrue($this->policy->view(null, $this->comment));
    }

    /** @test */
    public function anyone_can_view_comments_list()
    {
        // Authenticated user can view list
        $this->assertTrue($this->policy->viewAny($this->user));
        
        // Other user can view list
        $this->assertTrue($this->policy->viewAny($this->otherUser));
        
        // Guest can view list (null user)
        $this->assertTrue($this->policy->viewAny(null));
    }

    /** @test */
    public function anyone_can_create_comments()
    {
        // Authenticated user can create
        $this->assertTrue($this->policy->create($this->user));
        
        // Other user can create
        $this->assertTrue($this->policy->create($this->otherUser));
        
        // Guest can create (null user)
        $this->assertTrue($this->policy->create(null));
    }

    /** @test */
    public function comment_owners_can_update_their_comments()
    {
        $this->assertTrue($this->policy->update($this->user, $this->comment));
    }

    /** @test */
    public function users_cannot_update_other_users_comments()
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->comment));
    }

    /** @test */
    public function guests_cannot_update_comments()
    {
        $this->assertFalse($this->policy->update(null, $this->comment));
    }

    /** @test */
    public function comment_owners_can_delete_their_comments()
    {
        $this->assertTrue($this->policy->delete($this->user, $this->comment));
    }

    /** @test */
    public function post_owners_can_delete_comments_on_their_posts()
    {
        // Create a comment by another user on the post owner's post
        $otherUserComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->otherUser->id,
            'comment' => 'Comment by other user',
        ]);

        // Post owner can delete the comment
        $this->assertTrue($this->policy->delete($this->user, $otherUserComment));
    }

    /** @test */
    public function users_cannot_delete_other_users_comments()
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->comment));
    }

    /** @test */
    public function guests_cannot_delete_comments()
    {
        $this->assertFalse($this->policy->delete(null, $this->comment));
    }

    /** @test */
    public function restore_is_not_allowed()
    {
        $this->assertFalse($this->policy->restore($this->user, $this->comment));
        $this->assertFalse($this->policy->restore($this->otherUser, $this->comment));
        $this->assertFalse($this->policy->restore(null, $this->comment));
    }

    /** @test */
    public function force_delete_is_not_allowed()
    {
        $this->assertFalse($this->policy->forceDelete($this->user, $this->comment));
        $this->assertFalse($this->policy->forceDelete($this->otherUser, $this->comment));
        $this->assertFalse($this->policy->forceDelete(null, $this->comment));
    }

    /** @test */
    public function guest_comments_can_be_deleted_by_post_owner()
    {
        // Create a guest comment
        $guestComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => null, // Guest comment
            'comment' => 'Guest comment',
        ]);

        // Post owner can delete guest comment
        $this->assertTrue($this->policy->delete($this->user, $guestComment));
    }

    /** @test */
    public function guest_comments_cannot_be_deleted_by_other_users()
    {
        // Create a guest comment
        $guestComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => null, // Guest comment
            'comment' => 'Guest comment',
        ]);

        // Other user cannot delete guest comment
        $this->assertFalse($this->policy->delete($this->otherUser, $guestComment));
    }
}