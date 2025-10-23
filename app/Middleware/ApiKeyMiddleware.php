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
        // 1) Intentar leer Authorization desde variables de servidor comunes
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        // 2) Fallback a getallheaders()/apache_request_headers en entornos donde no se propaga
        if ($auth === '' || $auth === null) {
            $headers = [];
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } elseif (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
            }
            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    if (strcasecmp($name, 'Authorization') === 0) {
                        $auth = $value;
                        break;
                    }
                }
            }
        }

        // 3) Si es Bearer, devolver el token
        if (is_string($auth) && stripos($auth, 'Bearer ') === 0) {
            return trim(substr($auth, 7));
        }

        // 4) Leer X-Api-Key desde $_SERVER y, si no, desde headers crudos
        $xApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if ($xApiKey === '' || $xApiKey === null) {
            $headers = [];
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } elseif (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
            }
            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    if (strcasecmp($name, 'X-Api-Key') === 0) {
                        $xApiKey = $value;
                        break;
                    }
                }
            }
        }

        return is_string($xApiKey) ? trim($xApiKey) : '';
    }
}
