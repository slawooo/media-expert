# Recruitment Task API

Simple Symfony REST API for managing records with current status and status history.
The project includes CRUD operations, filtering, basic authentication and automated tests.

## Setup and utility commands

### Installation
* `docker compose up -d --build`
* `docker compose exec --user 1000:1000 php composer install`
* `docker compose exec --user 1000:1000 php php bin/console doctrine:database:create`
* `docker compose exec --user 1000:1000 php php bin/console doctrine:migrations:migrate`

### Create test database
* `docker compose exec --user 1000:1000 php php bin/console doctrine:database:create --env=test`

### Base URL
* `https://localhost`

A browser or API client may show a warning about the local HTTPS certificate. This is expected in the local development environment.

### Other utility commands
* `docker compose exec --user 1000:1000 php bash`
* `docker compose down`

### Testing
* `docker compose exec --user 1000:1000 php php bin/phpunit --testdox`
* `docker compose exec --user 1000:1000 php php bin/phpunit tests/Unit`
* `docker compose exec --user 1000:1000 php php bin/phpunit tests/Integration`
* `docker compose exec --user 1000:1000 php php bin/phpunit tests/Functional`

### CS Fixer
* `docker compose exec --user 1000:1000 php vendor/bin/php-cs-fixer fix --dry-run --diff`
* `docker compose exec --user 1000:1000 php vendor/bin/php-cs-fixer fix`

## API usage

All `/api/*` endpoints require HTTP Basic Auth.

### Example credentials
* login: `api`
* password: `secret`

### Create record
* `POST /api/records`

Example payload:
```json
{
  "number": "REC-001",
  "status": "new"
}
```

### Get and filter records
* `GET /api/records`
* `GET /api/records?number=REC`
* `GET /api/records?currentStatus=processing`
* `GET /api/records?historicalStatus=new`
* `GET /api/records?createdFrom=2025-01-01 00:00:00&createdTo=2025-12-31 23:59:59`

### Get single record
* `GET /api/records/{id}`

### Update record
* `PUT /api/records/{id}`

Example payload:
```json
{
  "number": "REC-001-UPDATED"
}
```

### Change record status
* `PATCH /api/records/{id}/status`

Example payload:
```json
{
  "status": "processing"
}
```

### Delete record
* `DELETE /api/records/{id}`

## Architecture notes
The project is intentionally simple, but structured so that responsibilities are separated.
* `Entity` contains the data model and some domain logic.
* `Repository` contains read/query logic, including filtering by number, dates, current status and historical status.
* `Service` handles write operations and application flow for CRUD-related use cases.
* `Controller` exposes REST API endpoints.
* `Mapper` separates response mapping from HTTP handling logic.
* `Factory` separates request parsing from repository search logic.
* Tests are split into unit, integration and functional layers.

In a larger real-world project, I would consider more explicit separation between the domain and infrastructure layers, with a richer domain model and less coupling between domain objects and ORM mapping concerns (e.g. XML mappings instead of attributes).

### Design notes / trade-offs
* `currentStatus` is stored directly on `Record`, even though it is also represented in status history. This duplicates part of the data, but makes reading/filtering by current status simpler and more efficient.
* Status values are stored as strings. In a larger project, I would make the allowed statuses explicit in PHP (e.g. as constants, enums or value objects) to avoid arbitrary values.
* Test database schema is created directly from Doctrine entity metadata in the base test classes. For a production-grade setup, running migrations in tests would be closer to the real deployment flow, but this approach keeps test setup simpler.
* Authentication is implemented as minimal HTTP Basic Auth to secure the API without introducing unnecessary complexity.

## Task description
You can find the original task description here: [task.txt](docs/task.txt)

## Additional notes
This project was bootstrapped using the Symfony Docker template.  
Original template instructions was moved to [README.symfony.md](README.symfony.md)
