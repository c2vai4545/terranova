<?php
session_start();

$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

$ticketId = $_GET['id'];

$query = "SELECT TicketSoporte.problema, TicketSoporte.respuesta, TicketSoporte.fechaRespuesta, CONCAT(Usuario.nombre1, ' ', Usuario.apellido1) AS solucionadorNombre
          FROM TicketSoporte
          LEFT JOIN Usuario ON TicketSoporte.solucionador = Usuario.rut
          WHERE TicketSoporte.id = :ticketId";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':ticketId', $ticketId);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$problema = $ticket['problema'];
$respuesta = $ticket['respuesta'];
$fechaRespuesta = $ticket['fechaRespuesta'];
$solucionadorNombre = $ticket['solucionadorNombre'];

$response = array(
    'problema' => $problema,
    'respuesta' => $respuesta,
    'fechaRespuesta' => $fechaRespuesta,
    'solucionador' => $solucionadorNombre
);

header('Content-Type: application/json');
echo json_encode($response);
?>
