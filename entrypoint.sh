#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database (if empty)..."
php artisan db:seed --force

exec "$@"
