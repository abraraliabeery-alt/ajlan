@echo off
setlocal

where php >nul 2>&1 || (echo PHP 8.2 or newer is required.& exit /b 1)
where composer >nul 2>&1 || (echo Composer 2 is required.& exit /b 1)

if not exist .env copy .env.example .env >nul
if not exist database\database.sqlite type nul > database\database.sqlite

call composer install
if errorlevel 1 exit /b 1

php artisan key:generate
if errorlevel 1 exit /b 1

php artisan migrate --seed --force
if errorlevel 1 exit /b 1

echo.
echo Project ready. Starting at http://127.0.0.1:8000
php artisan serve
