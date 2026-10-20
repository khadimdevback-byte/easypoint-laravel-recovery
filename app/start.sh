start.sh – nouveau contenu complet
#!/bin/bash
set -e
cd /app
# 1) Permissions : php-fpm exécute Laravel en tant que www-data,
# qui doit pouvoir écrire dans storage/ et bootstrap/cache/
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
# 2) Cache de config généré au démarrage, quand les variables
# Coolify (APP_KEY, DB_*...) sont disponibles
php artisan config:cache
php artisan view:cache
# 3) Démarrage des services (inchangé)
php-fpm --fpm-config /app/php-fpm.conf -D
exec nginx -c /app/nginx.conf -g 'daemon off;'
