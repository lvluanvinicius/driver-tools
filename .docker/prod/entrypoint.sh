#!/bin/bash

APP_URL=${APP_URL:-https://localhost}
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
DRIVER_STORAGE_PATH=${DRIVER_STORAGE_PATH:-/storage/cednet-applicacoes/driver-tool-dev}

# Altera o APP_URL diretamente no .env
sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env
sed -i "s|^DRIVER_STORAGE_PATH=.*|DRIVER_STORAGE_PATH=${DRIVER_STORAGE_PATH}|" .env

php artisan config:clear
php artisan config:cache
php artisan view:clear
php artisan route:clear

# Iniciar a aplicação
exec "$@"
