<?php
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

    public function ingesta(): void
    {
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

        $temp = isset($payload['temp']) ? (float)$payload['temp'] : 0.0;
        $humSue = isset($payload['humSue']) ? (float)$payload['humSue'] : 0.0;
        $humAir = isset($payload['humAir']) ? (float)$payload['humAir'] : 0.0;

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
