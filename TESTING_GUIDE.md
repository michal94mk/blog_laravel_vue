# Testing Guide

Comprehensive guide for testing the Laravel Blog application.

## 🧪 Testing Overview

The application includes comprehensive test coverage with:
- **20+ Test Cases**
- **138+ Assertions**
- **Feature Tests** for web routes and API endpoints
- **Unit Tests** for policies and models
- **Database Tests** with factories and seeders

## 🚀 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Feature tests only
php artisan test tests/Feature/

# Unit tests only
php artisan test tests/Unit/

# API tests only
php artisan test tests/Feature/Api/

# Specific test file
php artisan test tests/Feature/PostTest.php

# Specific test method
php artisan test --filter="test_guests_can_view_posts_index"
```

### Run Tests with Coverage
```bash
php artisan test --coverage
```

### Run Tests in Parallel
```bash
php artisan test --parallel
```

## 📋 Test Structure

### Test Categories

#### 1. Feature Tests
- **Web Routes:** Testing HTTP requests and responses
- **API Endpoints:** Testing REST API functionality
- **User Workflows:** Testing complete user journeys
- **Database Interactions:** Testing with real database

#### 2. Unit Tests
- **Policies:** Testing authorization logic
- **Models:** Testing model relationships and methods
- **Form Requests:** Testing validation rules
- **Services:** Testing business logic

#### 3. Integration Tests
- **Authentication Flow:** Testing login/logout
- **CRUD Operations:** Testing create, read, update, delete
- **API Authentication:** Testing token-based auth
- **Database Transactions:** Testing data integrity

## 🎯 Test Cases

### Web Application Tests

#### Post Tests (`tests/Feature/PostTest.php`)
```php
// Test public access
test('guests can view posts index')
test('guests can view individual post')
test('posts are displayed in chronological order')

// Test authentication
test('authenticated users can create posts')
test('guests cannot create posts')
test('post creation requires valid data')

// Test authorization
test('post owners can edit their posts')
test('users cannot edit other users posts')
test('post owners can delete their posts')
test('users cannot delete other users posts')

// Test display
test('posts show page displays comments')
```

#### Comment Tests (`tests/Feature/CommentTest.php`)
```php
// Test comment creation
test('authenticated users can add comments')
test('guests can add comments')
test('comment creation requires valid data')

// Test authorization
test('comment owners can delete their comments')
test('post owners can delete any comment on their posts')
test('users cannot delete other users comments')
test('guests cannot delete comments')

// Test display
test('comments are displayed in chronological order')
test('comments show author information')
test('guest comments show guest label')
```

### API Tests

#### Authentication API Tests (`tests/Feature/Api/AuthApiTest.php`)
```php
// Test registration
test('users can register via api')
test('registration requires valid data')

// Test login
test('users can login via api')
test('login requires valid credentials')

// Test authentication
test('authenticated users can logout via api')
test('guests cannot logout via api')
test('authenticated users can get their profile via api')
test('guests cannot get profile via api')

// Test token management
test('authenticated users can refresh token via api')
test('guests cannot refresh token via api')
```

#### Posts API Tests (`tests/Feature/Api/PostApiTest.php`)
```php
// Test public access
test('guests can view posts via api')
test('guests can view individual post via api')

// Test authenticated access
test('authenticated users can create posts via api')
test('guests cannot create posts via api')
test('post creation requires valid data via api')

// Test authorization
test('post owners can update their posts via api')
test('users cannot update other users posts via api')
test('post owners can delete their posts via api')
test('users cannot delete other users posts via api')

// Test data integrity
test('posts are displayed in chronological order via api')
```

### Unit Tests

#### Policy Tests (`tests/Unit/PostPolicyTest.php`, `tests/Unit/CommentPolicyTest.php`)
```php
// Test view permissions
test('anyone can view any post')
test('anyone can view posts list')

// Test create permissions
test('authenticated users can create posts')
test('guests cannot create posts')

// Test update permissions
test('post owners can update their posts')
test('users cannot update other users posts')
test('guests cannot update posts')

// Test delete permissions
test('post owners can delete their posts')
test('users cannot delete other users posts')
test('guests cannot delete posts')

// Test additional permissions
test('restore is not allowed')
test('force delete is not allowed')
```

## 🛠️ Test Setup

### Database Configuration
Tests use a separate test database to avoid affecting development data:

```php
// In TestCase.php
use RefreshDatabase;

// In phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Test Data Generation
Uses Laravel factories for consistent test data:

```php
// User factory
User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com'
]);

// Post factory
Post::factory()->create([
    'title' => 'Test Post',
    'content' => 'Test content'
]);

// Comment factory
Comment::factory()->create([
    'comment' => 'Test comment'
]);
```

### Authentication in Tests
```php
// Authenticate user
$user = User::factory()->create();
$this->actingAs($user);

// API authentication
Sanctum::actingAs($user);

// Guest user
$this->assertGuest();
```

## 📊 Test Data

### Factories
Located in `database/factories/`:

#### UserFactory
```php
public function definition(): array
{
    return [
        'name' => $this->faker->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'password' => Hash::make('password'),
    ];
}
```

#### PostFactory
```php
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'title' => $this->faker->sentence(6),
        'content' => $this->faker->paragraphs(3, true),
    ];
}
```

