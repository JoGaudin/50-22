#!/bin/sh
set -e

: "${PORT:=8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec supervisord -n -c /etc/supervisor/supervisord.conf
