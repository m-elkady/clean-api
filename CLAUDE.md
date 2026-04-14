# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clean API is a full-stack application with a Symfony 7.4/PHP 8.4 backend and Vue 3/Vuetify frontend, demonstrating clean architecture patterns with clear separation of concerns.

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
    ↓ deserializes JSON → Request DTO
Service (business logic, validation)
    ↓ uses
Repository (data access via Doctrine ORM)
    ↓ returns
Entity (doctrine-mapped domain objects)
    ↓ wrapped in
Response DTO (serialized to JSON)
```

**Key patterns:**
- **Request DTOs** (`src/Request/`): Validate incoming data using Symfony Validator constraints. Include static `fromJson()` for deserialization.
- **Response DTOs** (`src/Response/`): Wrap outgoing data, extend `BaseResponse` for consistent JSON structure with `success`, `message`, `code`.
- **Services** (`src/Service/`): Contain business logic, validate requests via `ValidatorInterface`, return Response DTOs.
- **Repositories** (`src/Repository/`): Extend `ServiceEntityRepository`, include custom pagination using Doctrine's `Paginator`.
- **Controllers** (`src/Controller/`): Thin layer using `BaseController` for serialization. Routes defined via PHP 8 `#[Route]` attributes.

**BaseController helpers:**
- `getRequest(mixed $data, string $requestClass)` - deserialize to Request DTO
- `getResponse($response)` - serialize Response DTO to JSON

### Frontend Structure

- **Vite + Vue 3** with file-based routing via `unplugin-vue-router`
- **Vuetify 3** component library with auto-import
- **Axios client** configured at `src/services/axios.js` (base URL: `http://clean-api.localhost`)
- Routes defined manually in `src/router/index.js` (two routes: `/` for Hello, `/users` for UsersList)

### Docker Services

| Container | Purpose | Access |
|-----------|---------|--------|
| backend-clean-api | PHP 8.4-FPM | `docker exec backend-clean-api ...` |
| mysql-clean-api | MySQL 5.7 | localhost:3306 |
| nginx-clean-api | Nginx reverse proxy | http://clean-api.localhost (backend API) |
| (frontend) | Run locally for dev | http://localhost:3000 |

**Required hosts entry:** Add `127.0.0.1 clean-api.localhost` to `/etc/hosts`

### Database

- **Doctrine ORM** with MySQL 5.7
- **Migrations** in `backend/migrations/` (versioned, apply via `doctrine:migrations:migrate`)
- **Fixtures** in `backend/src/DataFixtures/` (load via `doctrine:fixtures:load`)

## API Endpoints (User resource)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/user` | Create user |
| GET | `/user/{value}/{fieldName}` | Get user by field (default: `id`) |
| PUT/PATCH | `/user/{id}` | Update user |
| DELETE | `/user/{id}` | Delete user |
| GET | `/user?perPage=10&sortBy=firstName&order=desc&firstName=Search` | List/search with pagination |

## Adding New Resources

When adding a new entity (e.g., Product):

1. **Entity**: Create in `src/Entity/Product.php` with Doctrine attributes, `setData()` method
2. **Repository**: Create in `src/Repository/ProductRepository.php`, extend `ServiceEntityRepository`
3. **Request DTOs**: Create in `src/Request/` (AddProductRequest, etc.) with validation attributes
4. **Response DTOs**: Create in `src/Response/` extending `BaseResponse`
5. **Service**: Create in `src/Service/ProductService.php` with validation and business logic
6. **Controller**: Create in `src/Controller/ProductsController.php` extending `BaseController`, add `#[Route]` attributes
7. **Migration**: Generate via `docker exec backend-clean-api php bin/console doctrine:migrations:generate`
8. **Frontend**: Add axios methods in `src/services/axios.js`, create Vue components as needed
