# Start full-stack development environment (Windows)
# Requires: Docker Desktop running

Write-Host "Starting full-stack development environment..." -ForegroundColor Cyan

# Check if Docker is running
try {
    docker ps 2>$null | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "Docker not running" }
} catch {
    Write-Host "Docker is not running. Please start Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Start services
Write-Host "Starting Docker Compose..." -ForegroundColor Yellow
docker compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to start containers." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Development environment started." -ForegroundColor Green
Write-Host ""
Write-Host "Use these URLs (with port numbers):" -ForegroundColor White
Write-Host "  Backend (Laravel):  http://localhost:8201" -ForegroundColor Cyan
Write-Host "  Login page:         http://localhost:8201/login" -ForegroundColor Cyan
Write-Host "  Frontend (Next.js): http://localhost:8200" -ForegroundColor Cyan
Write-Host ""
Write-Host "If you see 'connection refused', wait 30 seconds for containers to start, then try again." -ForegroundColor Yellow
Write-Host "Check status: docker compose ps" -ForegroundColor Gray
