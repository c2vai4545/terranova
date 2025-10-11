<?php
class DashboardController
{
    public function admin(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            redirect('/');
        }
        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        view('dashboard/admin', ['nombre' => $nombre]);
    }

    public function worker(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '2') {
            redirect('/');
        }
        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        view('dashboard/worker', ['nombre' => $nombre]);
    }
}
