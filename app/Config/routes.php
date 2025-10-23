<?php
return [
    'GET /' => [AuthController::class, 'landing'],
    'GET /login' => [AuthController::class, 'showLogin'],
    'POST /login' => [AuthController::class, 'login'],
    'GET /logout' => [AuthController::class, 'logout'],

    'GET /admin' => [DashboardController::class, 'admin'],
    'GET /worker' => [DashboardController::class, 'worker'],

    'GET /monitor' => [MonitorController::class, 'view'],
    'GET /monitor/data' => [MonitorController::class, 'ajax'],
    'POST /ingesta' => [MonitorController::class, 'ingesta'],

    'GET /historico' => [HistoricoController::class, 'filtros'],
    'POST /historico/graficos' => [HistoricoController::class, 'graficos'],

    'GET /soporte' => [SoporteController::class, 'menu'],
    'GET /soporte/admin' => [SoporteController::class, 'listarAbiertos'],
    'GET /soporte/problema' => [SoporteController::class, 'obtenerProblema'],
    'POST /soporte/cerrar' => [SoporteController::class, 'cerrar'],
    'GET /soporte/mis' => [SoporteController::class, 'misTickets'],
    'GET /soporte/respuesta' => [SoporteController::class, 'obtenerRespuesta'],
    'GET /soporte/crear' => [SoporteController::class, 'crearForm'],
    'POST /soporte/crear' => [SoporteController::class, 'crear'],

    'GET /cuentas' => [CuentaController::class, 'menu'],
    'GET /cuentas/crear' => [CuentaController::class, 'crearForm'],
    'POST /cuentas/crear' => [CuentaController::class, 'crear'],
    'GET /cuentas/editar' => [CuentaController::class, 'editarForm'],
    'POST /cuentas/editar' => [CuentaController::class, 'editar'],
    'GET /cuentas/usuario' => [CuentaController::class, 'obtenerUsuario'],
    'POST /cuentas/guardar' => [CuentaController::class, 'guardarEdicion'],
    'GET /cuentas/reset' => [CuentaController::class, 'resetearContrasena'],

    'GET /micuenta' => [UsuarioController::class, 'miCuentaForm'],
    'POST /micuenta' => [UsuarioController::class, 'miCuenta'],

    // API para app móvil
    'POST /api/login' => [AuthApiController::class, 'login'],
    'POST /api/logout' => [AuthApiController::class, 'logout'],
    'GET /api/me' => [AuthApiController::class, 'me'],
    'GET /api/monitor' => [MonitorController::class, 'ajaxApi'],
    'POST /api/ingesta' => [MonitorController::class, 'ingesta'],
    'POST /api/change-password' => [AuthApiController::class, 'changePassword'],
    'GET /api/historico' => [HistoricoController::class, 'api'],
    'GET /api/soporte/mis' => [SoporteController::class, 'misTicketsApi'],
];
