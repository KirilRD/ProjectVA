# Install PHP and npm (Node.js) on Windows

Use **PowerShell or CMD as Administrator**, or run these in Git Bash if `winget` is in your PATH.

## 1. Install Node.js (includes npm)

```bash
winget install OpenJS.NodeJS.LTS --accept-package-agreements
```

After install, **close and reopen** your terminal (or Git Bash) so `node` and `npm` are in PATH.

Verify:

```bash
node -v
npm -v
```

## 2. Install PHP

```bash
winget install PHP.PHP.8.2 --accept-package-agreements
```

Or PHP 8.4: `winget install PHP.PHP.8.4 --accept-package-agreements`

**Close and reopen** your terminal, then verify:

```bash
php -v
```

## 3. Optional: Install both in one go

```bash
winget install OpenJS.NodeJS.LTS PHP.PHP.8.2 --accept-package-agreements
```

Then restart your terminal and run `node -v`, `npm -v`, `php -v`.
