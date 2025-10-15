#!/usr/bin/env bash
set -e

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
    'api' => [
        'ingesta_key' => getenv('API_INGESTA_KEY') ?: 'CAMBIAR_ESTA_CLAVE',
    ],
];
PHP

exec apache2-foreground


