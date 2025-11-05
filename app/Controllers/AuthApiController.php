<?php

class AuthApiController
{
    public function login(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $schema = [
            'rut' => ['required' => true, 'regex' => '/^\d{8}$/'],
            'contrasena' => ['required' => true, 'min' => 1],
        ];
        [$ok, $clean, $errors] = \Validator::validate($body, $schema);
        if (!$ok) {
            jsonResponse(['error' => 'credenciales_invalidas'], 400);
            return;
        }
        $rut = $clean['rut'];
        $pass = $clean['contrasena'];

        $auth = UsuarioModel::findAuthByRut($rut);
        if (!$auth || !password_verify($pass, $auth['contraseña'])) {
            jsonResponse(['error' => 'usuario_o_contrasena_incorrectos'], 401);
            return;
        }
        $_SESSION['rut'] = $auth['rut'];
        $_SESSION['idPerfil'] = (string)$auth['idPerfil'];
        // Generar JWT
        $token = TokenService::generate([
            'rut' => $auth['rut'],
            'idPerfil' => (int)$auth['idPerfil'],
        ]);
        jsonResponse(['rut' => $auth['rut'], 'idPerfil' => (int)$auth['idPerfil'], 'token' => $token]);
    }

    public function logout(): void
    {
        ApiSessionMiddleware::requireAuth();
        session_unset();
        session_destroy();
        jsonResponse(['ok' => true]);
    }

    public function me(): void
    {
        ApiSessionMiddleware::requireAuth();
        jsonResponse(['auth' => true, 'rut' => $_SESSION['rut'], 'idPerfil' => (int)$_SESSION['idPerfil']]);
    }

    public function changePassword(): void
    {
        ApiSessionMiddleware::requireAuth();
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $new = $body['nuevaContrasena'] ?? '';
        if (!PasswordPolicy::isValid($new) || strlen($new) > 30) {
            jsonResponse(['error' => 'contrasena_invalida'], 400);
            return;
        }
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT contraseña FROM Usuario WHERE rut = :rut');
        $stmt->execute([':rut' => $_SESSION['rut']]);
        $row = $stmt->fetch();
        if ($row && password_verify($new, $row['contraseña'])) {
            jsonResponse(['error' => 'igual_a_actual'], 400);
            return;
        }
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE Usuario SET contraseña = :p WHERE rut = :rut');
        $stmt->execute([':p' => $hash, ':rut' => $_SESSION['rut']]);
        jsonResponse(['ok' => true]);
    }

    public function deactivateUser(): void
    {
        ApiSessionMiddleware::requireAuth();
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $schema = [
            'rut' => ['required' => true, 'type' => 'int', 'min' => 1000000, 'max' => 99999999],
        ];
        [$ok, $clean, $errors] = \Validator::validate($body, $schema);
        if (!$ok) {
            jsonResponse(['error' => 'rut_invalido', 'details' => $errors], 400);
            return;
        }
        $rut = (int)$clean['rut'];

        $updated = UsuarioModel::deactivate($rut, 3); // 3 es un ejemplo, ajusta según tu lógica de perfiles

        if ($updated) {
            jsonResponse(['ok' => true, 'message' => 'Usuario desactivado correctamente.']);
        } else {
            jsonResponse(['error' => 'no_se_pudo_desactivar_usuario'], 500);
        }
    }

    public function listCuentasApi()
    {
        $usuarioModel = new UsuarioModel();
        $activeUsers = $usuarioModel->listActiveUsers();
        header('Content-Type: application/json');
        echo json_encode($activeUsers);
        return;
    }

    public function cuentasEditarFormApi(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::require([1]);
        $id = (int)($_GET['id'] ?? 0);
        $user = UsuarioModel::getById($id);
        if (!$user) {
            jsonResponse(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
            return;
        }
        jsonResponse(['status' => 'success', 'user' => $user]);
    }

    public function resetearContrasenaApi()
    {
        ApiSessionMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $rut = $input['rut'] ?? null;

        if (!$rut) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Falta el RUT.']);
            return;
        }

        if (UsuarioModel::resetPassword($rut)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Contraseña reseteada exitosamente.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al resetear la contraseña.']);
        }
        return;
    }

