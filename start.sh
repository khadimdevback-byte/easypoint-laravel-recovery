#!/bin/bash
php-fpm --fpm-config /app/php-fpm.conf -D
nginx -c /app/nginx.conf -g 'daemon off;'
