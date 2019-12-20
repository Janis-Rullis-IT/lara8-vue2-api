#!/bin/sh

set -e
service php7.2-fpm start
composer install
chmod a+w storage -R

#php artisan config:cache --env=testing
php artisan migrate --env=testing

#php artisan config:cache
php artisan migrate

chmod a+w storage -R

service nginx start && tail -F /var/log/nginx/error.log
