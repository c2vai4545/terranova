<?php
class LecturaService
{
    public static function hoursSinceLast(Database $db, string $table = 'Lectura'): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query("SELECT fechaLectura, horaLectura FROM {$table} ORDER BY fechaLectura DESC, horaLectura DESC LIMIT 1");
        $row = $stmt->fetch();
        if (!$row) return PHP_INT_MAX;
        $lastTs = strtotime($row['fechaLectura'] . ' ' . $row['horaLectura']);
        return (int) floor((time() - $lastTs) / 3600);
    }
}
