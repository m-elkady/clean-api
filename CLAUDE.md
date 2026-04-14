# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clean API is a full-stack application with Symfony 7.4/PHP 8.4 backend and Vue 3/Vuetify frontend, demonstrating clean architecture patterns with clear separation of concerns.

## Development Commands

### Initial Setup
```bash
make init              # Build containers, install deps, run migrations, load fixtures, start frontend
```

### Docker Operations
```bash
make start             # Start all containers
make stop              # Stop all containers
```

### Backend (run inside container via `docker exec backend-clean-api`)
```bash
docker exec backend-clean-api composer install                    # Install PHP dependencies
docker exec backend-clean-api php bin/console doctrine:migrations:migrate  # Run migrations
docker exec backend-clean-api php bin/console doctrine:fixtures:load      # Load test fixtures
docker exec backend-clean-api php bin/phpunit                      # Run all tests
docker exec backend-clean-api php bin/console cache:clear          # Clear cache
```

### Frontend (run in local terminal)
```bash
cd frontend && npm install      # Install dependencies
npm run dev                     # Start dev server (port 3000)
npm run build                   # Build for production
npm run lint                    # Run ESLint with auto-fix
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
1. Deserializes JSON/query params to Request DTOs
2. Validates DTOs using Symfony Validator
3. Throws `ValidationException` on validation failures

Request DTOs must implement `RequestValidatedInterface`:
```php
interface RequestValidatedInterface
{
    public static function fromArray(array $data): self;
}
```

The resolver is registered in `backend/config/services.yaml` with priority 256.

**Response Pattern:**

Use `AppResponse` static methods for consistent JSON responses:
- `AppResponse::success($data)` - 200 OK
- `AppResponse::created($data)` - 201 Created
- `AppResponse::paginated($items, $total, $page, $perPage)` - Paginated response
- `AppResponse::notFound($message)` - 404
- `AppResponse::error($message, $status)` - Generic error

**Key patterns:**
- **Request DTOs** (`src/Request/`): Validate incoming data using Symfony Validator constraints. Include static `fromArray()` for data hydration.
- **Services** (`src/Service/`): Contain business logic. Throw `UnprocessableEntityHttpException` for business rule violations (e.g., duplicate email).
- **Repositories** (`src/Repository/`): Extend `ServiceEntityRepository`, include custom queries (e.g., `findByEmailExcludingId()`).
- **Controllers** (`src/Controller/`): Thin layer. Methods receive validated Request DTOs directly via constructor injection.

### Frontend Structure

- **Vite + Vue 3** with file-based routing via `unplugin-vue-router`
- **Vuetify 3** component library with auto-import
- **Axios client** configured at `frontend/src/services/axios.js` (base URL: `http://clean-api.localhost`)
- Routes defined in `frontend/src/router/index.js` (two routes: `/` for Hello, `/users` for UsersList)

### Docker Services

| Container         | Purpose             | Access                                   |
|-------------------|---------------------|------------------------------------------|
| backend-clean-api | PHP 8.4-FPM         | `docker exec backend-clean-api ...`      |
| mysql-clean-api   | MySQL 5.7           | localhost:3306                           |
| nginx-clean-api   | Nginx reverse proxy | http://clean-api.localhost (backend API) |
| (frontend)        | Run locally for dev | http://localhost:3000                    |

**Required hosts entry:** Add `127.0.0.1 clean-api.localhost` to `/etc/hosts`

### Database

- **Doctrine ORM** with MySQL 5.7
- **Migrations** in `backend/migrations/` (versioned, apply via `doctrine:migrations:migrate`)
- **Fixtures** in `backend/src/DataFixtures/` (load via `doctrine:fixtures:load`)

## API Endpoints (User resource)

| Method    | Endpoint                       | Description                   |
|-----------|--------------------------------|-------------------------------|
| POST      | `/user`                        | Create user                   |
| GET       | `/user/{id}`                   | Get user by ID                |
| GET       | `/user/by/{fieldName}/{value}` | Get user by field (default: id) |
| PATCH/PUT | `/user/{id}`                   | Update user                   |
| DELETE    | `/user/{id}`                   | Delete user                   |
| GET       | `/user`                        | List/search with pagination   |

## Adding New Resources

When adding a new entity (e.g., Product):

1. **Entity**: Create in `src/Entity/Product.php` with Doctrine attributes, getters/setters
2. **Repository**: Create in `src/Repository/ProductRepository.php`, extend `ServiceEntityRepository`
3. **Request DTOs**: Create in `src/Request/` (AddProductRequest, UpdateProductRequest, etc.) with:
   - Validation attributes (`#[Assert\NotBlank]`, etc.)
   - Default values for typed properties
   - `fromArray()` factory method
   - Implement `RequestValidatedInterface`
4. **Service**: Create in `src/Service/ProductService.php` with business logic
5. **Controller**: Create in `src/Controller/ProductsController.php`, add `#[Route]` attributes, inject Service
6. **Migration**: Generate via `docker exec backend-clean-api php bin/console doctrine:migrations:generate`
7. **Frontend**: Add axios methods in `src/services/axios.js`, create Vue components as needed
