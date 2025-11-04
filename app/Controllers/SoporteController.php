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

    // API para app móvil: obtener mis tickets
    public function misTicketsApi(): void
    {
        JwtMiddleware::requireAuth();
        $tickets = TicketSoporteModel::listByCreador((string)$_SESSION['rut']);

        $formattedTickets = [];
        foreach ($tickets as $ticket) {
            $estado = 'abierto';
            if (!empty($ticket['respuesta']) && !empty($ticket['fechaRespuesta'])) {
                $estado = 'cerrado';
            }

            $formattedTickets[] = [
                'id' => (int)$ticket['id'],
                'fechaCreacion' => $ticket['fechaCreacion'] . 'T00:00:00Z', // Formato ISO básico
                'estado' => $estado,
                'tieneRespuesta' => !empty($ticket['respuesta'])
            ];
        }

        jsonResponse(['tickets' => $formattedTickets]);
    }

    // API para app móvil: crear nuevo ticket
    public function crearApi(): void
    {
        ApiSessionMiddleware::requireAuth();

        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $problema = trim($body['problema'] ?? '');

        if ($problema === '') {
            jsonResponse(['error' => 'El campo problema es obligatorio'], 400);
            return;
        }

        try {
            TicketSoporteModel::crear($problema, (string)$_SESSION['rut']);
            jsonResponse(['ok' => true, 'message' => 'Ticket creado exitosamente']);
        } catch (Throwable $e) {
            jsonResponse(['error' => 'Error al crear el ticket: ' . $e->getMessage()], 500);
        }
    }
}
