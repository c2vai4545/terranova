<?php
class RoleMiddleware
{
    /**
     * Requiere sesión iniciada y que el idPerfil del usuario esté dentro de los permitidos.
     * En peticiones API (cabecera Accept: application/json o ruta que comience con /api) responde JSON 403;
     * en peticiones normales hace redirect a '/'.
     * @param int[] $allowedIds
     */
    public static function require(array $allowedIds): void
    {
        AuthMiddleware::requireAuth();
        $perfil = (int)($_SESSION['idPerfil'] ?? 0);
        if (in_array($perfil, $allowedIds, true)) {
            return; // autorizado
        }
        // Determinar si es petición API
        $isApi = (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/api'))
            || (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json');
        if ($isApi) {
            jsonResponse(['error' => 'forbidden'], 403);
        } else {
            redirect('/');
        }
        exit();
    }
}