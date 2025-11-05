<?php
class UsuarioModel
{
    public static function findAuthByRut(string $rut): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT rut, idPerfil, contraseña FROM Usuario WHERE rut = :rut');
        $stmt->execute([':rut' => $rut]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByRut(string $rut): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT rut, nombre1, nombre2, apellido1, apellido2, idPerfil FROM Usuario WHERE rut = :rut');
        $stmt->execute([':rut' => $rut]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getNombreCorto(string $rut): string
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT nombre1, apellido1 FROM Usuario WHERE rut = :rut');
        $stmt->execute([':rut' => $rut]);
        $row = $stmt->fetch();
        return $row ? ($row['nombre1'] . ' ' . $row['apellido1']) : '';
    }

    public static function existsRut(string $rut): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM Usuario WHERE rut = :rut');
        $stmt->execute([':rut' => $rut]);
        return (bool) $stmt->fetch();
    }

    public static function insert(array $u): bool
    {
        $pdo = Database::pdo();
        $hash = password_hash($u['contraseña'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO Usuario (rut, nombre1, nombre2, apellido1, apellido2, idPerfil, contraseña) VALUES (:rut, :n1, :n2, :a1, :a2, :perfil, :pass)');
        $stmt->execute([
            ':rut' => $u['rut'],
            ':n1' => $u['nombre1'],
            ':n2' => $u['nombre2'] ?? null,
            ':a1' => $u['apellido1'],
            ':a2' => $u['apellido2'] ?? null,
            ':perfil' => $u['idPerfil'],
            ':pass' => $hash,
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function update(array $u): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE Usuario SET nombre1 = :n1, nombre2 = :n2, apellido1 = :a1, apellido2 = :a2, idPerfil = :perfil WHERE rut = :rut');
        $stmt->execute([
            ':rut' => $u['rut'],
            ':n1' => $u['nombre1'],
            ':n2' => $u['nombre2'] ?? null,
            ':a1' => $u['apellido1'],
            ':a2' => $u['apellido2'] ?? null,
            ':perfil' => $u['idPerfil'],
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function resetPassword(string $rut, string $newPassword = 'Terranova.2023'): bool
    {
        $pdo = Database::pdo();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE Usuario SET contraseña = :pass WHERE rut = :rut');
        $stmt->execute([':pass' => $hash, ':rut' => $rut]);
        return $stmt->rowCount() > 0;
    }

    public static function listAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT u.rut, u.nombre1, u.nombre2, u.apellido1, u.apellido2, u.idPerfil, p.nombrePerfil FROM Usuario u JOIN Perfil p ON u.idPerfil = p.idPerfil');
        return $stmt->fetchAll();
    }

    public static function listActiveUsers(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT u.rut, u.nombre1, u.nombre2, u.apellido1, u.apellido2, u.idPerfil, p.nombrePerfil FROM Usuario u JOIN Perfil p ON u.idPerfil = p.idPerfil WHERE u.idPerfil != 3');
        return $stmt->fetchAll();
    }

    public static function deactivate(int $rut, int $idPerfil): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE Usuario SET idPerfil = :idPerfil WHERE rut = :rut');
        $stmt->execute([
            ':idPerfil' => $idPerfil,
            ':rut' => $rut,
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function getById(int $idUsuario): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM Usuario WHERE idUsuario = :idUsuario');
        $stmt->execute([':idUsuario' => $idUsuario]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function actualizarContrasena(string $rut, string $hashedPassword): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE Usuario SET contraseña = :pass WHERE rut = :rut');
        $stmt->execute([':pass' => $hashedPassword, ':rut' => $rut]);
        return $stmt->rowCount() > 0;
    }
}