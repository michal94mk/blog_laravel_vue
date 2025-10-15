<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
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

        $this->policy = new PostPolicy();
    }

    /** @test */
    public function anyone_can_view_any_post()
    {
        // Authenticated user can view
        $this->assertTrue($this->policy->view($this->user, $this->post));
        
        // Other user can view
        $this->assertTrue($this->policy->view($this->otherUser, $this->post));
        
        // Guest can view (null user)
        $this->assertTrue($this->policy->view(null, $this->post));
    }

    /** @test */
    public function anyone_can_view_posts_list()
    {
        // Authenticated user can view list
        $this->assertTrue($this->policy->viewAny($this->user));
        
        // Other user can view list
        $this->assertTrue($this->policy->viewAny($this->otherUser));
        
        // Guest can view list (null user)
        $this->assertTrue($this->policy->viewAny(null));
    }

    /** @test */
    public function authenticated_users_can_create_posts()
    {
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->create($this->otherUser));
    }

    /** @test */
    public function guests_cannot_create_posts()
    {
        $this->assertFalse($this->policy->create(null));
    }

    /** @test */
    public function post_owners_can_update_their_posts()
    {
        $this->assertTrue($this->policy->update($this->user, $this->post));
    }

    /** @test */
    public function users_cannot_update_other_users_posts()
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->post));
    }

    /** @test */
    public function guests_cannot_update_posts()
    {
        $this->assertFalse($this->policy->update(null, $this->post));
    }

    /** @test */
    public function post_owners_can_delete_their_posts()
    {
        $this->assertTrue($this->policy->delete($this->user, $this->post));
    }

    /** @test */
    public function users_cannot_delete_other_users_posts()
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->post));
    }

    /** @test */
    public function guests_cannot_delete_posts()
    {
        $this->assertFalse($this->policy->delete(null, $this->post));
    }

    /** @test */
    public function restore_is_not_allowed()
    {
        $this->assertFalse($this->policy->restore($this->user, $this->post));
        $this->assertFalse($this->policy->restore($this->otherUser, $this->post));
        $this->assertFalse($this->policy->restore(null, $this->post));
    }

    /** @test */
    public function force_delete_is_not_allowed()
    {
        $this->assertFalse($this->policy->forceDelete($this->user, $this->post));
        $this->assertFalse($this->policy->forceDelete($this->otherUser, $this->post));
        $this->assertFalse($this->policy->forceDelete(null, $this->post));
    }
}