# Setup Instructions

Detailed setup instructions for different operating systems and environments.

## 🪟 Windows Setup

### Prerequisites
1. **Install XAMPP** (includes PHP, MySQL, Apache)
   - Download from: https://www.apachefriends.org/
   - Install with default settings
   - Start Apache and MySQL from XAMPP Control Panel

2. **Install Composer**
   - Download from: https://getcomposer.org/download/
   - Run the installer
   - Verify: `composer --version`

3. **Install Node.js**
   - Download from: https://nodejs.org/
   - Install LTS version
   - Verify: `node --version` and `npm --version`

4. **Install Git**
   - Download from: https://git-scm.com/
   - Install with default settings
   - Verify: `git --version`

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/michal94mk/blog_laravel_vue.git
cd blog_laravel_vue

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
copy .env.example .env
php artisan key:generate

# 4. Database setup
# Edit .env file with your database credentials
# Create database in phpMyAdmin (http://localhost/phpmyadmin)

# 5. Run migrations
php artisan migrate
php artisan db:seed

# 6. Build assets
npm run build

# 7. Start server
php artisan serve
```

### Windows Troubleshooting
- **PHP not found:** Add PHP to PATH environment variable
- **Composer not found:** Restart command prompt after installation
- **Permission errors:** Run command prompt as Administrator
- **Port conflicts:** Change port with `php artisan serve --port=8001`

---

## 🐧 Linux Setup (Ubuntu/Debian)

### Prerequisites
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Git
sudo apt install git -y
```

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/michal94mk/blog_laravel_vue.git
cd blog_laravel_vue

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
sudo mysql -u root -p
# Create database: CREATE DATABASE blog_laravel_vue;
# Create user: CREATE USER 'blog_user'@'localhost' IDENTIFIED BY 'password';
# Grant privileges: GRANT ALL PRIVILEGES ON blog_laravel_vue.* TO 'blog_user'@'localhost';
# Flush privileges: FLUSH PRIVILEGES;
# Exit: EXIT;

# 5. Update .env file
nano .env
# Update database credentials

# 6. Run migrations
php artisan migrate
php artisan db:seed

# 7. Build assets
npm run build

# 8. Start server
php artisan serve
```

### Linux Troubleshooting
- **Permission errors:** `sudo chown -R $USER:$USER storage bootstrap/cache`
- **PHP extensions missing:** Install required extensions with apt
- **MySQL connection:** Check if MySQL service is running: `sudo systemctl status mysql`

---

## 🍎 macOS Setup

### Prerequisites
1. **Install Homebrew**
   ```bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```

2. **Install PHP**
   ```bash
   brew install php@8.3
   brew link php@8.3
   ```

3. **Install Composer**
   ```bash
   brew install composer
   ```

4. **Install Node.js**
   ```bash
   brew install node
   ```

5. **Install MySQL**
   ```bash
   brew install mysql
   brew services start mysql
   ```

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/michal94mk/blog_laravel_vue.git
cd blog_laravel_vue

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
mysql -u root -p
# Create database: CREATE DATABASE blog_laravel_vue;
# Create user: CREATE USER 'blog_user'@'localhost' IDENTIFIED BY 'password';
# Grant privileges: GRANT ALL PRIVILEGES ON blog_laravel_vue.* TO 'blog_user'@'localhost';
# Flush privileges: FLUSH PRIVILEGES;
# Exit: EXIT;

# 5. Update .env file
nano .env
# Update database credentials

# 6. Run migrations
php artisan migrate
php artisan db:seed

# 7. Build assets
npm run build

# 8. Start server
php artisan serve
```

---

## 🐳 Docker Setup (Optional)

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/michal94mk/blog_laravel_vue.git
cd blog_laravel_vue

# 2. Create Docker environment
cp .env.example .env

# 3. Build and start containers
docker-compose up -d

# 4. Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# 5. Generate key and run migrations
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# 6. Build assets
docker-compose exec app npm run build

# 7. Access application
# Web: http://localhost:8000
# API: http://localhost:8000/api/v1
```

---

## 🧪 Testing Setup

### Run Tests
```bash
# All tests
php artisan test

# Specific test suites
php artisan test tests/Feature/
php artisan test tests/Unit/
php artisan test tests/Feature/Api/

# With coverage
php artisan test --coverage
```

### Test Database
```bash
# Create test database
mysql -u root -p
CREATE DATABASE blog_laravel_vue_test;

# Update .env.testing
cp .env .env.testing
# Update DB_DATABASE=blog_laravel_vue_test
```

---

## 🔧 Development Tools

### Recommended VS Code Extensions
- PHP Intelephense
- Laravel Blade Snippets
- Tailwind CSS IntelliSense
- GitLens
- REST Client

### Postman Collection
Import the API endpoints into Postman for testing:
1. Create new collection
2. Add requests for all API endpoints
3. Set up environment variables
4. Test authentication flow

### Database Management
- **phpMyAdmin** (XAMPP)
- **MySQL Workbench**
- **TablePlus**
- **Sequel Pro** (macOS)

---

## 🚨 Common Issues & Solutions

### Issue: "Class not found" errors
**Solution:**
```bash
composer dump-autoload
```

### Issue: "Permission denied" on storage
**Solution:**
```bash
# Linux/macOS
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Windows
# Run command prompt as Administrator
```

### Issue: "Database connection failed"
**Solution:**
1. Check if database service is running
2. Verify credentials in `.env`
3. Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

### Issue: "Token mismatch" errors
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: "Module not found" (Node.js)
**Solution:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### Issue: API returns 405 Method Not Allowed
**Solution:**
- Check if `EnsureFrontendRequestsAreStateful` middleware is disabled for API
- Verify route definitions in `routes/api.php`

---

## 📞 Support

If you encounter issues:
1. Check this troubleshooting guide
2. Review Laravel documentation
3. Check GitHub issues
4. Create a new issue with detailed error information

---

## 🎯 Next Steps

After successful setup:
1. Explore the web interface
2. Test the API endpoints
3. Run the test suite
4. Review the code structure
5. Consider contributing improvements
