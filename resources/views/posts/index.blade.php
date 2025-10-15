@extends('posts.layout')

@section('title', 'All Posts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
        @auth
            <a href="{{ route('posts.create') }}" 
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition duration-150 ease-in-out">
                Create New Post
            </a>
        @endauth
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <!-- Post Meta -->
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span>By {{ $post->user->name }}</span>
                            <span class="mx-2">•</span>
                            <time datetime="{{ $post->created_at->toISOString() }}">
                                {{ $post->created_at->format('M j, Y') }}
                            </time>
                        </div>

                        <!-- Post Title -->
                        <h2 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
                            <a href="{{ route('posts.show', $post) }}" 
                               class="hover:text-indigo-600 transition-colors duration-150">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <!-- Post Content Preview -->
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            {{ Str::limit($post->content, 150) }}
                        </p>

                        <!-- Comments Count -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                {{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}
                            </div>

                            <a href="{{ route('posts.show', $post) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Read More →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No posts yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first blog post.</p>
            @auth
                <div class="mt-6">
                    <a href="{{ route('posts.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Create your first post
                    </a>
                </div>
            @endauth
        </div>
    @endif
</div>
@endsection
