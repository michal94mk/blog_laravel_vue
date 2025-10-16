# Laravel Blog - Vue.js Frontend

This is the Vue.js frontend for the Laravel Blog application.

## Features

- **Vue 3** with Composition API
- **Vue Router** for client-side navigation
- **Pinia** for state management
- **Axios** for API communication
- **Tailwind CSS** for styling
- **Vite** for fast development and building
- **Responsive Design** with mobile-first approach
- **Authentication** with token-based auth
- **CRUD Operations** for posts and comments
- **Real-time Updates** with reactive state management

## Setup

### Prerequisites
- Node.js 18+ 
- NPM or Yarn
- Laravel backend running on port 8000

### Installation

1. Install dependencies:
```bash
npm install
```

2. Start development server:
```bash
npm run dev
```

3. Build for production:
```bash
npm run build
```

4. Preview production build:
```bash
npm run preview
```

## Development

The frontend runs on port 3000 and proxies API requests to the Laravel backend on port 8000.

### Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build

### Environment Variables

Create a `.env` file in the frontend directory:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api/v1
VITE_APP_NAME="Laravel Blog"
VITE_APP_VERSION=1.0.0
```

## Project Structure

```
frontend/
├── public/           # Static assets
├── src/
│   ├── components/   # Reusable Vue components
│   │   ├── Layout.vue
│   │   ├── Navbar.vue
│   │   ├── Loading.vue
│   │   ├── ErrorMessage.vue
│   │   ├── EmptyState.vue
│   │   └── Pagination.vue
│   ├── views/        # Page components
│   │   ├── Home.vue
│   │   ├── Login.vue
│   │   ├── Register.vue
│   │   ├── PostDetail.vue
│   │   ├── CreatePost.vue
│   │   └── EditPost.vue
│   ├── stores/       # Pinia stores
│   │   ├── auth.js
│   │   ├── posts.js
│   │   └── comments.js
│   ├── router/       # Vue Router configuration
│   │   └── index.js
│   ├── services/     # API services
│   │   └── api.js
│   └── assets/       # Static assets and styles
│       └── main.css
├── package.json
├── vite.config.js
├── tailwind.config.js
└── README.md
```

## Features Overview

### Authentication
- User registration and login
- Token-based authentication
- Protected routes
- Automatic token refresh
- Logout functionality

### Posts Management
- View all posts with pagination
- Create new posts (authenticated users)
- Edit own posts
- Delete own posts
- View individual post details

### Comments System
- Add comments to posts
- Edit own comments
- Delete own comments
- Post owners can delete any comment
- Guest users can add comments

### UI/UX Features
- Responsive design
- Loading states
- Error handling
- Empty states
- Form validation
- Navigation guards
- Mobile-friendly interface

## API Integration

The frontend communicates with the Laravel backend through REST API endpoints:

- `GET /api/v1/posts` - List all posts
- `POST /api/v1/posts` - Create new post
- `GET /api/v1/posts/{id}` - Get post details
- `PUT /api/v1/posts/{id}` - Update post
- `DELETE /api/v1/posts/{id}` - Delete post
- `POST /api/v1/posts/{id}/comments` - Add comment
- `PUT /api/v1/comments/{id}` - Update comment
- `DELETE /api/v1/comments/{id}` - Delete comment

## Styling

The project uses Tailwind CSS for styling with custom components and utilities. The design is:

- Mobile-first responsive
- Clean and modern
- Accessible
- Consistent color scheme
- Proper typography hierarchy

## Browser Support

- Chrome 88+
- Firefox 85+
- Safari 14+
- Edge 88+

## Contributing

1. Follow Vue.js style guide
2. Use Composition API
3. Write clean, readable code
4. Add proper error handling
5. Test on multiple devices
6. Follow accessibility guidelines
