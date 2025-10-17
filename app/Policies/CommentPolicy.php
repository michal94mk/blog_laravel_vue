<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view comments
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Comment $comment): bool
    {
        return true; // Anyone can view individual comments
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        return true; // Both authenticated users and guests can create comments
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, Comment $comment): bool
    {
        // Admin can update any comment
        if ($user && $user->isAdmin()) {
            return true;
        }
        
        // Comment owner can update their own comment
        return $user !== null && $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, Comment $comment): bool
    {
        // Admin can delete any comment
        if ($user && $user->isAdmin()) {
            return true;
        }
        
        // Comment owner can delete their own comment
        if ($user && $user->id === $comment->user_id) {
            return true;
        }
        
        // Post owner can delete comments on their post
        if ($user && $comment->post && $user->id === $comment->post->user_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(?User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(?User $user, Comment $comment): bool
    {
        return false;
    }
}
