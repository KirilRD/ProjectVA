# AI Agents Documentation

This file is dedicated to **AI agent requirements** for this project. Use it when configuring an AI coding assistant (e.g. Cursor, Copilot) or when onboarding a development agent so it behaves consistently with the project’s conventions and logic.

---

## Purpose

- Define how an AI agent should interact with this codebase.
- Provide **Initial Prompts** (and equivalent rules) for starting a development agent.
- Ensure terminal commands, verification habits, stack knowledge, and auth/role logic are applied consistently.

---

## Development Process: How This Project Was Built

This section describes how AI was used in the development process. Use it to understand the division of responsibilities and the reasoning behind key implementations.

### Human–AI Pair Programming Model

This project was built using a **Human–AI Pair Programming** model:

- **The Human acted as the Architect:** defining security requirements, acceptance criteria, and the overall behavior (e.g. “Back must not show cached sensitive pages,” “2FA must be one-time and time-limited”).
- **The AI acted as the Implementation Specialist:** translating those requirements into code—middleware, controllers, JavaScript, routes—and iterating until the behavior matched the architect’s intent.

Requirements and edge cases were specified by the human; implementation details, patterns, and file-level changes were produced and refined with the AI. Future developers using AI to maintain the project should provide the **Initial Development Prompts** below to establish the correct context.

### Docker Configuration

AI was used to **orchestrate the Docker setup** for this project: PHP 8.2, MySQL, and Nginx containers configured **specifically for Laravel 12**. The AI helped define `docker-compose.yml`, the PHP Dockerfile, Nginx config, ports (8201 backend, 8203 MySQL, etc.), environment variables, and startup scripts so that Laravel 12 runs correctly inside the stack.

### Complex Security Logic: Back-Button Navigation and No-Cache

AI helped solve the **Back-button navigation** and **No-Cache** security issue through a **combination of server-side Middleware and client-side JavaScript “traps”:**

- **Middleware:** `DisableBackCache` adds no-cache headers (`Cache-Control`, `Pragma`, `Expires`) to auth, 2FA, dashboard, profile, and admin responses so the browser does not serve cached HTML when the user presses Back.
- **JavaScript “traps”:** For authenticated users, `history.pushState` and a `popstate` listener ensure that when the user presses Back, they do not navigate to cached content; instead, a custom event opens a confirmation modal (“Do you want to leave your profile?”). Only on “Yes” is logout submitted; on “No” the user stays on the page and state is pushed again.

Together, Middleware and the client-side logic close the gap where sensitive pages could otherwise reappear from cache after logout.

### Prompt Engineering for 2FA

Prompts for the **Two-Factor Authentication** flow were **structured to enforce data integrity and logical flow:**

- **Integrity:** One-time codes, expiry (e.g. 15 minutes), and clearing the code on success and on logout so codes cannot be reused or left valid indefinitely.
- **Logical flow:** Login → generate code → send notification → redirect to verify-2fa → block dashboard until verification → on success clear code and redirect to intended URL; middleware to block access when a code is pending and to redirect already-verified users away from the verify page.

By spelling out these constraints in prompts, the AI produced a consistent 2FA implementation (controllers, middleware, notifications, routes) that preserves integrity and avoids logical gaps (e.g. Back-button reuse of the verify page).

---

## 🤖 Initial Development Prompts

To continue development, provide the following prompts to your AI agent to establish the correct context. These are required for future developers who will use AI to maintain the project.

---

### Prompt 1: Project Architecture Context

> You are a Senior Laravel Developer. This is a Tool Management System built with Laravel 12, PHP 8.2, and Docker. The tech stack includes MySQL and Tailwind CSS. Key features implemented: 2FA Authentication, Role-Based Access Control (RBAC), and a custom Review system. Familiarize yourself with `routes/web.php` and the custom Middlewares in `app/Http/Middleware`.

---

### Prompt 2: Security & Session Rules

> Important: The project uses a custom "No-Cache" Middleware and a JavaScript-based navigation guard to prevent unauthorized back-button access after logout. When adding new features, always ensure that the user has passed the 2FA check (auth / 2FA middleware) and that session security is maintained.

---

### Prompt 3: Business Logic for Reviews

> The review system has strict constraints: 1) One review per user per tool. 2) Tool owners cannot rate their own tools. Always respect these constraints in the Controller logic and UI rendering.

---

### Prompt 4: Coding Standards

> Follow PSR-12 coding standards, use type-hinting, and ensure all UI elements are consistent with the existing Tailwind CSS design. Keep the Dockerized environment in mind for any file system or networking tasks.

