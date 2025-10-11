<?php
class TicketSoporteModel
{
    public static function listAbiertos(): array
    {
        $pdo = Database::pdo();
        $sql = 'SELECT TicketSoporte.id, TicketSoporte.fechaCreacion, CONCAT(Usuario.nombre1, " ", Usuario.apellido1) AS creador
                FROM TicketSoporte LEFT JOIN Usuario ON TicketSoporte.creador = Usuario.rut
                WHERE TicketSoporte.respuesta IS NULL OR TicketSoporte.fechaRespuesta IS NULL OR TicketSoporte.solucionador IS NULL';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getProblema(int $id): ?string
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT problema FROM TicketSoporte WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $row['problema'] : null;
    }

    public static function cerrar(int $id, string $respuesta, string $solucionador): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE TicketSoporte SET respuesta = :r, fechaRespuesta = CURRENT_DATE(), solucionador = :s WHERE id = :id');
        $stmt->execute([':r' => $respuesta, ':s' => $solucionador, ':id' => $id]);
    }

    public static function crear(string $problema, string $creador): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO TicketSoporte (fechaCreacion, problema, creador) VALUES (CURRENT_DATE(), :p, :c)');
        $stmt->execute([':p' => $problema, ':c' => $creador]);
    }

    public static function listByCreador(string $rut): array
    {
        $pdo = Database::pdo();
        $sql = 'SELECT TicketSoporte.id, TicketSoporte.fechaCreacion, TicketSoporte.problema, TicketSoporte.respuesta, TicketSoporte.fechaRespuesta,
                       CONCAT(Usuario.nombre1, " ", Usuario.apellido1) AS solucionador
                FROM TicketSoporte LEFT JOIN Usuario ON TicketSoporte.solucionador = Usuario.rut
                WHERE TicketSoporte.creador = :rut';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':rut' => $rut]);
        return $stmt->fetchAll();
    }

    public static function obtenerRespuesta(int $id): ?array
    {
        $pdo = Database::pdo();
        $sql = 'SELECT TicketSoporte.problema, TicketSoporte.respuesta, TicketSoporte.fechaRespuesta,
                       CONCAT(Usuario.nombre1, " ", Usuario.apellido1) AS solucionadorNombre
                FROM TicketSoporte LEFT JOIN Usuario ON TicketSoporte.solucionador = Usuario.rut
                WHERE TicketSoporte.id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
