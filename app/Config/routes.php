<?php
return [
    'GET /' => [AuthController::class, 'landing'],
    'GET /login' => [AuthController::class, 'showLogin'],
    'POST /login' => [AuthController::class, 'login'],
    'POST /logout' => [AuthController::class, 'logout'],

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
    'POST /cuentas/reset' => [CuentaController::class, 'resetearContrasena'],

    'GET /micuenta' => [UsuarioController::class, 'miCuentaForm'],
    'POST /micuenta' => [UsuarioController::class, 'miCuenta'],

    // API para app móvil
    'POST /api/v1/login' => [AuthApiController::class, 'login'],
    'POST /api/v1/logout' => [AuthApiController::class, 'logout'],
    'GET /api/v1/me' => [AuthApiController::class, 'me'],
    'GET /api/v1/monitor' => [MonitorController::class, 'ajaxApi'],
    'POST /api/v1/ingesta' => [MonitorController::class, 'ingesta'],
    'POST /api/v1/change-password' => [AuthApiController::class, 'changePassword'],
    'GET /api/v1/historico' => [HistoricoController::class, 'api'],
    'GET /api/v1/soporte/mis' => [SoporteController::class, 'misTicketsApi'],
    'POST /api/v1/soporte/new' => [SoporteController::class, 'crearApi'],
    'POST /api/v1/cuentas/desactivar' => [AuthApiController::class, 'deactivateUser'],
];
