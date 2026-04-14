# Clean API

A full-stack application demonstrating clean architecture patterns with Symfony 7.4/PHP 8.4 backend and Vue 3/Vuetify frontend.

## Features

- **Clean Architecture**: Clear separation of concerns with layered design (Controller → Service → Repository → Entity)
- **Request Validation**: DTO-based validation using Symfony Validator
- **RESTful API**: Standardized JSON responses with proper HTTP status codes
- **Pagination**: Efficient data pagination with filtering and sorting
- **Email Uniqueness**: Database-level unique constraints with application-level validation
- **Modern Frontend**: Vue 3 with Vuetify components and file-based routing

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Symfony 7.4, PHP 8.4, Doctrine ORM |
| Database | MySQL 5.7 |
| Frontend | Vue 3, Vuetify 3, Vite |
| Web Server | Nginx |
| Containerization | Docker & Docker Compose |

## Prerequisites

- Docker & Docker Compose
- PHP 8.4+ (for local development)
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

## Manual Setup (Alternative)

### Backend

```bash
# Start containers
make start

# Install dependencies
docker exec backend-clean-api composer install

# Run migrations
docker exec backend-clean-api php bin/console doctrine:migrations:migrate

# Load fixtures
docker exec backend-clean-api php bin/console doctrine:fixtures:load
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/user` | Create user |
| GET | `/user/{id}` | Get user by ID |
| GET | `/user/by/{fieldName}/{value}` | Get user by any field |
| PATCH/PUT | `/user/{id}` | Update user |
| DELETE | `/user/{id}` | Delete user |
| GET | `/user` | List/search with pagination |

### Create User Example

```bash
POST http://clean-api.localhost/user
Content-Type: application/json

{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com"
}
```

### Pagination Example

```bash
GET http://clean-api.localhost/user?page=1&perPage=10&sortBy=firstName&order=desc&email=john
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

| Service | URL |
|---------|-----|
| Backend API | http://clean-api.localhost |
| Frontend | http://localhost:3000 |

## License

MIT
