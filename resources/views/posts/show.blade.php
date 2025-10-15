@extends('posts.layout')

@section('title', $post->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('posts.index') }}" 
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to all posts
        </a>
    </div>

    <!-- Post Content -->
    <article class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-8">
            <!-- Post Meta -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center text-sm text-gray-500">
                    <span>By <strong class="text-gray-900">{{ $post->user->name }}</strong></span>
                    <span class="mx-2">•</span>
                    <time datetime="{{ $post->created_at->toISOString() }}">
                        {{ $post->created_at->format('F j, Y \a\t g:i A') }}
                    </time>
                </div>

                <!-- Edit/Delete Actions -->
                @can('update', $post)
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('posts.edit', $post) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" 
                              class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

            <!-- Post Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            <!-- Post Content -->
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>
    </article>

    <!-- Comments Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">
                Comments ({{ $post->comments->count() }})
            </h2>

            <!-- Add Comment Form -->
            <div class="mb-8">
                <form method="POST" action="{{ route('comments.store', $post) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                            Add a comment
                        </label>
                        <textarea name="comment" id="comment" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('comment') border-red-500 @enderror"
                                  placeholder="Write your comment here...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition duration-150 ease-in-out">
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            @if($post->comments->count() > 0)
                <div class="space-y-6">
                    @foreach($post->comments as $comment)
                        <div class="border-l-4 border-gray-200 pl-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        @if($comment->user)
                                            <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                        @else
                                            <span class="font-medium text-gray-900">Guest</span>
                                        @endif
                                        <span class="mx-2">•</span>
                                        <time datetime="{{ $comment->created_at->toISOString() }}">
                                            {{ $comment->created_at->format('M j, Y \a\t g:i A') }}
                                        </time>
                                    </div>
                                    <p class="text-gray-700">{{ $comment->comment }}</p>
                                </div>

                                <!-- Delete Comment Button -->
                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" 
                                          class="ml-4"
                                          onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-sm text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No comments yet. Be the first to comment!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
