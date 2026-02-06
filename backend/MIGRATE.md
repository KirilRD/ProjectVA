# Fix "could not find driver" (SQLite)

The error means PHP’s **PDO SQLite** extension is not enabled. Enable it in your **system** `php.ini`, then run migrations.

## 1. Find your php.ini

```bash
php --ini
```

Use the path shown under "Loaded Configuration File".

## 2. Enable SQLite in php.ini

Open that file and add or uncomment (remove the leading `;`):

```ini
extension=pdo_sqlite
extension=sqlite3
```

On Windows the line might be:

```ini
extension=php_pdo_sqlite.dll
extension=php_sqlite3.dll
```

Save the file.

## 3. Run migrations

From the **backend** directory:

```bash
composer run migrate-fresh
```

Or:

```bash
php artisan migrate:fresh --seed
```

## Alternative: use Docker

If you use Docker, run migrations inside the container (backend uses MySQL there):

```bash
docker compose exec php_fpm php artisan migrate:fresh --seed
```

No SQLite extension is needed on your host.
