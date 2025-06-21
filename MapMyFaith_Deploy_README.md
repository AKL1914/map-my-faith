# 🚀 Map My Faith — Full Deployment Guide

Your complete step-by-step handbook for deploying Laravel + Docker + Horizon + Redis + CI/CD on Hetzner Cloud.

---

## 📌 1️⃣ Hetzner VPS Setup

✅ **Create a server:**
- Small plan: `CX22` or `CPX11` (2 CPU, 2–4 GB RAM)
- Region: Helsinki or closest to NZ
- OS: Ubuntu 22.04 LTS or 24.04 LTS
- Add your SSH public key during setup (no root password needed).

✅ **Login to server:**

```bash
ssh root@YOUR_SERVER_IP
```

... (truncated for brevity; full text in previous message)

## 🚀 **You rock — ship with confidence!**

📌 2️⃣ Install Docker & Docker Compose

sudo apt update && sudo apt upgrade -y
sudo apt install docker.io docker-compose -y
sudo systemctl enable docker --now

📌 3️⃣ Clone the Repository

mkdir -p ~/apps
cd ~/apps
git clone https://github.com/YOUR_USERNAME/map-my-faith.git

📌 4️⃣ Build & Start Services
docker compose -f docker-compose.yml -f docker-compose.prod.yml build
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

📌 5️⃣ Set Up HTTPS

Install Certbot:
sudo apt install certbot python3-certbot-nginx -y

Stop Docker NGINX container:
docker compose stop nginx

Issue SSL certificate (replace with your domain):
sudo certbot certonly --standalone -d mapmyfaith.online -d www.mapmyfaith.online
Mount /etc/letsencrypt in your NGINX Docker config and update default.prod.conf for SSL.

Restart:
docker compose up -d

📌 6️⃣ Supervisor + Horizon Setup

✅ docker/php/supervisord.prod.conf:

[supervisord]
nodaemon=true

[program:php-fpm]
command=/usr/local/sbin/php-fpm
autostart=true
autorestart=true

[program:laravel-horizon]
command=php /var/www/html/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/horizon.log

Check status:
docker compose exec app supervisorctl status
docker compose exec app php artisan horizon:supervisors

📌 7️⃣ Verify Redis

Inside your app container:
docker compose exec app php artisan tinker

Then in Tinker:
Cache::put('foo', 'bar', 10);
Cache::get('foo'); // Should return "bar"

Redis::connection()->ping(); // Should return "PONG"
Or check live keys:
docker exec -it map_my_faith_app_redis redis-cli
keys *

📌 8️⃣ Import Database

Upload your SQL dump:
scp backup.sql root@YOUR_SERVER_IP:/root/

Import:

docker exec -i map_my_faith_app_mysql mysql -u laravel_app_user -p laravel < /root/backup.sql

📌 9️⃣ CI/CD with Tests & Deploy

Create .github/workflows/deploy.yml:

name: 🚀 Test & Deploy

on:
  push:
    branches:
      - develop

jobs:
  test-and-deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, bcmath, redis, pdo_mysql

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction

      - name: Copy .env.example to .env.testing
        run: cp .env.example .env.testing

      - name: Generate APP_KEY
        run: php artisan key:generate --env=testing

      - name: Run tests
        run: php artisan test --env=testing

      - name: Setup SSH agent
        uses: webfactory/ssh-agent@v0.8.0
        with:
          ssh-private-key: ${{ secrets.SSH_PRIVATE_KEY }}

      - name: Deploy to Hetzner
        run: |
          ssh -o StrictHostKeyChecking=no ${{ secrets.SSH_USER }}@${{ secrets.SSH_HOST }} "
            cd ~/apps/map-my-faith && \
            git pull origin develop && \
            docker compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache && \
            docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
          "


Required Secrets:
	•	SSH_PRIVATE_KEY
	•	SSH_USER
	•	SSH_HOST

📌 10️⃣ Add DevOps Access

They generate a key:

ssh-keygen -t ed25519 -C "devops"

You add it:
nano ~/.ssh/authorized_keys

Paste their .pub key on a new line.

✅ They can now SSH in securely!

📌 11️⃣ Handy Admin Commands

Task
Command
Check Supervisor
docker compose exec app supervisorctl status
Restart Supervisor
docker compose exec app supervisorctl restart all
Rebuild Containers
docker compose build
Up Containers
docker compose up -d
View Logs
docker compose logs -f
DB Shell
docker exec -it map_my_faith_app_mysql mysql -u laravel_app_user -p laravel
Redis CLI
docker exec -it map_my_faith_app_redis redis-cli
