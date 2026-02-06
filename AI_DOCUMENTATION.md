# AI Co-Authorship Documentation

This document describes how this project was developed in collaboration with AI (e.g. Cursor, Copilot, or similar). It is intended for future maintainers and for setting up development agents with consistent behavior.

---

## Overview

The full-stack starter kit (Laravel backend, Next.js frontend, Docker, role-based access, tools catalog, 2FA) was built iteratively with AI assistance. Key areas where AI helped include:

- **Project structure and Docker** — Compose setup, ports, Laravel + Next.js wiring.
- **Authentication and 2FA** — Login flow, code generation, expiry, session handling.
- **Back-button guard** — Preventing sensitive pages from being shown from cache and handling browser Back with a logout confirmation.
- **Role-based access** — User roles (Owner, QA, etc.), admin vs owner middleware, tool recommendations.
- **Tools CRUD** — Models, controllers, validation, categories/tags/roles, approval workflow.
- **Cursor rules** — Persistent rules so the AI uses Git Bash and verifies fixes before claiming “fixed.”

---

## Setting Up a Development Agent

To get consistent, project-aware behavior from an AI coding assistant (e.g. Cursor), use **project rules** that apply to all conversations in this repo.

### Initial prompts / rules to add

1. **Terminal: Git Bash only**  
   - “When suggesting or writing terminal commands for this project, always use Git Bash (bash/sh) syntax, not PowerShell or CMD.”  
   - Use `&&` to chain, forward slashes, `export VAR=value`, `./start.sh`, etc.  
   - Implemented in this repo as: `.cursor/rules/git-bash-commands.mdc`

2. **Verify before saying fixed**  
   - “Before saying something is fixed, actually test the flow (e.g. run server, hit endpoint, submit form). Prefer: ‘I made these changes; I ran [verification]. You should see X when you do Y. If not, tell me what you see.’ Avoid claiming ‘It’s fixed’ without a verification step or clear instruction for the user to confirm.”  
   - Implemented as: `.cursor/rules/verify-before-fixed.mdc`

3. **Stack and ports**  
   - “Backend is Laravel on port 8201, frontend Next.js on 8200. API base: `http://localhost:8201/api`. Use these when suggesting URLs or env vars.”

4. **Auth and roles**  
   - “Users have a `role` (owner, backend, frontend, qa, designer, project_manager) and `is_admin`. Only users with `role === 'owner'` may manage users in admin. Admin routes use `auth`, `verified`, `2fa`, `admin`, and for user CRUD also `owner` middleware.”

These can be added as Cursor rules (`.cursor/rules/*.mdc`) or as an `AGENTS.md` / “Instructions for AI” section so the agent always sees them.

---

## Complex Tasks Solved With AI Help

### 1. Back-button guard

**Problem:** After logout (or when leaving sensitive pages), the browser Back button could show cached dashboard/2FA/profile content. That is confusing and a security/privacy concern.

**Approach (implemented with AI):**

1. **Server-side: no-cache headers**  
   Middleware `DisableBackCache` adds:
   - `Cache-Control: no-cache, no-store, max-age=0, must-revalidate`
   - `Pragma: no-cache`
   - `Expires` in the past  

   So the browser does not serve cached HTML for those responses. Applied to auth, 2FA, dashboard, profile, and admin routes via `disable.back.cache` in `routes/web.php` and `routes/auth.php`.

2. **Client-side: Back = “Leave?” confirmation**  
   For authenticated users only (`data-auth="1"` on `<body>`):
   - On load: `history.pushState(null, null, null)` so there is an extra history entry.
   - On `popstate` (Back/Forward): dispatch a custom event `back-button-prompt` and push state again so the user stays on the same page.
   - A modal (Alpine.js) listens for `back-button-prompt` and asks: “Do you want to leave your profile?” with **No** (dismiss and push state again) and **Yes** (submit logout form).
   - Result: Back does not reveal cached sensitive content; it either keeps the user on the page or logs them out after confirmation.

**Relevant files:**

- `backend/app/Http/Middleware/DisableBackCache.php` — no-cache headers.
- `backend/bootstrap/app.php` — register `disable.back.cache`.
- `backend/resources/views/layouts/app.blade.php` — `pushState` / `popstate` script and modal.

