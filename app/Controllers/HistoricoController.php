<?php
class HistoricoController
{
    public function filtros(): void
    {
        AuthMiddleware::requireAuth();
        $tipos = TipoLecturaModel::listAll();
        view('historico/filtros', ['tipos' => $tipos]);
    }

    public function graficos(): void
    {
        AuthMiddleware::requireAuth();
        $fechaInicio = $_POST['fechaInicio'] ?? '';
        $fechaFin = $_POST['fechaFin'] ?? '';
        $tipos = $_POST['tiposLectura'] ?? [];
        $lecturas = [];
        // Mapa idTipoLectura => nombre (para títulos/leyendas claras)
        $tiposMap = [];
        foreach (TipoLecturaModel::listAll() as $t) {
            $tiposMap[(int)$t['idTipoLectura']] = $t['nombre'];
        }
        foreach ($tipos as $idTipo) {
            $data = LecturaModel::getByTipoAndRango((int)$idTipo, $fechaInicio, $fechaFin);
            $lecturas[] = [
                'tipoLectura' => (int)$idTipo,
                'tipoNombre' => $tiposMap[(int)$idTipo] ?? ('Tipo ' . (int)$idTipo),
                'data' => $data,
            ];
        }
        view('historico/graficos', ['lecturas' => $lecturas]);
    }

    // API para histórico (JSON)
    public function api(): void
    {
        ApiSessionMiddleware::requireAuth();
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $tiposParam = $_GET['tipos'] ?? '';

        // Validación simple de fechas (YYYY-MM-DD)
        $isDate = function (string $v): bool {
            return (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $v);
        };
        if (!$isDate($start) || !$isDate($end)) {
            $end = date('Y-m-d');
            $start = date('Y-m-d', strtotime('-7 days'));
        }

        // Listado de tipos disponibles y mapa id=>nombre
        $tiposAll = TipoLecturaModel::listAll();
        $tiposMap = [];
        foreach ($tiposAll as $t) {
            $tiposMap[(int)$t['idTipoLectura']] = $t['nombre'];
        }

        // Parseo de tipos solicitados
        $tipos = [];
        if (is_string($tiposParam) && trim($tiposParam) !== '') {
            foreach (explode(',', $tiposParam) as $raw) {
                $id = (int)trim($raw);
                if ($id > 0 && isset($tiposMap[$id])) {
                    $tipos[] = $id;
                }
            }
        }
        if (!$tipos) {
            $tipos = array_keys($tiposMap);
        }

        $series = [];
        foreach ($tipos as $idTipo) {
            $data = LecturaModel::getByTipoAndRango((int)$idTipo, $start, $end);
            $series[] = [
                'idTipoLectura' => (int)$idTipo,
                'tipoNombre' => $tiposMap[(int)$idTipo] ?? ('Tipo ' . (int)$idTipo),
                'data' => $data,
            ];
        }

        jsonResponse([
            'start' => $start,
            'end' => $end,
            'series' => $series,
        ]);
    }
}
