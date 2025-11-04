<?php
class AuthMiddleware
{
    public static function requireAuth(): void
    {
        JwtMiddleware::requireAuth();
    }
}
