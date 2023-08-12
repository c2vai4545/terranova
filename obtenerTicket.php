<?php
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

$query = "SELECT id, fechaCreacion, CONCAT(Usuario.nombre1, ' ', Usuario.apellido1) AS creador FROM TicketSoporte
          LEFT JOIN Usuario ON TicketSoporte.creador = Usuario.rut
          WHERE respuesta IS NULL OR fechaRespuesta IS NULL OR solucionador IS NULL";
$stmt = $pdo->prepare($query);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<tbody id="tickets-container">
  <?php foreach ($tickets as $ticket): ?>
    <tr>
      <td><?php echo 'TCK-' . str_pad($ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
      <td><?php echo $ticket['fechaCreacion']; ?></td>
      <td><?php echo $ticket['creador']; ?></td>
      <td>
        <input type="radio" name="ticket" value="<?php echo $ticket['id']; ?>"
               onclick="mostrarProblema('<?php echo $ticket['id']; ?>')">
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
