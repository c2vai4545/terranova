<?php
class TemporalModel
{
    public static function getLatest(): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT temperatura, humedadAire, humedadSuelo FROM Temporal LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function truncateAndInsert(float $temp, float $humAir, float $humSue): void
    {
        $pdo = Database::pdo();
        $pdo->exec('TRUNCATE TABLE Temporal');
        $stmt = $pdo->prepare('INSERT INTO Temporal (temperatura, humedadAire, humedadSuelo) VALUES (:t, :ha, :hs)');
        $stmt->execute([':t' => $temp, ':ha' => $humAir, ':hs' => $humSue]);
    }
}
