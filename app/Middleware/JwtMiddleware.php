<?php

class JwtMiddleware
{
    public static function requireAuth(): void
    {
        $token = null;

        // Obtener el header Authorization de la forma más robusta posible
        $authHeader = '';
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // Algunos servidores (por ejemplo Nginx + PHP-FPM) lo exponen así
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    $authHeader = $value;
                    break;
                }
            }
        }

        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));
        } elseif (isset($_COOKIE['jwt'])) {
            $token = $_COOKIE['jwt'];
        }

        if (!$token) {
            // Sin token => no autenticado
            self::unauthorized();
        }

        $payload = TokenService::verify($token);
        if ($payload === null) {
            self::unauthorized();
        }

        // Cargar datos del usuario en $_SESSION para compatibilidad
        $_SESSION['rut'] = $payload['rut'] ?? null;
        $_SESSION['idPerfil'] = $payload['idPerfil'] ?? null;
    }

    private static function unauthorized(): void
    {
        // Limpiar cookie JWT y sesión para evitar bucles de redirección
        if (isset($_COOKIE['jwt'])) {
            setcookie('jwt', '', time() - 3600, '/', '', false, true);
            unset($_COOKIE['jwt']);
        }
        session_unset();

        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            jsonResponse(['error' => 'no_autenticado'], 401);
        } else {
            redirect('/login');
        }
        exit();
    }
}