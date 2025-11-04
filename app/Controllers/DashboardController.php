<?php
class DashboardController
{
    public function admin(): void
    {
        RoleMiddleware::require([1]);
        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        view('dashboard/admin', ['nombre' => $nombre]);
    }

    public function worker(): void
    {
        RoleMiddleware::require([2]);
        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        view('dashboard/worker', ['nombre' => $nombre]);
    }
}
