# Clean API

A full-stack application demonstrating clean architecture patterns with Symfony 7.4/PHP 8.3+ backend and Vue 3/Vuetify
frontend, featuring JWT authentication with refresh tokens.

## Features

- **Clean Architecture**: Clear separation of concerns with layered design (Controller → Service → Repository → Entity)
- **JWT Authentication**: Secure authentication with access tokens and refresh tokens using LexikJWTAuthenticationBundle
- **Request Validation**: DTO-based validation using Symfony Validator with custom RequestResolver
- **RESTful API**: Standardized JSON responses with proper HTTP status codes
- **Pagination**: Efficient data pagination with filtering and sorting
- **Email Uniqueness**: Database-level unique constraints with application-level validation
- **Modern Frontend**: Vue 3 with Vuetify components, Pinia state management, and authentication guards

## Tech Stack

| Layer            | Technology                                            |
|------------------|-------------------------------------------------------|
| Backend          | Symfony 7.4, PHP 8.3+, Doctrine ORM                   |
| Authentication   | LexikJWTAuthenticationBundle (JWT + refresh tokens)   |
| Database         | MySQL 5.7                                              |
| Frontend         | Vue 3, Vuetify 3, Vite, Pinia, Vue Router             |
| Web Server       | Nginx                                                  |
| Containerization | Docker & Docker Compose                                |

## Prerequisites

- Docker & Docker Compose
- PHP 8.3+ (for local development)
- Node.js 18+ (for frontend)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/m-elkady/clean-api.git
   cd clean-api
   ```

2. **Add host entry**
   ```bash
   echo "127.0.0.1 clean-api.localhost" | sudo tee -a /etc/hosts
   ```

3. **Initialize the project**
   ```bash
   make init
   ```
   This command will:
    - Build and start Docker containers
    - Install backend dependencies
    - Run database migrations
    - Load test fixtures
    - Start the frontend dev server

## API Endpoints

### Authentication

| Method | Endpoint         | Description                    | Auth Required |
|--------|------------------|--------------------------------|---------------|
| POST   | `/auth/login`    | Login with email and password  | No            |
| POST   | `/auth/refresh`  | Refresh access token           | No            |
| GET    | `/auth/me`       | Get current authenticated user | Yes           |
| POST   | `/auth/logout`   | Logout and invalidate tokens   | Yes           |

### Users

| Method    | Endpoint                       | Description                 | Auth Required |
|-----------|--------------------------------|-----------------------------|---------------|
| POST      | `/user`                        | Create user                 | No            |
| GET       | `/user/{id}`                   | Get user by ID              | Yes           |
| GET       | `/user/by/{fieldName}/{value}` | Get user by any field       | Yes           |
| PATCH/PUT | `/user/{id}`                   | Update user                 | Yes           |
| DELETE    | `/user/{id}`                   | Delete user                 | Yes           |
| GET       | `/user`                        | List/search with pagination | Yes           |

### Examples

**Login:**
```bash
POST http://clean-api.localhost/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Create User:**
```bash
POST http://clean-api.localhost/user
Content-Type: application/json

{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Pagination:**
```bash
GET http://clean-api.localhost/user?page=1&perPage=10&sortBy=firstName&order=desc&email=john
Authorization: Bearer <access_token>
```

## Development

### Backend Commands

```bash
# Run tests
docker exec backend-clean-api php bin/phpunit

# Clear cache
docker exec backend-clean-api php bin/console cache:clear

# Generate migration
docker exec backend-clean-api php bin/console doctrine:migrations:generate

# Load fixtures
docker exec backend-clean-api php bin/console doctrine:fixtures:load
```

### Frontend Commands

```bash
cd frontend

# Start dev server
npm run dev

# Build for production
npm run build

# Lint code
npm run lint
```

## Access

| Service     | URL                        |
|-------------|----------------------------|
| Backend API | http://clean-api.localhost |
| Frontend    | http://localhost:3000      |

## License

MIT