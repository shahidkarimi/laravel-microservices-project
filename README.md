# Laravel Microservices Project

This project consists of two microservices: "users" and "notifications", communicating via Redis pub/sub.

## Requirements

- Docker
- Docker Compose

## Setup

1. Clone the repository:
   ```
   git clone https://github.com/shahidkarimi/laravel-microservices-project.git
   cd laravel-microservices-project
   ```

2. Build and start the Docker containers:
   ```
   docker-compose up -d --build
   ```

3. Install dependencies for both services:
   ```
   docker-compose exec users-service composer install
   docker-compose exec notifications-service composer install
   ```

4. Run migrations for the users-service:
   ```
   docker-compose exec users-service php artisan migrate
   ```

5. Start the event consumer for the notifications-service:
   ```
   docker-compose exec notifications-service php artisan consume:user-created-events
   ```

## Usage

To create a new user, send a POST request to `http://localhost:8000/api/users` with the following JSON body:

```json
{
  "email": "user@example.com",
  "firstName": "John",
  "lastName": "Doe"
}
```

The user will be created in the users-service, and an event will be sent to the notifications-service, which will log the user creation.

## Running Tests

To run tests for each service:

```
docker-compose exec users-service php artisan test
docker-compose exec notifications-service php artisan test
```

## Architecture

This project follows a microservices architecture with two services:

1. Users Service: Handles user creation and storage
2. Notifications Service: Consumes user creation events and logs them
3. Redis: Pub/Sub service for event communication

Communication between services is done via Redis pub/sub.

The project uses Laravel 11.x with PHP 8.3 and SQLite as the database.

## Run it manually

1. Run the docker compose `docker-compose up -d --build`
2. Run the migrations for users service  `cd users-service && docker-compose exec users-service php artisan migrate`
3. Run the event consumer for notifications service `cd notifications-service && docker-compose exec notifications-service php artisan consume:user-created-events`
3. Keep logs open for notification service `tail -f storage/log/*` 
4. Create a user via `curl -X POST http://localhost:8000/api/users -H "Content-Type: application/json" -d '{"email": "user@example.com", "firstName": "John", "lastName": "Doe"}'`
5. See the event in the log. Log should display something like 
```
[2024-09-24 22:53:11] local.INFO: User created:  {"email":"user@examplexxx.com","firstName":"John","lastName":"Doe"}```

