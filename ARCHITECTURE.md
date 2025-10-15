# Project Architecture

This document describes the architecture, design patterns, and technical decisions of the Laravel Blog application.

## 🏗️ Overall Architecture

### High-Level Architecture
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Browser   │    │   Mobile App    │    │   API Client    │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          │ HTTP/HTTPS           │ HTTP/HTTPS           │ HTTP/HTTPS
          │                      │                      │
          ▼                      ▼                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel Application                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Web Routes  │  │ API Routes  │  │ Auth Routes │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Controllers │  │ API Contr.  │  │ Middleware  │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Policies    │  │ Resources   │  │ Requests    │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
└─────────────────────────────────────────────────────────────────┘
          │                      │                      │
          ▼                      ▼                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Database Layer                              │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Eloquent    │  │ Migrations  │  │ Seeders     │            │
│  │ Models      │  │             │  │             │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
└─────────────────────────────────────────────────────────────────┘
```

## 🎯 Design Patterns

### 1. MVC (Model-View-Controller)
- **Models:** `User`, `Post`, `Comment` - Handle data logic and relationships
- **Views:** Blade templates for web interface
- **Controllers:** Handle HTTP requests and business logic

### 2. Repository Pattern (Implicit)
- Eloquent models act as repositories
- Centralized data access logic
- Easy to mock for testing

### 3. Policy Pattern
- Authorization logic separated from controllers
- `PostPolicy` and `CommentPolicy` handle permissions
- Clean, testable authorization rules

### 4. Resource Pattern
- API responses standardized with Resources
- Consistent JSON structure
- Easy to modify response format

### 5. Form Request Pattern
- Validation logic separated from controllers
- Reusable validation rules
- Clean controller methods

## 🗂️ Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/                 # API Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   └── CommentController.php
│   │   ├── Auth/                # Authentication Controllers
│   │   └── PostController.php   # Web Controllers
│   ├── Requests/                # Form Validation
│   │   ├── StorePostRequest.php
│   │   ├── UpdatePostRequest.php
│   │   └── StoreCommentRequest.php
│   ├── Resources/               # API Resources
│   │   ├── PostResource.php
│   │   ├── CommentResource.php
│   │   └── UserResource.php
│   └── Middleware/              # Custom Middleware
├── Models/                      # Eloquent Models
│   ├── User.php
│   ├── Post.php
│   └── Comment.php
├── Policies/                    # Authorization Policies
│   ├── PostPolicy.php
│   └── CommentPolicy.php
└── Providers/                   # Service Providers
    └── AuthServiceProvider.php

database/
├── migrations/                  # Database Schema
│   ├── create_users_table.php
│   ├── create_posts_table.php
│   └── create_comments_table.php
├── seeders/                     # Sample Data
│   ├── DatabaseSeeder.php
│   ├── PostSeeder.php
│   └── CommentSeeder.php
└── factories/                   # Test Data
    ├── UserFactory.php
    ├── PostFactory.php
    └── CommentFactory.php

routes/
├── web.php                      # Web Routes
├── api.php                      # API Routes
└── auth.php                     # Authentication Routes

resources/
├── views/                       # Blade Templates
│   ├── layouts/
│   ├── posts/
│   └── auth/
└── css/                         # Stylesheets
    └── app.css

tests/
├── Feature/                     # Feature Tests
│   ├── PostTest.php
│   ├── CommentTest.php
│   └── Api/
│       ├── PostApiTest.php
│       └── AuthApiTest.php
└── Unit/                        # Unit Tests
    ├── PostPolicyTest.php
    └── CommentPolicyTest.php
```

## 🔄 Data Flow

### Web Request Flow
```
1. User Request → 2. Route → 3. Middleware → 4. Controller → 5. Policy → 6. Model → 7. Database
                                                                                    ↓
8. Response ← 7. View ← 6. Controller ← 5. Model ← 4. Database ← 3. Query
```

### API Request Flow
```
1. API Request → 2. Route → 3. Middleware → 4. Controller → 5. Policy → 6. Model → 7. Database
                                                                                    ↓
8. JSON Response ← 7. Resource ← 6. Controller ← 5. Model ← 4. Database ← 3. Query
```

## 🗄️ Database Design

### Entity Relationship Diagram
```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│    Users    │         │    Posts    │         │  Comments   │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id (PK)     │◄────────┤ id (PK)     │◄────────┤ id (PK)     │
│ name        │   1:N   │ user_id (FK)│   1:N   │ post_id (FK)│
│ email       │         │ title       │         │ user_id (FK)│
│ password    │         │ content     │         │ comment     │
│ created_at  │         │ created_at  │         │ created_at  │
│ updated_at  │         │ updated_at  │         │ updated_at  │
└─────────────┘         └─────────────┘         └─────────────┘
```

### Relationships
- **User → Posts:** One-to-Many (User has many Posts)
- **User → Comments:** One-to-Many (User has many Comments)
- **Post → Comments:** One-to-Many (Post has many Comments)
- **Post → User:** Many-to-One (Post belongs to User)
- **Comment → User:** Many-to-One (Comment belongs to User)
- **Comment → Post:** Many-to-One (Comment belongs to Post)

## 🔐 Security Architecture