    public function listarAbiertosApi()
    {
        ApiSessionMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
            return;
        }

        $tickets = TicketSoporteModel::listAbiertos();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'tickets' => $tickets]);
        return;
    }

    public function obtenerProblemaApi($id)
    {
        ApiSessionMiddleware::requireAuth();
        $id = (int)$id;
        $problema = TicketSoporteModel::getProblema($id);

        header('Content-Type: application/json');
        if ($problema !== null) {
            echo json_encode(['status' => 'success', 'problema' => $problema]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID del ticket no proporcionado o no encontrado.']);
        }
        return;
    }

    public function cerrarApi()
    {
        ApiSessionMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $respuesta = $input['respuesta'] ?? null;

        if (!$id || !$respuesta) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios: id o respuesta.']);
            return;
        }

        if (TicketSoporteModel::cerrar($id, $respuesta, (string)$_SESSION['rut'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Ticket cerrado exitosamente.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al cerrar el ticket.']);
        }
        return;
    }

    public function obtenerRespuestaApi($id)
    {
        ApiSessionMiddleware::requireAuth();
        $id = (int)$id;
        $row = TicketSoporteModel::obtenerRespuesta($id);

        header('Content-Type: application/json');
        if ($row) {
            echo json_encode([
                'status' => 'success',
                'problema' => $row['problema'],
                'respuesta' => $row['respuesta'],
                'fechaRespuesta' => $row['fechaRespuesta'],
                'solucionador' => $row['solucionadorNombre'],
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID del ticket no proporcionado o no encontrado.']);
        }
        return;
    }

    public function miCuentaFormApi()
    {
        ApiSessionMiddleware::requireAuth();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Información de la cuenta lista.']);
        return;
    }

    public function miCuentaApi(): void
    {
        AuthMiddleware::requireAuth();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        $nuevaContrasena = $data['nuevaContrasena'] ?? '';
        $repetirContrasena = $data['repetirContrasena'] ?? '';

        $errores = [];

        if (empty($nuevaContrasena)) {
            $errores[] = 'La nueva contraseña no puede estar vacía.';
        }

        if ($nuevaContrasena !== $repetirContrasena) {
            $errores[] = 'Las contraseñas no coinciden.';
        }

        if (strlen($nuevaContrasena) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $nuevaContrasena)) {
            $errores[] = 'La contraseña debe contener al menos una letra mayúscula.';
        }

        if (!preg_match('/[a-z]/', $nuevaContrasena)) {
            $errores[] = 'La contraseña debe contener al menos una letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $nuevaContrasena)) {
            $errores[] = 'La contraseña debe contener al menos un número.';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $nuevaContrasena)) {
            $errores[] = 'La contraseña debe contener al menos un carácter especial.';
        }

        if (!empty($errores)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        $rut = $_SESSION['rut'];
        $hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        if (UsuarioModel::actualizarContrasena($rut, $hashedPassword)) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
        }
    }

    public function guardarEdicionApi()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;
        $rut = $input['rut'] ?? null;
        $nombre1 = $input['nombre1'] ?? null;
        $nombre2 = $input['nombre2'] ?? null;
        $apellido1 = $input['apellido1'] ?? null;
        $apellido2 = $input['apellido2'] ?? null;
        $email = $input['email'] ?? null;
        $idPerfil = $input['idPerfil'] ?? null;

        if (!$id || !$rut || !$nombre1 || !$apellido1 || !$email || !$idPerfil) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios.']);
            return;
        }

        $usuarioModel = new UsuarioModel();
        $data = [
            'rut' => $rut,
            'nombre1' => $nombre1,
            'nombre2' => $nombre2,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => $email,
            'idPerfil' => $idPerfil
        ];

        if ($usuarioModel->update($data)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario.']);
        }
        return;
    }

    public function editarApi()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;
        $rut = $input['rut'] ?? null;
        $nombre1 = $input['nombre1'] ?? null;
        $nombre2 = $input['nombre2'] ?? null;
        $apellido1 = $input['apellido1'] ?? null;
        $apellido2 = $input['apellido2'] ?? null;
        $email = $input['email'] ?? null;
        $idPerfil = $input['idPerfil'] ?? null;

        if (!$id || !$rut || !$nombre1 || !$apellido1 || !$email || !$idPerfil) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios.']);
            return;
        }

        $usuarioModel = new UsuarioModel();
        $data = [
            'rut' => $rut,
            'nombre1' => $nombre1,
            'nombre2' => $nombre2,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => $email,
            'idPerfil' => $idPerfil
        ];

        if ($usuarioModel->update($data)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario.']);
        }
        return;
    }

    public function crearFormApi()
    {
        $perfilModel = new PerfilModel();
        $perfiles = $perfilModel->listAll();
        header('Content-Type: application/json');
        echo json_encode($perfiles);
        return;
    }

    public function crearApi()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $rut = $input['rut'] ?? null;
        $nombre1 = $input['nombre1'] ?? null;
        $nombre2 = $input['nombre2'] ?? null;
        $apellido1 = $input['apellido1'] ?? null;
        $apellido2 = $input['apellido2'] ?? null;
        $email = $input['email'] ?? null;
        $idPerfil = $input['idPerfil'] ?? null;
        $contrasena = $input['contrasena'] ?? null;

        if (!$rut || !$nombre1 || !$apellido1 || !$email || !$idPerfil || !$contrasena) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios.']);
            return;
        }

        if (!PasswordPolicy::isValid($contrasena)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'La contraseña no cumple con la política de seguridad.']);
            return;
        }

        $usuarioModel = new UsuarioModel();
        $hashedPassword = password_hash($contrasena, PASSWORD_BCRYPT);

        $data = [
            'rut' => $rut,
            'nombre1' => $nombre1,
            'nombre2' => $nombre2,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => $email,
            'idPerfil' => $idPerfil,
            'contraseña' => $hashedPassword
        ];

        if ($usuarioModel->insert($data)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Usuario creado correctamente.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el usuario.']);
        }
        return;
    }

    public function cuentasObtenerUsuarioApi($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getById($id);
        header('Content-Type: application/json');
        echo json_encode($usuario);
        return;
    }

    public function historicoFiltrosApi(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') {
            jsonResponse(['status' => 'error', 'message' => 'Acceso denegado'], 403);
            return;
        }
        $tipos = TipoLecturaModel::listAll();
        jsonResponse(['status' => 'success', 'tipos' => $tipos]);
    }

    public function adminApi(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::require([1]);
        header('Content-Type: application/json');

        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        echo json_encode(['success' => true, 'nombre' => $nombre]);
    }

    public function workerApi(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::require([2]);
        header('Content-Type: application/json');

        $nombre = UsuarioModel::getNombreCorto((string)$_SESSION['rut']);
        echo json_encode(['success' => true, 'nombre' => $nombre]);
    }

    public function graficosApi(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::require([1]);
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        $fechaInicio = $input['fechaInicio'] ?? '';
        $fechaFin = $input['fechaFin'] ?? '';
        $tipos = $input['tiposLectura'] ?? [];

        $lecturas = LecturaModel::getByTipoAndRango($tipos, $fechaInicio, $fechaFin);

        $tiposMap = [];
        foreach (TipoLecturaModel::listAll() as $t) {
            $tiposMap[(int)$t['idTipoLectura']] = $t['nombre'];
        }

        echo json_encode(['success' => true, 'lecturas' => $lecturas, 'tiposMap' => $tiposMap]);
    }

    public function soporteMenuApi(): void
    {
        AuthMiddleware::requireAuth();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Acceso al menú de soporte concedido.']);
    }

    public function cuentasMenuApi(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::require([1]);
        header('Content-Type: application/json');

        $usuarios = UsuarioModel::listActiveUsers();
        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
    }

    public function landingApi(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Página de inicio disponible.']);
    }

    public function showLoginApi(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Endpoint de inicio de sesión disponible.']);
    }

    public function monitorDataApi(): void
    {
        JwtMiddleware::requireAuth();
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
}
