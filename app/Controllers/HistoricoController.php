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
}