#### CommentFactory
```php
public function definition(): array
{
    return [
        'post_id' => Post::factory(),
        'user_id' => User::factory(),
        'comment' => $this->faker->paragraph(2),
    ];
}
```

### Seeders
Used for integration tests and development:

```php
// DatabaseSeeder
public function run(): void
{
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    PostSeeder::run($user);
    CommentSeeder::run();
}
```

## 🔍 Test Assertions

### HTTP Assertions
```php
// Status codes
$response->assertStatus(200);
$response->assertStatus(201);
$response->assertStatus(422);
$response->assertStatus(403);

// Redirects
$response->assertRedirect('/login');
$response->assertRedirect(route('posts.index'));

// JSON responses
$response->assertJson(['success' => true]);
$response->assertJsonStructure(['data', 'message']);

// Validation errors
$response->assertJsonValidationErrors(['title', 'content']);
```

### Database Assertions
```php
// Record exists
$this->assertDatabaseHas('posts', [
    'title' => 'Test Post',
    'user_id' => $user->id
]);

// Record missing
$this->assertDatabaseMissing('posts', [
    'id' => $post->id
]);

// Record count
$this->assertDatabaseCount('posts', 5);
```

### Authentication Assertions
```php
// User authenticated
$this->assertAuthenticated();
$this->assertAuthenticatedAs($user);

// User not authenticated
$this->assertGuest();

// User authorized
$this->assertTrue($user->can('update', $post));

// User not authorized
$this->assertFalse($user->can('update', $otherPost));
```

## 🚨 Error Testing

### Testing Validation Errors
```php
test('post creation requires valid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Missing title
    $response = $this->post('/posts', [
        'content' => 'Test content'
    ]);
    $response->assertSessionHasErrors(['title']);

    // Missing content
    $response = $this->post('/posts', [
        'title' => 'Test title'
    ]);
    $response->assertSessionHasErrors(['content']);

    // Title too long
    $response = $this->post('/posts', [
        'title' => str_repeat('a', 256),
        'content' => 'Test content'
    ]);
    $response->assertSessionHasErrors(['title']);
});
```

### Testing Authorization Errors
```php
test('users cannot edit other users posts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $response = $this->get("/posts/{$post->id}/edit");
    $response->assertStatus(403);
});
```

### Testing API Errors
```php
test('guests cannot create posts via api', function () {
    $response = $this->postJson('/api/v1/posts', [
        'title' => 'Test Post',
        'content' => 'Test content'
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('posts', [
        'title' => 'Test Post'
    ]);
});
```

## 🔧 Test Configuration

### PHPUnit Configuration
```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

### Test Environment
```env
# .env.testing
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

## 📈 Test Coverage

### Coverage Goals
- **Models:** 100% method coverage
- **Controllers:** 100% method coverage
- **Policies:** 100% method coverage
- **Routes:** 100% route coverage
- **Overall:** 95%+ line coverage

### Coverage Report
```bash
# Generate coverage report
php artisan test --coverage

# HTML coverage report
php artisan test --coverage-html coverage/
```

### Coverage Analysis
```bash
# View coverage in browser
open coverage/index.html
```

## 🚀 Continuous Integration

### GitHub Actions
```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        
    - name: Install dependencies
      run: composer install
      
    - name: Run tests
      run: php artisan test
```

### Pre-commit Hooks
```bash
# Install pre-commit hooks
composer require --dev brianium/paratest

# Run tests before commit
php artisan test
```

## 🐛 Debugging Tests

### Test Debugging
```php
// Dump response content
$response->dump();

// Dump session data
$response->dumpSession();

// Dump database state
$this->dumpDatabase();

// Debug specific assertions
$this->assertTrue(false, 'Debug message');
```

### Test Isolation
```php
// Use RefreshDatabase trait
use RefreshDatabase;

// Use DatabaseTransactions for specific tests
use DatabaseTransactions;

// Use WithoutMiddleware for testing without auth
use WithoutMiddleware;
```

## 📚 Best Practices

### Test Organization
1. **Arrange-Act-Assert:** Structure tests clearly
2. **Descriptive Names:** Use clear, descriptive test names
3. **Single Responsibility:** Each test should test one thing
4. **Independent Tests:** Tests should not depend on each other

### Test Data
1. **Use Factories:** Generate consistent test data
2. **Minimal Data:** Use only necessary data for tests
3. **Realistic Data:** Use realistic test data
4. **Clean State:** Ensure clean state between tests

### Performance
1. **Database Transactions:** Use transactions for speed
2. **Parallel Testing:** Run tests in parallel when possible
3. **Mock External Services:** Mock external API calls
4. **Optimize Queries:** Use eager loading in tests

## 🎯 Test Maintenance

### Regular Tasks
1. **Update Tests:** Keep tests in sync with code changes
2. **Review Coverage:** Monitor test coverage regularly
3. **Refactor Tests:** Improve test quality over time
4. **Document Changes:** Document test changes

### Test Quality
1. **Readable Tests:** Write tests that are easy to understand
2. **Maintainable Tests:** Keep tests maintainable
3. **Fast Tests:** Keep tests fast and efficient
4. **Reliable Tests:** Ensure tests are reliable and consistent
