<?php
date_default_timezone_set('America/Punta_Arenas');

return [
    'db' => [
        'host' => 'DIRECCION DEL HOST QUITADO POR SEGURIDAD',
        'name' => 'NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD',
        'user' => 'USUARIO DE HOST QUITADO POR SEGURIDAD',
        'pass' => 'CONTRASENA DE HOST QUITADO POR SEGURIDAD',
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        // Cambiar en producción y mover a config.local.php
        'secret' => 'CAMBIAR_ESTA_CLAVE_JWT',
    ],
];
