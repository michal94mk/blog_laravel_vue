# API Usage Examples

Practical examples of how to use the Laravel Blog API with different tools and programming languages.

## 🚀 Quick Start

### Base URL
```
http://127.0.0.1:8000/api/v1
```

### Authentication Flow
1. Register a new user
2. Login to get authentication token
3. Use token in Authorization header for protected endpoints

---

## 📝 cURL Examples

### 1. Register User
```bash
curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
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
    "token": "1|abc123def456ghi789..."
  },
  "message": "User registered successfully"
}
```

### 2. Login
```bash
curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 3. Get All Posts (Public)
```bash
curl -X GET http://127.0.0.1:8000/api/v1/posts
```

### 4. Create Post (Authenticated)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/posts \
  -H "Authorization: Bearer 1|abc123def456ghi789..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First API Post",
    "content": "This post was created using the API!"
  }'
```

### 5. Add Comment (Guest)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/posts/1/comments \
  -H "Content-Type: application/json" \
  -d '{
    "comment": "Great post! This is a guest comment."
  }'
```

### 6. Get User Profile
```bash
curl -X GET http://127.0.0.1:8000/api/v1/me \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

### 7. Update Post
```bash
curl -X PUT http://127.0.0.1:8000/api/v1/posts/1 \
  -H "Authorization: Bearer 1|abc123def456ghi789..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Post Title",
    "content": "This post has been updated via API."
  }'
```

### 8. Delete Post
```bash
curl -X DELETE http://127.0.0.1:8000/api/v1/posts/1 \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

### 9. Logout
```bash
curl -X POST http://127.0.0.1:8000/api/v1/logout \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

---

## 🧪 Postman Collection

### Environment Variables
Create a Postman environment with:
- `base_url`: `http://127.0.0.1:8000/api/v1`
- `token`: (will be set automatically)

### Pre-request Scripts
Add this to login/register requests to automatically save token:
```javascript
pm.test("Save token", function () {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
});
```

### Collection Structure
```
Blog API
├── Authentication
│   ├── Register User
│   ├── Login User
│   ├── Get Profile
│   ├── Refresh Token
│   └── Logout
├── Posts
│   ├── Get All Posts
│   ├── Get Single Post
│   ├── Create Post
│   ├── Update Post
│   └── Delete Post
└── Comments
    ├── Get Post Comments
    ├── Create Comment
    ├── Get Single Comment
    ├── Update Comment
    └── Delete Comment
```

---

## 💻 JavaScript Examples

### Using Fetch API
```javascript
// Base configuration
const API_BASE = 'http://127.0.0.1:8000/api/v1';
let authToken = null;

// Register user
async function registerUser(userData) {
    const response = await fetch(`${API_BASE}/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    });
    
    const data = await response.json();
    if (data.success) {
        authToken = data.data.token;
        console.log('User registered:', data.data.user);
    }
    return data;
}

// Login user
async function loginUser(credentials) {
    const response = await fetch(`${API_BASE}/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
    });
    
    const data = await response.json();
    if (data.success) {
        authToken = data.data.token;
        console.log('User logged in:', data.data.user);
    }
    return data;
}

// Get all posts
async function getPosts() {
    const response = await fetch(`${API_BASE}/posts`);
    const data = await response.json();
    return data;
}

// Create post (authenticated)
async function createPost(postData) {
    const response = await fetch(`${API_BASE}/posts`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${authToken}`
        },
        body: JSON.stringify(postData)
    });
    
    return await response.json();
}