### Authentication Layers
```
┌─────────────────────────────────────────────────────────────┐
│                    Security Layers                         │
├─────────────────────────────────────────────────────────────┤
│ 1. HTTPS/TLS (Transport Security)                          │
├─────────────────────────────────────────────────────────────┤
│ 2. CSRF Protection (Web Routes)                            │
├─────────────────────────────────────────────────────────────┤
│ 3. Authentication (Session/Token)                          │
├─────────────────────────────────────────────────────────────┤
│ 4. Authorization (Policies)                                │
├─────────────────────────────────────────────────────────────┤
│ 5. Input Validation (Form Requests)                        │
├─────────────────────────────────────────────────────────────┤
│ 6. SQL Injection Protection (Eloquent ORM)                 │
└─────────────────────────────────────────────────────────────┘
```

### Authentication Methods
- **Web:** Session-based authentication (Laravel Breeze)
- **API:** Token-based authentication (Laravel Sanctum)

### Authorization Rules
- **Posts:** Users can only edit/delete their own posts
- **Comments:** Users can edit/delete their own comments
- **Post Owners:** Can delete any comment on their posts
- **Guests:** Can view posts and add comments

## 🧪 Testing Architecture

### Test Pyramid
```
                    ┌─────────────────┐
                    │   E2E Tests     │ ← Few, High-level
                    └─────────────────┘
                ┌─────────────────────────┐
                │    Integration Tests    │ ← Some, Medium-level
                └─────────────────────────┘
        ┌─────────────────────────────────────────┐
        │           Unit Tests                    │ ← Many, Low-level
        └─────────────────────────────────────────┘
```

### Test Types
- **Unit Tests:** Models, Policies, Form Requests
- **Feature Tests:** Web routes, API endpoints, User workflows
- **Integration Tests:** Database interactions, Authentication flows

## 🚀 Performance Considerations

### Caching Strategy
- **Route Caching:** `php artisan route:cache`
- **Config Caching:** `php artisan config:cache`
- **View Caching:** `php artisan view:cache`
- **Query Caching:** Eloquent query caching

### Database Optimization
- **Indexes:** Primary keys, foreign keys, frequently queried columns
- **Eager Loading:** `with()` to prevent N+1 queries
- **Pagination:** Limit results for large datasets

### API Optimization
- **Resource Transformation:** Consistent JSON structure
- **Conditional Loading:** Load relationships only when needed
- **Response Compression:** Gzip compression for large responses

## 🔧 Configuration Management

### Environment-based Configuration
```env
# Application
APP_ENV=local|production
APP_DEBUG=true|false

# Database
DB_CONNECTION=mysql|postgresql|sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_laravel_vue

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
```

### Service Providers
- **AuthServiceProvider:** Policy registration
- **AppServiceProvider:** Global configuration
- **RouteServiceProvider:** Route model binding

## 📊 Monitoring & Logging

### Logging Strategy
- **Application Logs:** `storage/logs/laravel.log`
- **Error Tracking:** Exception handling and reporting
- **API Logging:** Request/response logging for debugging

### Performance Monitoring
- **Query Logging:** Database query performance
- **Response Times:** API endpoint performance
- **Memory Usage:** Application memory consumption

## 🔄 Deployment Architecture

### Development Environment
```
Developer Machine → Git Repository → Local Laravel Server
```

### Production Environment
```
Load Balancer → Web Servers → Application Servers → Database Cluster
```

### CI/CD Pipeline
```
Code Push → Tests → Build → Deploy → Health Check
```

## 🎯 Scalability Considerations

### Horizontal Scaling
- **Stateless Design:** No server-side sessions for API
- **Database Scaling:** Read replicas, connection pooling
- **Load Balancing:** Multiple application servers

### Vertical Scaling
- **Resource Optimization:** Memory, CPU, disk I/O
- **Caching:** Redis/Memcached for session and data caching
- **CDN:** Static asset delivery

## 🔮 Future Architecture Enhancements

### Microservices Architecture
- **User Service:** Authentication and user management
- **Content Service:** Posts and comments management
- **Notification Service:** Email and push notifications
- **Search Service:** Full-text search functionality

### Event-Driven Architecture
- **Event Sourcing:** Track all changes as events
- **Message Queues:** Asynchronous processing
- **Webhooks:** Real-time notifications

### API Gateway
- **Rate Limiting:** API usage control
- **Authentication:** Centralized auth management
- **Routing:** Request routing and load balancing
- **Monitoring:** API usage analytics

## 📚 Technology Decisions

### Why Laravel?
- **Rapid Development:** Built-in features and conventions
- **Ecosystem:** Rich package ecosystem
- **Community:** Large, active community
- **Documentation:** Excellent documentation

### Why Sanctum?
- **Simplicity:** Easy token-based authentication
- **Flexibility:** Works with SPA and mobile apps
- **Security:** Built-in security features
- **Laravel Integration:** Native Laravel integration

### Why Blade?
- **Performance:** Compiled templates
- **Familiarity:** Similar to other template engines
- **Laravel Integration:** Native Laravel integration
- **Flexibility:** Can be extended with components

### Why Tailwind CSS?
- **Utility-First:** Rapid UI development
- **Consistency:** Design system consistency
- **Performance:** Small bundle size
- **Customization:** Highly customizable

## 🎯 Code Quality Standards

### Coding Standards
- **PSR-12:** PHP coding standard
- **Laravel Conventions:** Laravel-specific conventions
- **SOLID Principles:** Object-oriented design principles
- **DRY Principle:** Don't Repeat Yourself

### Code Organization
- **Single Responsibility:** Each class has one responsibility
- **Dependency Injection:** Loose coupling between components
- **Interface Segregation:** Small, focused interfaces
- **Open/Closed Principle:** Open for extension, closed for modification

### Documentation
- **PHPDoc:** Function and class documentation
- **README:** Project setup and usage
- **API Documentation:** Complete API reference
- **Architecture Docs:** Technical architecture documentation
