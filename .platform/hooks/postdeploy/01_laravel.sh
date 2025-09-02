#!/bin/bash
set -e
cd /var/app/current



php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force || true

chown -R webapp:webapp storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
