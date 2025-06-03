#!/bin/bash

# App Environments.
APP_URL=${APP_URL:-https://localhost}
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_TIMEZONE=${APP_TIMEZONE:-UTC}
DRIVER_STORAGE_PATH=${DRIVER_STORAGE_PATH:-/data/driver-tool}

# PHP Environments
PHP_MAX_EXECUTION_TIME=${PHP_MAX_EXECUTION_TIME:-30}
PHP_MAX_INPUT_TIME=${PHP_MAX_INPUT_TIME:-60}
PHP_POST_MAX_SIZE=${PHP_POST_MAX_SIZE:-2G}
PHP_UPLOAD_MAX_FILESIZE=${PHP_UPLOAD_MAX_FILESIZE:-2G}
PHP_MAX_FILE_UPLOADS=${PHP_MAX_FILE_UPLOADS:-20}
PHP_MEMORY_LIMIT=${PHP_MEMORY_LIMIT:--1}

# Use ambos para CLI e FPM
for target in /etc/php/8.4/cli/conf.d/99-custom.ini /etc/php/8.4/fpm/conf.d/99-custom.ini; do
    echo "max_execution_time = ${PHP_MAX_EXECUTION_TIME}" > $target
    echo "max_input_time = ${PHP_MAX_INPUT_TIME}" >> $target
    echo "post_max_size = ${PHP_POST_MAX_SIZE}" >> $target
    echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}" >> $target
    echo "max_file_uploads = ${PHP_MAX_FILE_UPLOADS}" >> $target
    echo "memory_limit = ${PHP_MEMORY_LIMIT}" >> $target
done


# Altera o APP_URL diretamente no .env
sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
sed -i "s|^APP_NAME=.*|APP_NAME=${APP_NAME}|" .env
sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env
sed -i "s|^APP_TIMEZONE=.*|APP_TIMEZONE=${APP_TIMEZONE}|" .env
sed -i "s|^DRIVER_STORAGE_PATH=.*|DRIVER_STORAGE_PATH=${DRIVER_STORAGE_PATH}|" .env

php artisan config:clear
php artisan config:cache
php artisan view:clear
php artisan route:clear

# Iniciar a aplicação
exec "$@"
