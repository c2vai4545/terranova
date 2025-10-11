<?php
class TipoLecturaModel
{
    public static function listAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT idTipoLectura, nombre FROM TipoLectura');
        return $stmt->fetchAll();
    }
}