---

## Initial Prompts for Starting a Development Agent

When setting up a development agent for this project, apply the following prompts (or equivalent Cursor rules / AGENTS.md content) so the agent always follows them.

---

### 1. Terminal: Git Bash only

**Prompt:**

> When suggesting or writing terminal commands for this project, **always use Git Bash (bash/sh) syntax**, not PowerShell or CMD.
>
> - Use `&&` to chain commands: `cd backend && php artisan serve`
> - Use forward slashes in paths: `./start.sh`, `backend/.env`
> - Use `export VAR=value` for environment variables
> - Use `./script.sh` to run scripts
> - Use Unix-style: `npm run dev`, `docker compose up -d`
>
> Do **not** use PowerShell (`Set-Location`, `;`, `.\script.ps1`), CMD (`cd /d`), or PowerShell env (`$env:VAR = "value"`). When the user runs commands in their terminal, assume they use **Git Bash**.

**In this repo:** Implemented as `.cursor/rules/git-bash-commands.mdc`.

---

### 2. Verify before saying “fixed”

**Prompt:**

> Before telling the user that something is fixed or that "everything is fixed":
>
> 1. **Actually test the flow** when possible: run the relevant commands (e.g. start server, hit the endpoint, submit the form). Use curl, tests, or a quick manual check to confirm the behavior.
> 2. **Do not claim "it's fixed"** unless you have either run a verification step (e.g. curl GET/POST and checked status + content) or explicitly stated that the user should verify (e.g. "Try X and confirm; if not, we can debug further").
> 3. **Prefer:** "I've made these changes. I ran [brief verification]. You should see X when you do Y. If not, tell me what you see."
> 4. **Avoid:** "Everything is fixed" or "It's fixed" without any verification step or clear instruction for the user to confirm.
>
> When the codebase allows (backend running, routes known), run a minimal check (e.g. one curl or one test) before saying the fix is done.

**In this repo:** Implemented as `.cursor/rules/verify-before-fixed.mdc`.

---

### 3. Stack and ports

**Prompt:**

> This project uses:
> - **Backend:** Laravel 12 on port **8201** (Nginx + PHP-FPM in Docker).
> - **Frontend:** Next.js on port **8200**.
> - **API base URL:** `http://localhost:8201/api`.
> - **Database:** MySQL 8.0 (port 8203); **Cache:** Redis 7 (port 8204).
>
> When suggesting URLs, env vars, or API calls, use these ports and the API base URL above.

---

### 4. Auth and roles

**Prompt:**

> Users have a **role** (owner, backend, frontend, qa, designer, project_manager) and **is_admin**.
> - Only users with **role === 'owner'** may manage users in admin (create, edit, toggle active, delete). Admin user-management routes use the **owner** middleware.
> - **Admin** (`is_admin = true`) can access `/admin` (dashboard, toggle tool status, approve/reject tools).
> - All protected app routes use **auth**, **verified**, **2fa**, and **disable.back.cache** middleware.
>
> When changing auth or admin routes, preserve these middleware and role checks.

---

### 5. Docker and scripts

**Prompt:**

> To start the environment use **Git Bash** and run `./start.sh`, or manually: `docker compose build php_fpm` then `docker compose up -d`. Laravel commands run inside the PHP container: `docker compose exec php_fpm php artisan <command>`. Prefer suggesting these forms over host-installed PHP when the project is run via Docker.

---

## Summary: What to Give Your Agent

| Area           | Initial prompt / rule |
|----------------|------------------------|
| **Terminal**   | Git Bash only; no PowerShell/CMD. |
| **Verification** | Verify (run command/test) before saying “fixed”; give user a clear check step. |
| **Stack**      | Laravel 12 @ 8201, Next.js @ 8200, API base `http://localhost:8201/api`, MySQL, Redis. |
| **Auth/Roles** | role + is_admin; only owner manages users; auth/verified/2fa/disable.back.cache on app routes. |
| **Docker**     | Use `./start.sh` or `docker compose`; Artisan via `docker compose exec php_fpm`. |

---

## Where These Are Implemented

- **Cursor rules:** `.cursor/rules/git-bash-commands.mdc`, `.cursor/rules/verify-before-fixed.mdc`
- **Extended AI context:** `AI_DOCUMENTATION.md` (Back-button guard, 2FA logic, complex tasks)
- **User-facing docs:** `README.md` (English), `README_BG.md` (Bulgarian, incl. AI Агенти и разработка)

Use **AI_AGENTS.md** (this file) as the single reference for **Initial Prompts** when starting or configuring a development agent for this project.
