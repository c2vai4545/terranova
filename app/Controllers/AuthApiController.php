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
        [$ok, $clean, $errors] = Validator::validate($body, $schema);
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
        [$ok, $clean, $errors] = Validator::validate($body, $schema);
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
}
