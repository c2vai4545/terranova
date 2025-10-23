#!/usr/bin/env bash
set -e

# Configurar Apache para el puerto dinámico de la plataforma (Koyeb usa PORT=8000)
PORT="${PORT:-8000}"
echo "Configurando Apache para escuchar en puerto ${PORT}"

# ServerName para suprimir warning AH00558
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# Ajustar Listen y VirtualHost al puerto
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf || true
sed -i "s#<VirtualHost \*:[0-9]\+>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf || true

# Construye app/Config/config.local.php desde variables de entorno
cat > /var/www/html/app/Config/config.local.php <<'PHP'
<?php
date_default_timezone_set(getenv('TZ') ?: 'America/Montevideo');

return [
    'db' => [
        'host' => getenv('DB_HOST'),
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pass' => getenv('DB_PASS'),
        'charset' => 'utf8mb4',
    ],
];
PHP

exec apache2-foreground


