<?php
class CuentaController
{
    public function menu(): void
    {
        RoleMiddleware::require([1]);
        $usuarios = UsuarioModel::listActiveUsers();
        view('cuentas/menu', ['usuarios' => $usuarios]);
    }

    public function crearForm(): void
    {
        RoleMiddleware::require([1]);
        $perfiles = PerfilModel::listAll();
        view('cuentas/crear', ['perfiles' => $perfiles]);
    }

    public function crear(): void
    {
        RoleMiddleware::require([1]);

        $schema = [
            'rut' => ['required' => true, 'regex' => '/^\d{8}$/'],
            'nombre1' => ['required' => true, 'min' => 1, 'max' => 50],
            'apellido1' => ['required' => true, 'min' => 1, 'max' => 50],
            'idPerfil' => ['required' => true, 'type' => 'int', 'in' => array_column(PerfilModel::listAll(), 'idPerfil')],
        ];
        [$ok, $clean, $errors] = Validator::validate($_POST, $schema);
        if (!$ok) {
            $perfiles = PerfilModel::listAll();
            redirect('/cuentas/crear?error=' . urlencode(implode(', ', $errors)));
            return;
        }
        $data = [
            'rut' => $clean['rut'],
            'nombre1' => $clean['nombre1'],
            'nombre2' => $_POST['nombre2'] ?? null,
            'apellido1' => $clean['apellido1'],
            'apellido2' => $_POST['apellido2'] ?? null,
            'idPerfil' => (int)$clean['idPerfil'],
            'contraseña' => 'Terranova.2023',
        ];
        if (UsuarioModel::existsRut($data['rut'])) {
            $perfiles = PerfilModel::listAll();
            view('cuentas/crear', ['perfiles' => $perfiles, 'error' => 'El RUT ingresado ya existe.']);
            return;
        }
        UsuarioModel::insert($data);
        redirect('/cuentas/crear?success=Usuario creado exitosamente.');
    }

    public function editarForm(): void
    {
        RoleMiddleware::require([1]);
        $usuarios = UsuarioModel::listAll();
        view('cuentas/editar', ['usuarios' => $usuarios]);
    }

    public function editar(): void
    {
        RoleMiddleware::require([1]);
        $schema = [
            'rut' => ['required' => true, 'regex' => '/^\d{8}$/'],
            'nombre1' => ['required' => true, 'min' => 1, 'max' => 50],
            'apellido1' => ['required' => true, 'min' => 1, 'max' => 50],
            'idPerfil' => ['required' => true, 'type' => 'int', 'in' => array_column(PerfilModel::listAll(), 'idPerfil')],
        ];
        [$ok, $clean, $errors] = Validator::validate($_POST, $schema);
        if (!$ok) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                jsonResponse(['success' => false, 'message' => 'Error de validación', 'errors' => $errors], 400);
            } else {
                $usuarios = UsuarioModel::listAll();
                view('cuentas/editar', ['usuarios' => $usuarios, 'error' => implode(', ', $errors)]);
            }
            return;
        }
        $u = [
            'rut' => $clean['rut'],
            'nombre1' => $clean['nombre1'],
            'nombre2' => $_POST['nombre2'] ?? null,
            'apellido1' => $clean['apellido1'],
            'apellido2' => $_POST['apellido2'] ?? null,
            'idPerfil' => (int)$clean['idPerfil'],
        ];
        error_log("Datos de usuario para actualizar: " . print_r($u, true));
        UsuarioModel::update($u);

        jsonResponse(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
    }

    public function obtenerUsuario(): void
    {
        RoleMiddleware::require([1]);
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
