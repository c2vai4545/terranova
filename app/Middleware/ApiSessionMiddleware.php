<?php
class ApiSessionMiddleware
{
    public static function requireAuth(): void
    {
        // Delegar a JwtMiddleware para validación unificada
        JwtMiddleware::requireAuth();
    }
}
