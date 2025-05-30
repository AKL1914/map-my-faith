
# Campaign Management Application

This project is a Vue.js front-end integrated with a Laravel back-end. It allows users to manage campaigns and display pin markers on a map based on campaign selection. The application uses Docker for containerization and Laravel for back-end API services.

## DEPLOYMENT BRANCH

This branch builds the artifact/docker image that would be deployed in Azure Container Apps.

The steps required for this are:
  1. developers update the laravel-app folder. all of these changes need to be synced and copied in the ./docker/php        folder, as part of building the docker image requires all of these files in the same php directory
  2. update the environment variables in azure container apps
  3. run docker-compose up --build. 
     IMPORTANT! Azure Container Apps only supports docker images built for amd64 platforms. run this command first before building:

      export DOCKER_DEFAULT_PLATFORM=linux/amd64
  4. once the image is built, tag the image as bnsebastianiii/mapmyfaithmvp:latest and upload it to docker hub
  5. once the image is uploaded to docker hub, go to Azure container apps and create a new revision. This would automatically pull the latest version tagged in the earlier step

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
