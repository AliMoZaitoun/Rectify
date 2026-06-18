#!/bin/sh
set -e

echo "=== Fixing permissions ==="
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Ensure log file exists
mkdir -p /var/www/storage/logs
touch /var/www/storage/logs/laravel.log
chown www-data:www-data /var/www/storage/logs/laravel.log
chmod 664 /var/www/storage/logs/laravel.log

# Wait for database
DB_HOST=${DB_HOST:-rectify-db}
DB_PORT=${DB_PORT:-5432}
echo "Checking connection to $DB_HOST on port $DB_PORT..."

until nc -z "$DB_HOST" "$DB_PORT"; do
    echo "Waiting for Database..."
    sleep 2
done

echo "Database is up - executing commands"

# Laravel cache clear + rebuild
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force || echo "Migrations skipped (already up to date or error)"

echo "Starting PHP-FPM..."
exec php-fpm