<?php
class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['rut'], $_SESSION['idPerfil'])) {
            redirect('/');
        }
    }
}
