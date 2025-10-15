<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
            'message' => 'Comments retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $commentData = $request->validated();
        
        // Add user_id if authenticated, otherwise leave as null for guest
        if (Auth::check()) {
            $commentData['user_id'] = Auth::id();
        }
        
        $commentData['post_id'] = $post->id;
        
        $comment = Comment::create($commentData);
        $comment->load('user');

        return response()->json([
            'success' => true,
            'data' => new CommentResource($comment),
            'message' => 'Comment created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user', 'post']);
        
        return response()->json([
            'success' => true,
            'data' => new CommentResource($comment),
            'message' => 'Comment retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        Gate::authorize('update', $comment);
        
        $request->validate([
            'comment' => 'required|string|min:10|max:1000'
        ]);
        
        $comment->update($request->only('comment'));
        $comment->load('user');

        return response()->json([
            'success' => true,
            'data' => new CommentResource($comment),
            'message' => 'Comment updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);
        
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}