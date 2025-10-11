<?php
class AuthController
{
    public function landing(): void
    {
        redirect('/login');
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
        $user = UsuarioModel::findByRutAndPassword($rut, $pass);
        if (!$user) {
            view('auth/login', ['error' => 'Usuario o contraseña incorrectos.']);
            return;
        }
        $_SESSION['rut'] = $user['rut'];
        $_SESSION['idPerfil'] = (string)$user['idPerfil'];
        if ((string)$user['idPerfil'] === '1') {
            redirect('/admin');
        } elseif ((string)$user['idPerfil'] === '2') {
            redirect('/worker');
        } else {
            redirect('/');
        }
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        redirect('/');
    }
}
