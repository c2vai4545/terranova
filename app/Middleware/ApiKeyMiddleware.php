<?php
class ApiKeyMiddleware
{
    public static function requireValid(): void
    {
        $expected = AppConfig::get('api.ingesta_key');
        if (!$expected) {
            http_response_code(500);
            echo 'API key no configurada';
            exit();
        }
        $provided = self::readProvidedKey();
        if (!hash_equals($expected, $provided)) {
            http_response_code(401);
            echo 'No autorizado';
            exit();
        }
    }

    private static function readProvidedKey(): string
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (stripos($auth, 'Bearer ') === 0) {
            return trim(substr($auth, 7));
        }
        return $_SERVER['HTTP_X_API_KEY'] ?? '';
    }
}
