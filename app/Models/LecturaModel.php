<?php
class LecturaModel
{
    public static function insert(int $idTipoLectura, string $fecha, string $hora, float $lectura): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO Lectura (idTipoLectura, fechaLectura, horaLectura, lectura) VALUES (:id, :f, :h, :l)');
        $stmt->execute([':id' => $idTipoLectura, ':f' => $fecha, ':h' => $hora, ':l' => $lectura]);
    }

    public static function getByTipoAndRango(int $idTipoLectura, string $inicio, string $fin): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT fechaLectura, horaLectura, lectura FROM Lectura WHERE idTipoLectura = :id AND fechaLectura >= :fi AND fechaLectura <= :ff');
        $stmt->execute([':id' => $idTipoLectura, ':fi' => $inicio, ':ff' => $fin]);
        return $stmt->fetchAll();
    }
}