// Add comment
async function addComment(postId, comment) {
    const response = await fetch(`${API_BASE}/posts/${postId}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ comment })
    });
    
    return await response.json();
}

// Usage example
async function example() {
    // Register and login
    await registerUser({
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        password_confirmation: 'password123'
    });
    
    // Get posts
    const posts = await getPosts();
    console.log('Posts:', posts);
    
    // Create a post
    const newPost = await createPost({
        title: 'My JavaScript Post',
        content: 'Created using JavaScript fetch API!'
    });
    console.log('Created post:', newPost);
    
    // Add a comment
    const comment = await addComment(newPost.data.id, 'Great post!');
    console.log('Added comment:', comment);
}
```

### Using Axios
```javascript
import axios from 'axios';

// Create axios instance
const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api/v1',
    headers: {
        'Content-Type': 'application/json'
    }
});

// Add token to requests
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// API functions
export const authAPI = {
    register: (userData) => api.post('/register', userData),
    login: (credentials) => api.post('/login', credentials),
    logout: () => api.post('/logout'),
    getProfile: () => api.get('/me'),
    refreshToken: () => api.post('/refresh')
};

export const postsAPI = {
    getAll: () => api.get('/posts'),
    getById: (id) => api.get(`/posts/${id}`),
    create: (postData) => api.post('/posts', postData),
    update: (id, postData) => api.put(`/posts/${id}`, postData),
    delete: (id) => api.delete(`/posts/${id}`)
};

export const commentsAPI = {
    getByPost: (postId) => api.get(`/posts/${postId}/comments`),
    create: (postId, comment) => api.post(`/posts/${postId}/comments`, { comment }),
    getById: (id) => api.get(`/comments/${id}`),
    update: (id, comment) => api.put(`/comments/${id}`, { comment }),
    delete: (id) => api.delete(`/comments/${id}`)
};
```

---

## 🐍 Python Examples

### Using Requests Library
```python
import requests
import json

# Base configuration
API_BASE = 'http://127.0.0.1:8000/api/v1'
auth_token = None

class BlogAPI:
    def __init__(self, base_url=API_BASE):
        self.base_url = base_url
        self.session = requests.Session()
        self.session.headers.update({'Content-Type': 'application/json'})
    
    def set_token(self, token):
        """Set authentication token"""
        self.auth_token = token
        self.session.headers.update({'Authorization': f'Bearer {token}'})
    
    def register(self, name, email, password):
        """Register a new user"""
        data = {
            'name': name,
            'email': email,
            'password': password,
            'password_confirmation': password
        }
        response = self.session.post(f'{self.base_url}/register', json=data)
        result = response.json()
        
        if result.get('success'):
            self.set_token(result['data']['token'])
        
        return result
    
    def login(self, email, password):
        """Login user"""
        data = {'email': email, 'password': password}
        response = self.session.post(f'{self.base_url}/login', json=data)
        result = response.json()
        
        if result.get('success'):
            self.set_token(result['data']['token'])
        
        return result
    
    def get_posts(self):
        """Get all posts"""
        response = self.session.get(f'{self.base_url}/posts')
        return response.json()
    
    def create_post(self, title, content):
        """Create a new post"""
        data = {'title': title, 'content': content}
        response = self.session.post(f'{self.base_url}/posts', json=data)
        return response.json()
    
    def add_comment(self, post_id, comment):
        """Add a comment to a post"""
        data = {'comment': comment}
        response = self.session.post(f'{self.base_url}/posts/{post_id}/comments', json=data)
        return response.json()
    
    def get_profile(self):
        """Get user profile"""
        response = self.session.get(f'{self.base_url}/me')
        return response.json()

# Usage example
if __name__ == '__main__':
    api = BlogAPI()
    
    # Register and login
    result = api.register('John Doe', 'john@example.com', 'password123')
    print('Registration:', result)
    
    # Get posts
    posts = api.get_posts()
    print('Posts:', posts)
    
    # Create a post
    new_post = api.create_post('My Python Post', 'Created using Python requests!')
    print('Created post:', new_post)
    
    # Add a comment
    comment = api.add_comment(new_post['data']['id'], 'Great post from Python!')
    print('Added comment:', comment)
    
    # Get profile
    profile = api.get_profile()
    print('Profile:', profile)
```

---

## 🔧 PHP Examples

### Using Guzzle HTTP Client
```php
<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BlogAPI
{
    private $client;
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl = 'http://127.0.0.1:8000/api/v1')
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }
    
    public function setToken($token)
    {
        $this->token = $token;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
    }
    
    public function register($name, $email, $password)
    {
        try {
            $response = $this->client->post('/register', [
                'json' => [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $password
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if ($result['success']) {
                $this->setToken($result['data']['token']);
            }
            
            return $result;
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    
    public function login($email, $password)
    {
        try {
            $response = $this->client->post('/login', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if ($result['success']) {
                $this->setToken($result['data']['token']);
            }
            
            return $result;
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    
    public function getPosts()
    {
        try {
            $response = $this->client->get('/posts');
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    
    public function createPost($title, $content)
    {
        try {
            $response = $this->client->post('/posts', [
                'json' => [
                    'title' => $title,
                    'content' => $content
                ]
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    
    public function addComment($postId, $comment)
    {
        try {
            $response = $this->client->post("/posts/{$postId}/comments", [
                'json' => [
                    'comment' => $comment
                ]
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
}

// Usage example
$api = new BlogAPI();

// Register and login
$result = $api->register('John Doe', 'john@example.com', 'password123');
echo "Registration: " . json_encode($result) . "\n";

// Get posts
$posts = $api->getPosts();
echo "Posts: " . json_encode($posts) . "\n";

// Create a post
$newPost = $api->createPost('My PHP Post', 'Created using PHP Guzzle!');
echo "Created post: " . json_encode($newPost) . "\n";

// Add a comment
$comment = $api->addComment($newPost['data']['id'], 'Great post from PHP!');
echo "Added comment: " . json_encode($comment) . "\n";
```

---

## 🧪 Testing Examples

### API Testing with PHPUnit
```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ApiExampleTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_api_workflow()
    {
        // 1. Register user
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        
        $response->assertStatus(201);
        $token = $response->json('data.token');
        
        // 2. Create post
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/posts', [
            'title' => 'Test Post',
            'content' => 'Test content'
        ]);
        
        $response->assertStatus(201);
        $postId = $response->json('data.id');
        
        // 3. Add comment
        $response = $this->postJson("/api/v1/posts/{$postId}/comments", [
            'comment' => 'Test comment'
        ]);
        
        $response->assertStatus(201);
        
        // 4. Get posts
        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
```

---

## 🚨 Error Handling

### Common Error Responses
```json
// Validation Error (422)
{
    "message": "The given data was invalid.",
    "errors": {
        "title": ["The title field is required."],
        "content": ["The content field is required."]
    }
}

// Unauthorized (401)
{
    "message": "Unauthenticated."
}

// Forbidden (403)
{
    "message": "This action is unauthorized."
}

// Not Found (404)
{
    "message": "No query results for model [App\\Models\\Post] 999"
}
```

### Error Handling in JavaScript
```javascript
async function handleApiCall(apiCall) {
    try {
        const response = await apiCall();
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'API request failed');
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error.message);
        throw error;
    }
}

// Usage
handleApiCall(() => fetch('/api/v1/posts'))
    .then(data => console.log('Success:', data))
    .catch(error => console.error('Error:', error));
```

---

## 📊 Performance Tips

1. **Use pagination** for large datasets
2. **Cache frequently accessed data**
3. **Use appropriate HTTP methods**
4. **Implement rate limiting**
5. **Optimize database queries**
6. **Use compression for large responses**

---

## 🔐 Security Best Practices

1. **Always use HTTPS in production**
2. **Validate all input data**
3. **Implement proper authentication**
4. **Use CSRF protection for web routes**
5. **Sanitize user input**
6. **Implement rate limiting**
7. **Keep tokens secure**
8. **Regular security updates**
