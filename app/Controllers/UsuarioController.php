<?php
class UsuarioController
{
    public function miCuentaForm(): void
    {
        AuthMiddleware::requireAuth();
        view('usuario/mi_cuenta');
    }

    public function miCuenta(): void
    {
        AuthMiddleware::requireAuth();
        $nueva = $_POST['nuevaContrasena'] ?? '';
        $repetir = $_POST['repetirContrasena'] ?? '';
        if ($nueva !== $repetir) {
            view('usuario/mi_cuenta', ['mensaje' => 'Las contraseñas no coinciden']);
            return;
        }
        if (!PasswordPolicy::isValid($nueva)) {
            view('usuario/mi_cuenta', ['mensaje' => 'La contraseña no cumple con los requisitos']);
            return;
        }
        $usuario = UsuarioModel::findByRut((string)$_SESSION['rut']);
        if ($usuario && $usuario['rut']) {
            // Igual a la actual
            $pdo = Database::pdo();
            $stmt = $pdo->prepare('SELECT contraseña FROM Usuario WHERE rut = :rut');
            $stmt->execute([':rut' => $usuario['rut']]);
            $row = $stmt->fetch();
            if ($row && password_verify($nueva, $row['contraseña'])) {
                view('usuario/mi_cuenta', ['mensaje' => 'La nueva contraseña no puede ser igual a la contraseña actual']);
                return;
            }
            if (strlen($nueva) > 30) {
                view('usuario/mi_cuenta', ['mensaje' => 'La nueva contraseña supera la longitud máxima permitida']);
                return;
            }
            $hash = password_hash($nueva, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE Usuario SET contraseña = :p WHERE rut = :rut');
            $stmt->execute([':p' => $hash, ':rut' => $usuario['rut']]);
            redirect('/logout');
        } else {
            view('usuario/mi_cuenta', ['mensaje' => 'Usuario no encontrado']);
        }
    }
}
