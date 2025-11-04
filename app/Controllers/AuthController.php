<?php
class AuthController
{
    public function landing(): void
    {
        view('home/landing');
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['rut'], $_SESSION['idPerfil'])) {
            if ((string)$_SESSION['idPerfil'] === '1') {
                redirect('/admin');
            }
            if ((string)$_SESSION['idPerfil'] === '2') {
                redirect('/worker');
            }
        }
        view('auth/login');
    }

    public function login(): void
    {
        $rut = $_POST['rut'] ?? '';
        $pass = $_POST['contrasena'] ?? '';
        if ($rut === '' || $pass === '' || !Validation::isRut8($rut)) {
            view('auth/login', ['error' => 'Credenciales inválidas.']);
            return;
        }
        $auth = UsuarioModel::findAuthByRut($rut);
        if (!$auth || !password_verify($pass, $auth['contraseña'])) {
            view('auth/login', ['error' => 'Usuario o contraseña incorrectos.']);
            return;
        }
        $_SESSION['rut'] = $auth['rut'];
        $_SESSION['idPerfil'] = (string)$auth['idPerfil'];

        // Generar y almacenar JWT para uso unificado
        $token = TokenService::generate([
            'rut' => $auth['rut'],
            'idPerfil' => (int)$auth['idPerfil'],
        ]);

        // Enviar JWT en cookie (para vistas web) y en header opcional
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
        setcookie('jwt', $token, [
            'expires' => 0,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Authorization: Bearer ' . $token);

        if ((string)$auth['idPerfil'] === '1') {
            redirect('/admin');
        } elseif ((string)$auth['idPerfil'] === '2') {
            redirect('/worker');
        } else {
            redirect('/');
        }
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        // Borrar cookie JWT
        setcookie('jwt', '', time() - 3600, '/', '', true, true);
        redirect('/');
    }
}
