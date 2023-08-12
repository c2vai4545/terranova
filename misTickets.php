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

if (!isset($_SESSION['idPerfil'])) {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
    exit();
}

$idPerfil = $_SESSION['idPerfil'];
$rut = $_SESSION['rut'];

$query = "SELECT TicketSoporte.id, TicketSoporte.fechaCreacion, TicketSoporte.problema, 
                 TicketSoporte.respuesta, TicketSoporte.fechaRespuesta, 
                 CONCAT(Usuario.nombre1, ' ', Usuario.apellido1) AS solucionador
          FROM TicketSoporte
          LEFT JOIN Usuario ON TicketSoporte.solucionador = Usuario.rut
          WHERE TicketSoporte.creador = :rut";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':rut', $rut);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mis Tickets de Soporte</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
  
  <div class="wrapper">
    <h1 class="text-center">Mis Tickets de Soporte</h1>
        <div class="card">

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha de Ticket</th>
                        <th>Solucionador</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo 'TCK-' . str_pad($ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo $ticket['fechaCreacion']; ?></td>
                    <td><?php echo ($ticket['solucionador'] !== null) ? $ticket['solucionador'] : 'Sin Asignar'; ?></td>
                    <td><?php echo $ticket['respuesta'] ? 'Solucionado' : 'Pendiente'; ?></td>
                    <td>
                    <input type="radio" name="ticket" value="<?php echo $ticket['id']; ?>"
                            onclick="mostrarRespuesta('<?php echo $ticket['id']; ?>')">
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <div id="respuesta-form" style="display: none;">
                <label for="problema-label" class="font-weight-bold">Problema:</label>
                <div id="problema-label"></div>
                <br>
                <label for="respuesta-label" class="font-weight-bold">Respuesta:</label>
                <div id="respuesta-label"></div>
            </div>
        </div>
        <br/>
        <button class="btn btn-secondary" onclick="volver()">Volver</button>
    </div>


  

  <script>
    function mostrarRespuesta(ticketId) {
      var problemaLabel = document.getElementById("problema-label");
      var respuestaLabel = document.getElementById("respuesta-label");

      // Obtener la respuesta del ticket mediante una petición AJAX
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          var respuestaData = JSON.parse(xhr.responseText);
          problemaLabel.innerHTML = respuestaData.problema;
          respuestaLabel.innerHTML = respuestaData.respuesta ? respuestaData.respuesta + " - " + respuestaData.fechaRespuesta + " - " + respuestaData.solucionador : "Sin Asignar";
          document.getElementById("respuesta-form").style.display = "block";
        }
      };
      xhr.open("GET", "obtenerRespuesta.php?id=" + ticketId, true);
      xhr.send();
    }

    function volver() {
      location.href = "trabajador.php";
    }
  </script>
</body>
</html>
