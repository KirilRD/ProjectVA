# AI Tools Directory  
# Каталог AI инструменти

**Language / Език:** [English](#english) | [Български](#български)

---

<a name="български"></a>

## Български

### Име на проекта

**AI Tools Directory** (Каталог с AI инструменти) — уеб приложение за преглед, добавяне и одобрение на инструменти свързани с изкуствен интелект, с ролева система, коментари и интеграция с Docker.

### Възможности

- **Роли:** Owner, Admin, User (Backend, Frontend, QA, Designer, Project manager). Owner има пълен контрол и управление на потребители; Admin одобрява инструменти; потребителите преглеждат каталога и добавят инструменти.
- **Система за одобрение на инструменти:** Новите инструменти са със статус *pending* и се одобряват от Admin/Owner в админ таблото.
- **Коментари и оценки:** Потребителите могат да оставят ревюта и рейтинг (1–5) за одобрени инструменти.
- **Docker интеграция:** Приложението се стартира с Docker Compose (Laravel, Next.js, MySQL, Redis).
- **Сигурност No-Back-Cache:** Middleware предотвратява показване на кеширани чувствителни страници при натискане на бутона „Назад“ след изход; 2FA с еднократен код по имейл.

### Инсталация и стартиране

1. **Клониране и влизане в проекта:**
   ```bash
   cd full-stack-starter-kit
   ```

2. **Стартиране на услугите с Docker:**
   ```bash
   docker compose up -d
   ```
   Изчакайте около 15–20 секунди да стартират контейнерите.

3. **Първоначална настройка (ако още не е направена):**
   ```bash
   docker compose exec php_fpm php artisan key:generate --force
   docker compose exec php_fpm composer install --no-interaction
   docker compose exec php_fpm php artisan migrate:fresh --seed
   ```

4. **Достъп:**
   - **Бекенд (Laravel):** http://localhost:8201  
   - **Фронтенд (Next.js):** http://localhost:8200  

### Тестови потребители (след `migrate:fresh --seed`)

| Роля   | Имейл              | Парола     |
|--------|--------------------|------------|
| **Owner**  | `kiril@admin.local`  | `password123` |
| **Admin**  | `admin@admin.local`  | `password123` |
| Обикновен потребител | `ivan@backend.local` | `password123` |

При проблем с вход изпълнете отново:
```bash
docker compose exec php_fpm php artisan migrate:fresh --seed
```

### Технологичен стек

| Слой       | Технология                    | Порт |
|------------|--------------------------------|------|
| Бекенд     | Laravel 12, PHP 8.2, Nginx     | 8201 |
| Фронтенд   | Next.js 18, React, TypeScript | 8200 |
| База данни | MySQL 8.0                     | 8203 |
| Кеш        | Redis 7                       | 8204 |

### Сигурност

- **2FA:** След логин се изисква 6-цифрен код (изпратен по имейл), еднократен, с 15 мин. валидност.
- **No-Back-Cache:** За чувствителни страници се подават no-cache заглавия; при Back за логнат потребител се показва потвърждение за изход.

### Поздрави в интерфейса

В хедъра за логнат потребител се показва поздрава на български:  
*„Добре дошъл, [име]! Ти си с роля: [роля].“*

---

<a name="english"></a>

## English

### Project Name

**AI Tools Directory** — Web application for browsing, submitting, and approving AI-related tools, with role-based access, comments/ratings, and Docker integration.

### Features

- **Roles:** Owner, Admin, User (Backend, Frontend, QA, Designer, Project manager). Owner has full control and user management; Admin approves tools; users browse the catalog and submit tools.
- **Tool approval workflow:** New tools are *pending* until approved by Admin/Owner from the admin dashboard.
- **Comments and ratings:** Users can leave reviews and 1–5 star ratings on approved tools.
- **Docker integration:** Run the stack with Docker Compose (Laravel, Next.js, MySQL, Redis).
- **No-Back-Cache security:** Middleware prevents cached sensitive pages when using the Back button after logout; 2FA with one-time email code.

### Setup

1. Clone and enter the project: `cd full-stack-starter-kit`
2. Start services: `docker compose up -d`
3. First-time setup:  
   `docker compose exec php_fpm php artisan key:generate --force`  
   `docker compose exec php_fpm composer install --no-interaction`  
   `docker compose exec php_fpm php artisan migrate:fresh --seed`
4. Open backend: http://localhost:8201 — frontend: http://localhost:8200

### Test credentials (after seeding)

| Role   | Email               | Password     |
|--------|---------------------|--------------|
| **Owner** | `kiril@admin.local` | `password123` |
| **Admin** | `admin@admin.local` | `password123` |

If login fails, run:  
`docker compose exec php_fpm php artisan migrate:fresh --seed`

---

For AI-assisted development and agent setup, see `AI_DOCUMENTATION.md` and `AI_AGENTS.md`.
