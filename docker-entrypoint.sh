#!/bin/bash

set -e

echo ""
echo "========================================="
echo " SweetManager - Inicializando..."
echo "========================================="
echo ""

if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i "s|^DB_HOST=.*|DB_HOST=mysql|" .env
sed -i "s|^DB_PORT=.*|DB_PORT=3306|" .env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=laravel|" .env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=root|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=root|" .env

if [ ! -f vendor/autoload.php ]; then
    echo "Instalando dependências Composer..."
    composer install --no-interaction
fi

if [ ! -d node_modules ]; then
    echo "Instalando dependências NPM..."
    npm install
fi

php artisan key:generate --force

echo "Aguardando MySQL..."

until php artisan db:show > /dev/null 2>&1
do
    sleep 3
done

echo "MySQL conectado."

php artisan migrate --force

php artisan storage:link || true

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "========================================="
echo " SweetManager iniciado"
echo "========================================="
echo ""
echo "Aplicação: http://localhost:8000"
echo "phpMyAdmin: http://localhost:8080"
echo ""

exec php artisan serve --host=0.0.0.0 --port=8000