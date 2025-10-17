# Laravel Blog Application

A full-featured blog application built with Laravel 12, featuring user authentication, CRUD operations for posts and comments, REST API, and comprehensive testing.

## 🚀 Features

### Core Features
- **User Authentication** - Registration, login, logout with Laravel Breeze
- **Blog Posts** - Create, read, update, delete posts with authorization
- **Comments System** - Add comments to posts (authenticated users and guests)
- **Authorization** - Users can only edit/delete their own content
- **Responsive Design** - Modern UI with Tailwind CSS

### API Features
- **REST API** - Complete API with Laravel Sanctum authentication
- **Token-based Auth** - Secure API authentication with token refresh
- **Guest Support** - Guests can view posts and add comments
- **API Documentation** - Comprehensive API documentation included

### Testing
- **Feature Tests** - Complete test coverage for web routes
- **Unit Tests** - Policy and model testing
- **API Tests** - Comprehensive API endpoint testing
- **20+ Tests** - 138+ assertions covering all functionality

## 🛠️ Tech Stack

- **Backend:** Laravel 12.x, PHP 8.3+
- **Database:** MySQL/PostgreSQL
- **Authentication:** Laravel Breeze + Sanctum
- **Frontend:** Blade templates with Tailwind CSS
- **API:** REST API with JSON responses
- **Testing:** PHPUnit
- **Styling:** Tailwind CSS

## 📋 Requirements

- PHP 8.3 or higher
- Composer
- Node.js & NPM (for frontend assets)
- MySQL/PostgreSQL
- Git

## 🚀 Installation

> 📘 **Need detailed setup instructions?** See [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md) for comprehensive platform-specific guides (Windows, Linux, macOS, Docker).

### Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/michal94mk/blog_laravel_vue.git
cd blog_laravel_vue
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node.js dependencies
```bash
npm install
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_laravel_vue
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build frontend assets
```bash
npm run build
```

### 8. Start the development server
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## 🚀 Vue.js Frontend Development

### Setup Vue.js Frontend
```bash
cd frontend
npm install
```

### Start Vue.js Development Server
```bash
cd frontend
npm run dev
```

The Vue.js frontend will be available at `http://127.0.0.1:3000`

### Build Vue.js for Production
```bash
cd frontend
npm run build
```

This will build the Vue.js app into `public/frontend/` directory.

## 🧪 Testing

### Run all tests
```bash
php artisan test
```

### Run specific test suites
```bash
# Feature tests
php artisan test tests/Feature/

# Unit tests
php artisan test tests/Unit/

# API tests
php artisan test tests/Feature/Api/
```

### Test coverage
```bash
php artisan test --coverage
```

## 📚 API Documentation

The application includes a complete REST API. See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed API documentation.

### Quick API Examples

#### Register a new user
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

#### Login
```bash
curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Get all posts
```bash
curl -X GET http://127.0.0.1:8000/api/v1/posts
```

#### Create a post (authenticated)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "This is the content of my first post."
  }'
```

## 🎯 Usage

### Web Interface
1. Visit `http://127.0.0.1:8000`
2. Register a new account or login
3. Create, edit, and delete your posts
4. Add comments to posts
5. View all posts and comments

### API Usage
1. Register/login via API to get authentication token
2. Use token in Authorization header for protected endpoints
3. Guests can view posts and add comments without authentication
4. See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete reference

## 🏗️ Project Structure

```
blog_laravel_vue/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # API controllers
│   │   │   └── ...            # Web controllers
│   │   ├── Requests/          # Form validation
│   │   └── Resources/         # API resources
│   ├── Models/                # Eloquent models
│   └── Policies/              # Authorization policies
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── frontend/                  # Vue.js SPA
│   ├── src/
│   │   ├── components/        # Vue components
│   │   ├── views/            # Page components
│   │   ├── stores/           # Pinia stores
│   │   ├── router/           # Vue Router
│   │   ├── services/         # API services
│   │   └── assets/           # Static assets
│   ├── package.json          # Frontend dependencies
│   └── vite.config.js        # Vite configuration
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
├── tests/
│   ├── Feature/              # Feature tests
│   └── Unit/                 # Unit tests
├── resources/
│   ├── views/                # Blade templates
│   └── css/                  # Tailwind CSS
└── API_DOCUMENTATION.md      # API documentation
```

## 🔐 Authentication & Authorization

### Web Authentication
- Uses Laravel Breeze for authentication scaffolding
- Session-based authentication for web interface
- CSRF protection enabled

### API Authentication
- Uses Laravel Sanctum for API authentication
- Token-based authentication
- Token refresh functionality
- CORS enabled for frontend integration

### Authorization
- **Posts:** Users can only edit/delete their own posts
- **Comments:** Users can edit/delete their own comments
- **Post Owners:** Can delete any comment on their posts
- **Guests:** Can view posts and add comments

## 🎨 Frontend

### Vue.js SPA (Single Page Application)
- **Vue 3** with Composition API
- **Vue Router** for client-side navigation
- **Pinia** for state management
- **Axios** for API communication
- **Vite** for fast development and building
- **Tailwind CSS** for styling

### Blade Templates (Legacy)
- Responsive design with Tailwind CSS
- Modern, clean interface
- Mobile-friendly layout
- Form validation with error display

### Styling
- Tailwind CSS for utility-first styling
- Custom CSS classes for specific components
- Responsive grid layouts
- Modern typography and spacing

## 🧪 Testing Strategy

### Test Coverage
- **Feature Tests:** Web routes, authentication, CRUD operations
- **Unit Tests:** Policies, models, validation
- **API Tests:** All API endpoints, authentication, authorization
- **Database Tests:** Migrations, seeders, factories

### Test Data
- Uses Laravel factories for test data generation
- Database transactions for test isolation
- Comprehensive test scenarios including edge cases

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up web server (Apache/Nginx)
- [ ] Configure SSL certificate
- [ ] Set up database backups

### Environment Variables
```env
APP_NAME="Laravel Blog"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Michał** - [@michal94mk](https://github.com/michal94mk)

## 🙏 Acknowledgments

- Laravel framework and community
- Laravel Breeze for authentication scaffolding
- Laravel Sanctum for API authentication
- Tailwind CSS for styling
- PHPUnit for testing framework

---

## 📊 Project Statistics

- **Lines of Code:** 2000+
- **Test Coverage:** 95%+
- **API Endpoints:** 15+
- **Test Cases:** 20+
- **Features:** Authentication, CRUD, API, Testing, Documentation

## 🎯 Future Enhancements

- [x] Vue.js frontend integration
- [ ] Docker containerization
- [ ] Role-based access control (admin panel)
- [ ] File upload for post images
- [ ] Email notifications
- [ ] Search functionality
- [ ] Post categories and tags
- [ ] Social media integration