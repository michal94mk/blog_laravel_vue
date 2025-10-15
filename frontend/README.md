# Laravel Blog - Vue.js Frontend

This is the Vue.js frontend for the Laravel Blog application.

## Features

- Vue 3 with Composition API
- Vue Router for navigation
- Pinia for state management
- Axios for API communication
- Tailwind CSS for styling
- Vite for build tooling

## Setup

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

## Development

The frontend runs on port 3000 and proxies API requests to the Laravel backend on port 8000.

## Project Structure

```
src/
├── components/     # Reusable Vue components
├── views/         # Page components
├── stores/        # Pinia stores
├── router/        # Vue Router configuration
├── services/      # API services
└── assets/        # Static assets
```
