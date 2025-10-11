<?php
class PerfilModel
{
    public static function listAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT idPerfil, nombrePerfil FROM Perfil');
        return $stmt->fetchAll();
    }
}
