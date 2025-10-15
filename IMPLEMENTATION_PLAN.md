## Szczegółowy plan implementacji bloga w Laravel

### Założenia
- **Laravel**: 12.x, **PHP**: 8.3+
- **Auth**: Laravel Breeze + Sanctum (API authentication)
- **Frontend**: Vue SPA (w katalogu /frontend) + API integration
- **DB**: MySQL/MariaDB lub PostgreSQL
- **Komentarze gości**: dozwolone (kolumna `user_id` w `comments` nullable)
- **Role (opcjonalnie)**: `admin`
- **Struktura**: Laravel API + Vue SPA (jeden projekt, monorepo)

### 0) Strategia branchowania (Git)
- Rekomendacja: trunk-based development (prosto i szybko), z krótkimi gałęziami feature
  - `main`: chroniona, zawsze releasowalna
  - `feature/<skrót-opisu>`: 1 zadanie = 1 gałąź; PR do `main` (squash merge)
  - Hotfix: `hotfix/<opis>` od `main`, PR do `main`
- Zasady PR:
  - małe PR-y (<300 linii diff), opis zmian, link do taska
  - minimum 1 review, zielone testy CI, konwencja commitów (np. Conventional Commits)
- Alternatywa (większe zespoły): uproszczony GitFlow
  - `main` (prod), `develop` (integracja), `feature/*`, `release/*`, `hotfix/*`
  - Deploy z `main`, przygotowanie releasu z `release/*`

#### Konwencje nazw branchy
- `feature/<ID-issue>-<krotki-opis>` (np. `feature/123-post-crud`)
- `fix/<ID-issue>-<opis>` (np. `fix/201-comment-policy`)
- `chore/<opis>` (np. `chore/ci-pipeline`)
- `docs/<opis>` (np. `docs/readme-setup`)
- `test/<opis>` (np. `test/post-feature-tests`)

#### Konwencje commitów (Conventional Commits)
- Format: `typ(scope): krotki opis`
- Typy: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`, `revert`
- Przykłady:
  - `feat(posts): add create/update/delete with policies`
  - `fix(comments): enforce delete policy for post owner`
  - `docs: extend implementation plan for Laravel 12`
  - `test(posts): add feature tests for update authorization`
- Dłuższy opis w treści commita (po pustej linii), opcjonalnie `BREAKING CHANGE:`

#### Minimalny szablon PR (do skopiowania do opisu PR lub jako plik `.github/PULL_REQUEST_TEMPLATE.md`)
```markdown
## Co zostało zrobione
- [ ] Krótki opis zmian

## Kontekst / Powód
Powiązane zadanie/issue: #<ID>

## Zakres zmian
- [ ] Backend
- [ ] Frontend
- [ ] Migracje
- [ ] Testy

## Jak przetestować
Kroki manualne / komendy:
```
php artisan migrate
php artisan test
```

## Lista kontrolna
- [ ] Testy zielone (CI)
- [ ] Pokryte testami kluczowe ścieżki
- [ ] Brak zmian w publicznym API lub opisano je w `BREAKING CHANGE`
- [ ] PR < 300 linii diff (lub uzasadnienie)
```

### 1) Inicjalizacja projektu Laravel (Backend)
**Branch**: `feature/01-laravel-setup`
```bash
composer create-project laravel/laravel:^12 blog_laravel_vue
cd blog_laravel_vue
cp .env.example .env
php artisan key:generate

# Skonfiguruj .env (DB_*, APP_URL, SANCTUM_STATEFUL_DOMAINS)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=blog
# DB_USERNAME=root
# DB_PASSWORD=secret
# SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### 2) Uwierzytelnianie (Laravel Breeze + Sanctum)
**Branch**: `feature/02-auth-setup`
```bash
# Breeze (auth scaffolding)
composer require laravel/breeze --dev
php artisan breeze:install blade  # tylko dla auth scaffolding

# Sanctum (API authentication)
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Konfiguracja Sanctum w app/Http/Kernel.php
# Dodaj EnsureFrontendRequestsAreStateful do api middleware group
```

### 3) Modele, migracje, relacje
**Branch**: `feature/03-models-migrations`
```bash
php artisan make:model Post -m
php artisan make:model Comment -m
```
- Migracja `posts`:
  - `id`, `user_id` (FK, cascade on delete), `title` (string), `content` (text), timestamps
- Migracja `comments`:
  - `id`, `post_id` (FK, cascade on delete), `user_id` (nullable, FK, null on delete), `comment` (text), timestamps
- Relacje Eloquent:
  - `User` hasMany `Post`
  - `Post` belongsTo `User`
  - `Post` hasMany `Comment`
  - `Comment` belongsTo `Post`
  - `Comment` belongsTo `User` (nullable)

### 4) API Kontrolery i trasy
**Branch**: `feature/04-api-controllers`
```bash
php artisan make:controller Api/PostController --resource
php artisan make:controller Api/CommentController
php artisan make:controller Api/AuthController
```
- Trasy (`routes/api.php`):
```php
// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Posts (API)
Route::apiResource('posts', PostController::class);
Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');
```

