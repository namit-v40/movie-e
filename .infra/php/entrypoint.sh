#!/bin/sh

# Chown file when mount volume
chown -R www:www /app/bootstrap/cache
chown -R www:www /app/storage/framework
chown -R www:www /app/storage/logs

# Install vendor
composer install

# Run first Migrate
php artisan migrate

# Run docker command
exec "$@"
