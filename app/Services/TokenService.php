<?php

class TokenService
{
    private const ALG = 'HS256';

    /**
     * Genera un JWT usando HS256 y la clave configurada.
     * @param array $payload
     * @param int $ttl Segundos que dura el token (por defecto 1 hora)
     * @return string
     */
    public static function generate(array $payload, int $ttl = 3600): string
    {
        $header = [
            'alg' => self::ALG,
            'typ' => 'JWT',
        ];
        $payload['exp'] = time() + $ttl;
        $segments = [];
        $segments[] = self::base64UrlEncode(json_encode($header));
        $segments[] = self::base64UrlEncode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, self::secret(), true);
        $segments[] = self::base64UrlEncode($signature);
        return implode('.', $segments);
    }

    /**
     * Verifica la firma y vigencia del JWT.
     * @param string $jwt
     * @return array|null Devuelve el payload si es válido o null si no.
     */
    public static function verify(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        [$header64, $payload64, $signature64] = $parts;
        $signingInput = $header64 . '.' . $payload64;
        $expected = self::base64UrlEncode(hash_hmac('sha256', $signingInput, self::secret(), true));
        if (!hash_equals($expected, $signature64)) {
            return null;
        }
        $payloadJson = self::base64UrlDecode($payload64);
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || !isset($payload['exp']) || $payload['exp'] < time()) {
            return null; // Expirado o mal formado
        }
        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function secret(): string
    {
        // 1) Variable de entorno (ideal para Koyeb y cualquier PaaS)
        $env = getenv('JWT_SECRET');
        if ($env !== false && $env !== '') {
            return $env;
        }

        // 2) Archivos de configuración locales
        $cfg = require __DIR__ . '/../Config/config.php';
        if (isset($cfg['jwt']['secret']) && $cfg['jwt']['secret'] !== '') {
            return $cfg['jwt']['secret'];
        }

        // 3) Valor por defecto (no usar en producción)
        return 'CAMBIAR_ESTA_CLAVE_JWT';
    }
}