<?php
class ApiSessionMiddleware
{
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['rut'], $_SESSION['idPerfil'])) {
            jsonResponse(['error' => 'no_autenticado'], 401);
            exit();
        }
    }
}
