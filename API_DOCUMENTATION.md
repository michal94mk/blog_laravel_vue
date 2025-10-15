# API Documentation

This document describes the REST API endpoints for the Laravel Blog application.

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Endpoints

### Authentication

#### Register User
```http
POST /api/v1/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "token": "1|abc123..."
    },
    "message": "User registered successfully"
}
```

#### Login User
```http
POST /api/v1/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "token": "1|abc123..."
    },
    "message": "Login successful"
}
```

#### Logout User
```http
POST /api/v1/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

#### Get User Profile
```http
GET /api/v1/me
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z"
    },
    "message": "User retrieved successfully"
}
```

#### Refresh Token
```http
POST /api/v1/refresh
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "token": "2|def456..."
    },
    "message": "Token refreshed successfully"
}
```

### Posts

#### Get All Posts
```http
GET /api/v1/posts
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "My First Post",
            "content": "This is the content of my first post.",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "created_at": "2025-10-15T14:38:28.000000Z",
                "updated_at": "2025-10-15T14:38:28.000000Z"
            },
            "comments": [],
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z",
            "created_at_human": "2 hours ago",
            "updated_at_human": "2 hours ago"
        }
    ],
    "message": "Posts retrieved successfully"
}
```

#### Get Single Post
```http
GET /api/v1/posts/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "My First Post",
        "content": "This is the content of my first post.",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "comments": [
            {
                "id": 1,
                "comment": "Great post!",
                "user": {
                    "id": 2,
                    "name": "Jane Doe",
                    "email": "jane@example.com",
                    "created_at": "2025-10-15T14:38:28.000000Z",
                    "updated_at": "2025-10-15T14:38:28.000000Z"
                },
                "post_id": 1,
                "created_at": "2025-10-15T14:38:28.000000Z",
                "updated_at": "2025-10-15T14:38:28.000000Z",
                "created_at_human": "1 hour ago",
                "updated_at_human": "1 hour ago"
            }
        ],
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "2 hours ago",
        "updated_at_human": "2 hours ago"
    },
    "message": "Post retrieved successfully"
}
```

#### Create Post
```http
POST /api/v1/posts
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "title": "My New Post",
    "content": "This is the content of my new post."
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "title": "My New Post",
        "content": "This is the content of my new post.",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "0 seconds ago",
        "updated_at_human": "0 seconds ago"
    },
    "message": "Post created successfully"
}
```

#### Update Post
```http
PUT /api/v1/posts/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "title": "Updated Post Title",
    "content": "This is the updated content."
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Updated Post Title",
        "content": "This is the updated content.",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "2 hours ago",
        "updated_at_human": "0 seconds ago"
    },
    "message": "Post updated successfully"
}
```

#### Delete Post
```http
DELETE /api/v1/posts/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Post deleted successfully"
}
```

### Comments

#### Get Post Comments
```http
GET /api/v1/posts/{id}/comments
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "comment": "Great post!",
            "user": {
                "id": 2,
                "name": "Jane Doe",
                "email": "jane@example.com",
                "created_at": "2025-10-15T14:38:28.000000Z",
                "updated_at": "2025-10-15T14:38:28.000000Z"
            },
            "post_id": 1,
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z",
            "created_at_human": "1 hour ago",
            "updated_at_human": "1 hour ago"
        }
    ],
    "message": "Comments retrieved successfully"
}
```

#### Create Comment
```http
POST /api/v1/posts/{id}/comments
```

**Request Body:**
```json
{
    "comment": "This is a great post!"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "comment": "This is a great post!",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "post_id": 1,
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "0 seconds ago",
        "updated_at_human": "0 seconds ago"
    },
    "message": "Comment created successfully"
}
```

#### Get Single Comment
```http
GET /api/v1/comments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "comment": "Great post!",
        "user": {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "post_id": 1,
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "1 hour ago",
        "updated_at_human": "1 hour ago"
    },
    "message": "Comment retrieved successfully"
}
```

#### Update Comment
```http
PUT /api/v1/comments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "comment": "This is an updated comment."
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "comment": "This is an updated comment.",
        "user": {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane@example.com",
            "created_at": "2025-10-15T14:38:28.000000Z",
            "updated_at": "2025-10-15T14:38:28.000000Z"
        },
        "post_id": 1,
        "created_at": "2025-10-15T14:38:28.000000Z",
        "updated_at": "2025-10-15T14:38:28.000000Z",
        "created_at_human": "1 hour ago",
        "updated_at_human": "0 seconds ago"
    },
    "message": "Comment updated successfully"
}
```

#### Delete Comment
```http
DELETE /api/v1/comments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Comment deleted successfully"
}
```

## Error Responses

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "title": [
            "The title field is required."
        ],
        "content": [
            "The content field is required."
        ]
    }
}
```

### Unauthorized (401)
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
    "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
    "message": "No query results for model [App\\Models\\Post] 999"
}
```

## Authentication Notes

- **Public endpoints**: Registration, login, viewing posts and comments
- **Protected endpoints**: Creating/updating/deleting posts and comments, user profile
- **Guest comments**: Guests can create comments without authentication
- **Authorization**: Users can only edit/delete their own posts and comments, or comments on their posts

## Rate Limiting

The API implements rate limiting to prevent abuse. Default limits:
- 60 requests per minute for authenticated users
- 10 requests per minute for guests

## CORS

The API supports Cross-Origin Resource Sharing (CORS) for frontend applications.
