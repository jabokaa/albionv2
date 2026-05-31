#!/bin/bash
set -e

cd /var/www

echo "→ Instalando dependências PHP..."
composer install --optimize-autoloader --no-interaction 2>&1

echo "→ Instalando dependências Node..."
npm install 2>&1

echo "→ Buildando assets (Vite)..."
npm run build 2>&1

if [ -z "$(grep '^APP_KEY=.\+' .env 2>/dev/null)" ]; then
    echo "→ Gerando APP_KEY..."
    php artisan key:generate --force
fi

echo "→ Aguardando banco de dados..."
until php artisan db:show --json > /dev/null 2>&1; do
    sleep 2
done

echo "→ Rodando migrations..."
php artisan migrate --force

echo "→ Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache

echo "✓ Ambiente pronto!"

exec "$@"
