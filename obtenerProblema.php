<?php
// Verificar si se ha recibido el ID del ticket
if (isset($_GET['id'])) {
  $ticketId = $_GET['id'];

  $host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
  $dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
  $username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
  $password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener el problema asociado al ticket
    $query = "SELECT problema FROM TicketSoporte WHERE id = :ticketId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':ticketId', $ticketId);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $problema = $resultado['problema'];

    echo $problema;
  } catch (PDOException $e) {
    echo "Error al obtener el problema: " . $e->getMessage();
  }
} else {
  echo "ID del ticket no proporcionado.";
}
?>
