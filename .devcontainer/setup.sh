#!/bin/bash

# エラーが発生したら即座にスクリプトを終了
set -eux

cd /var/www/html/src

echo "📦 Composer install running..."
composer install

php artisan key:generate

php artisan migrate

php artisan db:seed

echo "End SETUP"
