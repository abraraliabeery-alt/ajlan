#!/usr/bin/env bash
set -euo pipefail

command -v php >/dev/null || { echo "PHP 8.2 or newer is required."; exit 1; }
command -v composer >/dev/null || { echo "Composer 2 is required."; exit 1; }

test -f .env || cp .env.example .env
test -f database/database.sqlite || touch database/database.sqlite

composer install
php artisan key:generate
php artisan migrate --seed --force

echo "Project ready. Starting at http://127.0.0.1:8000"
php artisan serve