### 5) Web Routes (redirect do Vue)
**Branch**: `feature/05-web-routes`
- Trasy (`routes/web.php`):
```php
// Redirect do Vue SPA
Route::get('/posts', function () {
    return redirect('http://localhost:3000/posts');
});
Route::get('/posts/create', function () {
    return redirect('http://localhost:3000/posts/create');
});
Route::get('/posts/{id}', function ($id) {
    return redirect("http://localhost:3000/posts/{$id}");
});
Route::get('/posts/{id}/edit', function ($id) {
    return redirect("http://localhost:3000/posts/{$id}/edit");
});
```

### 6) JSON Resources
**Branch**: `feature/06-json-resources`
```bash
php artisan make:resource PostResource
php artisan make:resource CommentResource
php artisan make:resource UserResource
```

### 7) Walidacja (Form Requests)
**Branch**: `feature/07-validation`
```bash
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest
php artisan make:request StoreCommentRequest
```
- Post: `title` required|max:255, `content` required
- Comment: `comment` required|min:1; `user_id` przypisz, jeśli zalogowany

### 8) Autoryzacja (Policies)
**Branch**: `feature/08-policies`
```bash
php artisan make:policy PostPolicy --model=Post
php artisan make:policy CommentPolicy --model=Comment
```
- `PostPolicy`:
  - `update`: właściciel posta lub admin
  - `delete`: właściciel posta lub admin
- `CommentPolicy`:
  - `delete`: właściciel komentarza lub właściciel posta lub admin
- (Opcjonalnie) kolumna `role` w `users` + metoda `isAdmin()` w `User`

### 9) Seedery i Factories
**Branch**: `feature/09-seeders`
```bash
php artisan make:factory PostFactory --model=Post
php artisan make:factory CommentFactory --model=Comment
php artisan make:seeder DatabaseSeeder
```
- `DatabaseSeeder`:
  - utwórz admina (`role = 'admin'`)
  - kilku użytkowników, do każdego kilka postów
  - do każdego posta kilka komentarzy (część gości, część zalogowanych)
```bash
php artisan migrate:fresh --seed
```

### 10) Vue Frontend (SPA)
**Branch**: `feature/10-vue-frontend`
```bash
# W katalogu frontend (wewnątrz Laravel)
mkdir frontend
cd frontend
npm create vue@latest .
npm install
npm install axios pinia vue-router@4
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### 11) Vue Komponenty i Routing
**Branch**: `feature/11-vue-components`
- Komponenty:
  - `PostsList.vue`: lista postów
  - `PostCreate.vue`: formularz tworzenia
  - `PostEdit.vue`: formularz edycji
  - `PostShow.vue`: szczegóły posta + komentarze
  - `CommentForm.vue`: formularz komentarza
- Routing (`router/index.js`):
  - `/posts` → PostsList
  - `/posts/create` → PostCreate
  - `/posts/:id` → PostShow
  - `/posts/:id/edit` → PostEdit

### 12) Vue Auth i API Integration
**Branch**: `feature/12-vue-auth`
- Pinia store dla auth
- Axios interceptors dla tokenów
- Login/Register/Logout komponenty
- API calls do Laravel backend

### 13) Testy (opcjonalnie, zalecane)
**Branch**: `feature/13-tests`
```bash
php artisan make:test PostApiTest
php artisan make:test CommentApiTest
php artisan make:test AuthApiTest
php artisan make:test PostPolicyTest --unit
php artisan make:test CommentPolicyTest --unit
```
- Feature: API endpoints, autoryzacja, dodawanie/usuwanie komentarzy
- Unit: polityki + relacje modeli

### 14) Docker (opcjonalnie)
**Branch**: `feature/14-docker`
- `docker-compose.yml`: `app` (php-fpm), `nginx`, `db` (mysql/postgres), `frontend` (node)
- `Dockerfile`: obraz `php:8.3-fpm` + rozszerzenia pdo, mbstring, intl, opcache
- Kroki: mount kodu, `composer install`, `php artisan key:generate`, `php artisan migrate --seed`

### 15) Instrukcje uruchomienia (README)
**Branch**: `feature/15-documentation`
```bash
# Backend (Laravel)
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve  # http://localhost:8000

# Frontend (Vue)
cd frontend
npm install
npm run dev  # http://localhost:3000
```
- Zaloguj się na konto admina utworzone przez seedery.

### 16) Publikacja na GitHub
**Branch**: `feature/16-github-setup`
```bash
# Jeden repo (monorepo)
git init
git add .
git commit -m "feat: Laravel API + Vue SPA blog application"
git branch -M main
git remote add origin https://github.com/<user>/blog_laravel_vue.git
git push -u origin main
```

### 17) Krytyczne detale
- **Komentarze gości**: `user_id` w `comments` nullable
- **Uprawnienia**: policy dla edycji/usuwania postów i usuwania komentarzy
- **Eager loading**: `Post::with(['user','comments.user'])` w API
- **CORS**: skonfigurowany dla frontendu
- **Rate limiting**: rozważ na `comments.store`
- **Token management**: refresh tokens, secure storage

---
Jeśli chcesz, mogę od razu wygenerować pliki (modele, migracje, kontrolery, policies, requests, widoki) zgodnie z planem oraz dodać seedery i testy.


