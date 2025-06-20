
# Campaign Management Application

This project is a Vue.js front-end integrated with a Laravel back-end. It allows users to manage campaigns and display pin markers on a map based on campaign selection. The application uses Docker for containerization and Laravel for back-end API services.

## Prerequisites

Before running this project, ensure you have the following tools installed:

- Docker
- Docker Compose
- Node.js (for development or if you're building the front-end locally)
- Composer (for Laravel dependencies)
- PHP (for Laravel if running locally)

## Getting Started

Follow these steps to set up the project in Docker.

### 1. Clone the repository
Clone the project repository to your local machine:

```bash
git clone <repository-url>
cd <repository-directory>
```

### 2. Build and run Docker containers
Use Docker Compose to build and run the containers for the Laravel back-end and Vue.js front-end.

```bash
docker-compose up --build
```

This will:
- Build the necessary images.
- Start the containers as defined in `docker-compose.yml`.

You can access the app by navigating to `http://localhost:8000` for the Laravel back-end and `http://localhost:8080` for the Vue.js front-end (if these ports are mapped in `docker-compose.yml`).

### 3. Docker ps (Checking running containers)
To verify that the Docker containers are running, use the following command:

To run the Laravel migrations, enter the `laravel` container and run the migration command:

```bash
docker exec -it <laravel-container-name> bash
php artisan migrate
```

This will apply the database migrations.

### 5. Running Seeders
If you have database seeders set up and want to populate the database with sample data, run the following command after running migrations:

```bash
php artisan db:seed
```

### 6. Access the Application
After the containers are up and the database has been migrated and seeded, you can access the app by visiting:

- **Back-end API**: `http://localhost:8080`
- **Front-end Vue.js app**: `http://localhost:8080`

## Docker Commands

- **Check Running Containers**:
  ```bash
  docker ps
  ```

- **Stop the containers**:
  ```bash
  docker-compose down
  ```

- **Rebuild and Restart**:
  ```bash
  docker-compose up --build
  ```

- **Run Artisan Commands in Docker**:
  ```bash
  docker exec -it <laravel-container-name> php artisan <command>
  ```

Example:

```bash
docker exec -it <laravel-container-name> php artisan migrate
docker exec -it <laravel-container-name> php artisan db:seed
```

### 7. Development Environment

If you are developing locally, you can run the back-end and front-end separately without Docker:

#### Front-End (Vue.js)

```bash
cd vue-app
npm install
npm run serve
```

The front-end will be accessible at `http://localhost:8080`.

#### Back-End (Laravel)

```bash
cd laravel-app
composer install
php artisan serve
```

The back-end will be accessible at `http://localhost:8080`.

## building/rebuilding the container
## this build the necessary dependencies and installs the packages

docker compose down
docker compose up --build

## Queue
When runnig it for the first time supervisord may fail
it needs to have the tables on the db
run the following command to create the tables

php artisan migrate
php artisan db:seed

## Check if the queue is running
docker exec -it map_my_faith_app bash
supervisorctl status

## Queue Worker log

tail -f /var/www/html/storage/logs/laravel-queue.log

## Restarting the container
docker compose down
docker compose up -d


## Get in the container for the app setup
docker exec -it map_my_faith_app  bash
php artisan migrate
php artisan db:seed

## Environment Variables
ask for the .env file from the developer

## Running the Vue.js Front-End Locally
npm install
npm run build
npm run dev 


🔐 How to Use This in a Vue SPA (Summary)
POST /api/v1/auth/google
→ Get url from JSON.

Redirect to that URL (use window.location.href = url).

After Google login, user is redirected back to your callback URL (e.g., /api/v1/auth/google/callback).

That response gives { token, user }.

Store the token in localStorage or cookie.

Attach token to API requests:

axios.defaults.headers.common['Authorization'] = `Bearer ${token}`


## License

This project is open source and available under the [MIT License](LICENSE).
Test deploy trigger
test deploy 🚀
