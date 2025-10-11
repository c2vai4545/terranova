<?php
class SoporteController
{
    public function menu(): void
    {
        AuthMiddleware::requireAuth();
        view('soporte/menu');
    }

    public function listarAbiertos(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            redirect('/');
        }
        $tickets = TicketSoporteModel::listAbiertos();
        view('soporte/abiertos', ['tickets' => $tickets]);
    }

    public function obtenerProblema(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $p = TicketSoporteModel::getProblema($id);
        echo $p !== null ? $p : 'ID del ticket no proporcionado.';
    }

    public function cerrar(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }
        $id = (int)($_POST['id'] ?? 0);
        $respuesta = trim($_POST['respuesta'] ?? '');
        if ($id <= 0 || $respuesta === '') {
            echo 'Error: Los campos son obligatorios';
            return;
        }
        TicketSoporteModel::cerrar($id, $respuesta, (string)$_SESSION['rut']);
        echo 'Ticket cerrado exitosamente';
    }

    public function crearForm(): void
    {
        AuthMiddleware::requireAuth();
        view('soporte/crear');
    }

    public function crear(): void
    {
        AuthMiddleware::requireAuth();
        $problema = trim($_POST['problema'] ?? '');
        if ($problema === '') {
            view('soporte/crear', ['error' => 'El campo problema es obligatorio']);
            return;
        }
        TicketSoporteModel::crear($problema, (string)$_SESSION['rut']);
        redirect('/soporte');
    }

    public function misTickets(): void
    {
        AuthMiddleware::requireAuth();
        $tickets = TicketSoporteModel::listByCreador((string)$_SESSION['rut']);
        view('soporte/mis', ['tickets' => $tickets]);
    }

    public function obtenerRespuesta(): void
    {
        AuthMiddleware::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $row = TicketSoporteModel::obtenerRespuesta($id);
        if ($row) {
            jsonResponse([
                'problema' => $row['problema'],
                'respuesta' => $row['respuesta'],
                'fechaRespuesta' => $row['fechaRespuesta'],
                'solucionador' => $row['solucionadorNombre'],
            ]);
        } else {
            jsonResponse(['problema' => null, 'respuesta' => null, 'fechaRespuesta' => null, 'solucionador' => null]);
        }
    }
}
