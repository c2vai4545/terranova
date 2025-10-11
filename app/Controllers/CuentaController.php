<?php
class CuentaController
{
    public function menu(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        view('cuentas/menu');
    }

    public function crearForm(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        $perfiles = PerfilModel::listAll();
        view('cuentas/crear', ['perfiles' => $perfiles]);
    }

    public function crear(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');

        $data = [
            'rut' => $_POST['rut'] ?? '',
            'nombre1' => $_POST['nombre1'] ?? '',
            'nombre2' => $_POST['nombre2'] ?? null,
            'apellido1' => $_POST['apellido1'] ?? '',
            'apellido2' => $_POST['apellido2'] ?? null,
            'idPerfil' => (int)($_POST['perfil'] ?? 0),
            'contraseña' => 'Terranova.2023',
        ];

        if (!Validation::isRut8($data['rut']) || $data['nombre1'] === '' || $data['apellido1'] === '' || $data['idPerfil'] === 0) {
            $perfiles = PerfilModel::listAll();
            view('cuentas/crear', ['perfiles' => $perfiles, 'error' => 'Datos inválidos.']);
            return;
        }
        if (UsuarioModel::existsRut($data['rut'])) {
            $perfiles = PerfilModel::listAll();
            view('cuentas/crear', ['perfiles' => $perfiles, 'error' => 'El RUT ingresado ya existe.']);
            return;
        }
        UsuarioModel::insert($data);
        redirect('/cuentas');
    }

    public function editarForm(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        $usuarios = UsuarioModel::listAll();
        view('cuentas/editar', ['usuarios' => $usuarios]);
    }

    public function editar(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        $rut = $_POST['rut'] ?? '';
        $u = [
            'rut' => $rut,
            'nombre1' => $_POST['nombre1'] ?? '',
            'nombre2' => $_POST['nombre2'] ?? null,
            'apellido1' => $_POST['apellido1'] ?? '',
            'apellido2' => $_POST['apellido2'] ?? null,
            'idPerfil' => (int)($_POST['perfil'] ?? 0),
        ];
        UsuarioModel::update($u);
        redirect('/cuentas/editar');
    }

    public function obtenerUsuario(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        $rut = $_GET['rut'] ?? '';
        $usuario = UsuarioModel::findByRut($rut);
        $perfiles = PerfilModel::listAll();
        jsonResponse(['usuario' => $usuario, 'perfiles' => $perfiles]);
    }

    public function guardarEdicion(): void
    {
        $this->editar();
    }

    public function resetearContrasena(): void
    {
        AuthMiddleware::requireAuth();
        if ((string)($_SESSION['idPerfil'] ?? '') !== '1') redirect('/');
        $rut = $_GET['rut'] ?? '';
        UsuarioModel::resetPassword($rut);
        echo 'Contraseña reseteada exitosamente.';
    }
}
