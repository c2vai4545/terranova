<?php

require_once __DIR__ . '/../Services/RateLimiter.php';

class MonitorController
{
    public function view(): void
    {
        AuthMiddleware::requireAuth();
        $data = TemporalModel::getLatest();
        view('monitor/view', ['data' => $data]);
    }

    public function ajax(): void
    {
        AuthMiddleware::requireAuth();
        $row = TemporalModel::getLatest();
        if ($row) {
            jsonResponse([
                'temperatura' => $row['temperatura'],
                'humedadAire' => $row['humedadAire'],
                'humedadSuelo' => $row['humedadSuelo'],
            ]);
        } else {
            jsonResponse(['temperatura' => null, 'humedadAire' => null, 'humedadSuelo' => null]);
        }
    }

    // Variante API: responde 401 JSON si no hay sesión
    // public function ajaxApi(): void
    // {
    //     JwtMiddleware::requireAuth();
    //     $row = TemporalModel::getLatest();
    //     if ($row) {
    //         jsonResponse([
    //             'temperatura' => $row['temperatura'],
    //             'humedadAire' => $row['humedadAire'],
    //             'humedadSuelo' => $row['humedadSuelo'],
    //         ]);
    //     } else {
    //         jsonResponse(['temperatura' => null, 'humedadAire' => null, 'humedadSuelo' => null]);
    //     }
    // }

    public function ingesta(): void
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        RateLimiter::setLimit(10); // 10 requests
        RateLimiter::setWindow(60); // in 1 minute

        if (!RateLimiter::check($ipAddress)) {
            http_response_code(429);
            echo 'Demasiadas solicitudes de ingesta. Intente de nuevo en ' . RateLimiter::getRetryAfter($ipAddress) . ' segundos.';
            exit();
        }
        RateLimiter::record($ipAddress);

        // Requerir autenticación por JWT
        JwtMiddleware::requireAuth();
        $idPerfil = isset($_SESSION['idPerfil']) ? (int)$_SESSION['idPerfil'] : null;
        // Política: solo Trabajador (idPerfil = 2) puede ingresar lecturas manuales
        if ($idPerfil !== 2) {
            http_response_code(403);
            echo 'No autorizado para ingresar lecturas';
            exit();
        }
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $payload = [];
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        } else {
            $payload = $_POST;
        }

        $schema = [
            'temp' => ['required' => true, 'type' => 'float'],
            'humSue' => ['required' => true, 'type' => 'float'],
            'humAir' => ['required' => true, 'type' => 'float'],
        ];
        [$ok, $clean, $errors] = Validator::validate($payload, $schema);
        if (!$ok) {
            http_response_code(400);
            echo 'Errores de validación: ' . implode(', ', $errors);
            return;
        }

        $temp = (float)$clean['temp'];
        $humSue = (float)$clean['humSue'];
        $humAir = (float)$clean['humAir'];

        try {
            TemporalModel::truncateAndInsert($temp, $humAir, $humSue);
            if (LecturaService::hoursSinceLast(new Database()) >= 2) {
                $date = date('Y-m-d');
                $time = date('H:i:s');
                LecturaModel::insert(1, $date, $time, $temp);
                LecturaModel::insert(3, $date, $time, $humSue);
                LecturaModel::insert(2, $date, $time, $humAir);
            }
            echo 'Los datos se guardaron correctamente en la base de datos.';
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'Error: ' . $e->getMessage();
        }
    }
}
