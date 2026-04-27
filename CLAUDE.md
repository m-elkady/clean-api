# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clean API is a full-stack application with Symfony 7.4/PHP 8.3+ backend (via FrankenPHP in worker mode) and Vue 3/Vuetify frontend, demonstrating clean architecture patterns with clear separation of concerns. Includes JWT authentication with refresh tokens.

The frontend is built and served via Docker/Nginx - there is no local dev server.

## Development Commands

### Initial Setup
```bash
make init              # Build containers, install deps, run migrations, load fixtures, build frontend
```

### Docker Operations
```bash
make start             # Start all containers
make stop              # Stop all containers
make restart           # Restart all containers
```

### Backend (run inside container via `docker exec backend-clean-api`)
```bash
docker exec backend-clean-api composer install                    # Install PHP dependencies
docker exec backend-clean-api php bin/console doctrine:migrations:migrate  # Run migrations
docker exec backend-clean-api php bin/console doctrine:fixtures:load      # Load test fixtures
docker exec backend-clean-api php bin/phpunit                      # Run all tests
docker exec backend-clean-api php bin/console cache:clear          # Clear cache
```

Or use Make shortcuts:
```bash
make test              # Run all tests
make console <command> # Run any Symfony console command (e.g., make console cache:clear)
```

### Frontend
```bash
cd frontend && npm install      # Install dependencies
npm run build                   # Build for production (served by Nginx)
npm run lint                    # Run ESLint with auto-fix
```

After making frontend changes, rebuild the frontend container:
```bash
docker-compose up -d --build frontend
# Or use: make frontend-build
```

## Architecture

### Backend Layer Structure

```
Controller (HTTP handling)
    ↓ via custom RequestResolver
Request DTOs (validated via Symfony Validator)
    ↓
Service (business logic)
    ↓
Repository (data access via Doctrine ORM)
    ↓
Entity (doctrine-mapped domain objects)
    ↓
Response via AppResponse (JSON)
```

**Custom RequestResolver Pattern:**

This project uses a custom `RequestResolver` (`backend/src/Resolver/RequestResolver.php`) that automatically:
1. Deserializes JSON/query params to Request DTOs using Symfony Serializer
2. Validates DTOs using Symfony Validator (calls `validate()` method)
3. Throws `ValidationException` on validation failures

Request DTOs must extend `BaseRequest`:
```php
abstract class BaseRequest
{
    public function validate(ValidatorInterface $validator): void
    {
        $errors = $validator->validate($this);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }
}
```

The resolver is registered in `backend/config/services.yaml` with priority 256.

**Response Pattern:**

Use `AppResponse` static methods for consistent JSON responses:
- `AppResponse::success($data)` - 200 OK
- `AppResponse::created($data)` - 201 Created
- `AppResponse::noContent()` - 204 No Content
- `AppResponse::validationError($errors)` - 422 Validation errors
- `AppResponse::notFound($message)` - 404
- `AppResponse::unauthorized($message)` - 401
- `AppResponse::error($message, $status)` - Generic error

**Key patterns:**
- **Request DTOs** (`src/Request/`): Extend `BaseRequest`, use typed properties with default values, add Symfony Validator attributes (`#[Assert\NotBlank]`, etc.)
- **Services** (`src/Service/`): Contain business logic. Throw `UnprocessableEntityHttpException` for business rule violations (e.g., duplicate email).
- **Repositories** (`src/Repository/`): Extend `ServiceEntityRepository`, include custom queries (e.g., `findByEmailExcludingId()`).
- **Controllers** (`src/Controller/`): Thin layer. Methods receive validated Request DTOs directly via constructor injection.

**Authentication:**

Uses LexikJWTAuthenticationBundle with custom refresh token storage:
- Access tokens (JWT) expire after 1 hour
- Refresh tokens stored in `refresh_tokens` table
- Auth endpoints in `AuthController`: `/auth/login`, `/auth/refresh`, `/auth/me`, `/auth/logout`
- Protected routes require `#[IsGranted('IS_AUTHENTICATED_FULLY')]` attribute

