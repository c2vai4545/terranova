<?php
class DashboardController
{
    public function admin(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            redirect('/');
        }
        view('dashboard/admin');
    }

    public function worker(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '2') {
            redirect('/');
        }
        view('dashboard/worker');
    }
}
