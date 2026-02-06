# Docker Setup (README stack)

Stack: Laravel (Backend), Next.js (Frontend), MySQL 8.0, Redis 7, Mailpit.

## Restart the whole stack

```bash
docker compose down && docker compose up -d
```

Wait ~15–20 seconds, then run first-time setup or migrations (see below).

## First-time setup (after fresh start)

Run inside the PHP container to create DB and seed test users:

```bash
docker compose exec php_fpm php artisan key:generate --force
docker compose exec php_fpm composer install --no-interaction
docker compose exec php_fpm php artisan migrate:fresh --seed
```

## Services (docker-compose.yml)

| Service   | Role              | Port(s)        |
|-----------|-------------------|----------------|
| frontend  | Next.js           | 8200 → 3000    |
| backend   | Nginx (proxies to php_fpm) | 8201 → 80  |
| php_fpm   | Laravel (PHP)     | 8202 → 9000    |
| mysql     | MySQL 8.0         | 8203 → 3306    |
| redis     | Redis 7           | 8204 → 6379    |
| mailpit   | SMTP + Web UI     | 8025 (UI), 1025 (SMTP) |
| tools     | Dev utilities     | 8205           |

## Run migrations (inside Docker)

Artisan runs in the **php_fpm** container (not `backend`; `backend` is Nginx only). After first start or after restart, run:

```bash
docker compose exec php_fpm php artisan migrate:fresh --seed
```

This creates all tables and seeds test users (e.g. kiril@admin.local / password123).

Other useful commands:

```bash
docker compose exec php_fpm php artisan key:generate --force
docker compose exec php_fpm composer install --no-interaction
docker compose exec php_fpm php artisan config:clear
```

## Mailpit

- **Web UI:** http://localhost:8025  
- **SMTP:** host `mailpit`, port `1025` (inside Docker network).  
- In Docker, `php_fpm` is configured with `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025` so 2FA and other mail go to Mailpit.

## Ports (README)

- **Frontend:** http://localhost:8200  
- **Backend (Laravel):** http://localhost:8201  
- **Mailpit Web UI:** http://localhost:8025  

## Database (Docker)

Inside Docker, the **php_fpm** container gets DB from docker-compose env: `DB_CONNECTION=mysql`, `DB_HOST=mysql`, `DB_PORT=3306`, `DB_DATABASE=vibecode-full-stack-starter-kit_app`. The `backend/.env` is overridden by these when running in the container.
