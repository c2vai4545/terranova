<?php
class UsuarioModel
{
    public static function findByRutAndPassword(string $rut, string $password): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT rut, nombre1, apellido1, idPerfil, contraseña FROM Usuario WHERE rut = :rut AND contraseña = :pass');
        $stmt->execute([':rut' => $rut, ':pass' => $password]);
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

    public static function insert(array $u): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO Usuario (rut, nombre1, nombre2, apellido1, apellido2, idPerfil, contraseña) VALUES (:rut, :n1, :n2, :a1, :a2, :perfil, :pass)');
        $stmt->execute([
            ':rut' => $u['rut'],
            ':n1' => $u['nombre1'],
            ':n2' => $u['nombre2'] ?? null,
            ':a1' => $u['apellido1'],
            ':a2' => $u['apellido2'] ?? null,
            ':perfil' => $u['idPerfil'],
            ':pass' => $u['contraseña'],
        ]);
    }

    public static function update(array $u): void
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
    }

    public static function resetPassword(string $rut, string $newPassword = 'Terranova.2023'): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE Usuario SET contraseña = :pass WHERE rut = :rut');
        $stmt->execute([':pass' => $newPassword, ':rut' => $rut]);
    }

    public static function listAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT rut, nombre1, nombre2, apellido1, apellido2, idPerfil FROM Usuario');
        return $stmt->fetchAll();
    }
}