**Prompt-style summary for an AI:**  
“We need a back-button guard: (1) add middleware that sets no-cache headers on auth and app routes so Back doesn’t show cached pages; (2) for logged-in users, on Back button show a modal ‘Do you want to leave?’ and only logout if they confirm; use history.pushState/popstate and a custom event so Back doesn’t actually navigate to cached content.”

---

### 2. Two-factor authentication (2FA) logic

**Problem:** After email/password login, require a second step (e.g. 6-digit code sent by email) before allowing access to the dashboard, and avoid reuse or indefinite validity of the code.

**Approach (implemented with AI):**

1. **Login flow**  
   - User submits login form → `AuthenticatedSessionController::store` validates and logs in the user.
   - Generate a **new** 6-digit code and store it with an expiry (e.g. 15 minutes) in `users.two_factor_code` and `users.two_factor_expires_at`.
   - Send the code via `TwoFactorCodeNotification` (e.g. email).
   - Save session (including `url.intended` for redirect after 2FA) and redirect to `/verify-2fa`.

2. **Verification**  
   - `TwoFactorController::index` shows the verify-2fa form; `store` validates the code:
     - Must match `user->two_factor_code`.
     - Must not be expired (`two_factor_expires_at`).
   - On success: clear `two_factor_code` and `two_factor_expires_at` (one-time use), then `redirect()->intended(route('dashboard'))`.

3. **Middleware**  
   - **TwoFactorMiddleware (`2fa`):** If the user is logged in and `two_factor_code` is set, allow only `/verify-2fa`, `/verify-2fa/*`, and logout routes; otherwise redirect to `/verify-2fa`. Also: if user is inactive (`is_active === false`), logout and redirect to login with a message.
   - **RedirectIf2FAVerified:** On `/verify-2fa`, if the user is already verified (no pending `two_factor_code`), redirect to dashboard so they cannot “Back” into the verify page and reuse it.

4. **Logout**  
   - Normal logout and the inactivity logout route clear `two_factor_code` and `two_factor_expires_at` so the next login gets a fresh code.

**Relevant files:**

- `backend/app/Http/Controllers/Auth/AuthenticatedSessionController.php` — login, generate code, redirect to verify-2fa.
- `backend/app/Http/Controllers/Auth/TwoFactorController.php` — show form, verify code, clear code, redirect intended.
- `backend/app/Http/Middleware/TwoFactorMiddleware.php` — enforce 2FA gate and inactive-user logout.
- `backend/app/Http/Middleware/RedirectIf2FAVerified.php` — already-verified users leave verify-2fa.
- `backend/app/Notifications/TwoFactorCodeNotification.php` — send code (e.g. email).
- `backend/routes/auth.php` — verify-2fa and logout-inactive routes; middleware applied.

**Prompt-style summary for an AI:**  
“We want 2FA after login: (1) on login success generate a 6-digit code, save with 15-min expiry, send by email, redirect to verify-2fa; (2) only allow verify-2fa and logout until code is entered; (3) on correct code clear the code and redirect to intended URL; (4) middleware should block dashboard access until 2FA is done and redirect already-verified users away from verify-2fa; (5) logout (normal and inactivity) must clear the code.”

---

## Cursor Rules in This Repo

| Rule file                     | Purpose |
|------------------------------|--------|
| `.cursor/rules/git-bash-commands.mdc` | All terminal commands in Git Bash syntax; no PowerShell/CMD. |
| `.cursor/rules/verify-before-fixed.mdc` | Verify fixes (run command/test) before saying “fixed”; give user a clear check. |

These act as “initial prompts” that are always applied when the agent works in this project.

---

## Summary

- **README.md** covers installation (Docker), roles (Owner, QA, etc.), and how to add new tools.
- **AI_DOCUMENTATION.md** (this file) describes AI co-authorship: how the project was built with AI, how to set up a dev agent with prompts/rules, and the two complex areas — **Back-button guard** (no-cache + Back = logout confirmation) and **2FA logic** (code generation, verification, middleware, one-time use, expiry).

Use this document when onboarding new contributors or when configuring an AI assistant so it respects the same conventions and understands the reasoning behind these features.