### Frontend Structure

- **Vite + Vue 3** with manual routing in `src/router/index.js`
- **Vuetify 3** component library with auto-import
- **Pinia** for state management (auth store at `src/stores/auth.js`)
- **Axios client** configured at `frontend/src/services/axios.js` (base URL: `http://api.clean-api.me`)
- Routes: `/` (Hello), `/users` (UsersList), `/login` (Login) - all except `/login` require authentication
- Navigation guard checks `auth.isAuthenticated` and redirects unauthenticated users to `/login`

**Note:** Frontend is served via Docker/Nginx, not a local dev server.

### Docker Services

| Container           | Purpose                        | Access                                   |
|---------------------|--------------------------------|------------------------------------------|
| backend-clean-api   | FrankenPHP (PHP 8.4)           | `docker exec backend-clean-api ...`    |
| frontend-clean-api  | Frontend builder (Vue 3)       | `docker-compose up -d --build frontend` |
| mysql-clean-api     | MySQL 5.7                      | localhost:3306                           |
| nginx-clean-api     | Nginx reverse proxy            | http://clean-api.me (frontend)          |
| nginx-clean-api     | Nginx reverse proxy            | http://api.clean-api.me (backend API)   |

**Required hosts entry:** Add `127.0.0.1 clean-api.me api.clean-api.me` to `/etc/hosts`

**Nginx configuration:**
- `docker/nginx/default.conf`: Backend API proxy (api.clean-api.me → backend:8080)
- `docker/nginx/frontend.conf`: Frontend static files (clean-api.me → /frontend/output)

**FrankenPHP configuration:**
- Configured in `docker/frankenphp/Caddyfile` with worker mode
- Worker script at `backend/public/worker.php`
- Runs 2 workers by default

### Database

- **Doctrine ORM** with MySQL 5.7
- **Migrations** in `backend/migrations/` (versioned, apply via `doctrine:migrations:migrate`)
- **Fixtures** in `backend/src/DataFixtures/` (load via `doctrine:fixtures:load`)

## API Endpoints

| Method    | Endpoint                       | Description                   | Auth Required |
|-----------|--------------------------------|-------------------------------|---------------|
| POST      | `/auth/login`                  | Login (email/password)        | No            |
| POST      | `/auth/refresh`                | Refresh access token          | No            |
| GET       | `/auth/me`                     | Get current user              | Yes           |
| POST      | `/auth/logout`                 | Logout                        | Yes           |
| POST      | `/user`                        | Create user                   | No            |
| GET       | `/user/{id}`                   | Get user by ID                | Yes           |
| GET       | `/user/by/{fieldName}/{value}` | Get user by field (default: id) | Yes      |
| PATCH/PUT | `/user/{id}`                   | Update user                   | Yes           |
| DELETE    | `/user/{id}`                   | Delete user                   | Yes           |
| GET       | `/user`                        | List/search with pagination   | Yes           |

## Adding New Resources

When adding a new entity (e.g., Product):

1. **Entity**: Create in `src/Entity/Product.php` with Doctrine attributes, getters/setters
2. **Repository**: Create in `src/Repository/ProductRepository.php`, extend `ServiceEntityRepository`
3. **Request DTOs**: Create in `src/Request/` (AddProductRequest, UpdateProductRequest, etc.) with:
   - Extend `BaseRequest`
   - Typed properties with default values
   - Validation attributes (`#[Assert\NotBlank]`, etc.)
4. **Service**: Create in `src/Service/ProductService.php` with business logic
5. **Controller**: Create in `src/Controller/ProductsController.php`, add `#[Route]` attributes, inject Service
6. **Migration**: Generate via `docker exec backend-clean-api php bin/console doctrine:migrations:generate`
7. **Frontend**: Add axios methods in `src/services/axios.js`, create Vue components as needed
8. **Rebuild frontend**: `docker-compose up -d --build frontend`