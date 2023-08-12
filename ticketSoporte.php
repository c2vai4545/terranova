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

$query = "SELECT id, fechaCreacion, CONCAT(Usuario.nombre1, ' ', Usuario.apellido1) AS creador FROM TicketSoporte
          LEFT JOIN Usuario ON TicketSoporte.creador = Usuario.rut
          WHERE respuesta IS NULL OR fechaRespuesta IS NULL OR solucionador IS NULL";
$stmt = $pdo->prepare($query);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tickets de Soporte</title>
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <h1 class="text-center">Tickets de Soporte</h1>
        <div class="wrapper">
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Fecha de Ticket</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Seleccionar</th>
                        </tr>
                    </thead>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo 'TCK-' . str_pad($ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo $ticket['fechaCreacion']; ?></td>
                            <td><?php echo $ticket['creador']; ?></td>
                            <td class="text-center">
                            <input type="radio" name="ticket" value="<?php echo $ticket['id']; ?>"
                                    onclick="mostrarProblema('<?php echo $ticket['id']; ?>')">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div id="problema" class="font-weight-bold"></div>
                <div id="respuesta-form" style="display: none;">
                    <label for="respuesta">Respuesta:</label>
                    <textarea id="respuesta" name="respuesta" rows="4" cols="50" maxlength="500" class="form-control"></textarea>
                    </br>
                    <button class="btn btn-dark btn-primario" onclick="cerrarTicket()">Cerrar Ticket</button>
                </div>
                
            </div>
            </br>
            
            <div>
                <button class="btn btn-secondary" onclick="volver()">Volver</button>
            </div>

        </div>

  <script>
    function mostrarProblema(ticketId) {
      var problema = document.getElementById("problema");
      problema.innerHTML = "";

      // Obtener el problema del ticket mediante una petición AJAX
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          problema.innerHTML = xhr.responseText;
          document.getElementById("respuesta-form").style.display = "block";
        }
      };
      xhr.open("GET", "obtenerProblema.php?id=" + ticketId, true);
      xhr.send();
    }

    function cerrarTicket() {
      var respuesta = document.getElementById("respuesta").value;
      if (respuesta.trim() === "") {
        alert("La respuesta no puede estar vacía.");
        return;
      }

      var ticketId = document.querySelector("input[name='ticket']:checked").value;

      // Realizar la petición AJAX para cerrar el ticket y guardar la respuesta
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          alert("Ticket cerrado exitosamente.");
          location.reload();
        }
      };
      xhr.open("POST", "cerrarTicket.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("id=" + ticketId + "&respuesta=" + encodeURIComponent(respuesta));
    }

    function volver() {
      location.href = "administrador.php";
    }
  </script>
</body>
</html>
