<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["rut"])) {
    header("Location: index.php");
    exit();
}

// Obtener el RUT y idPerfil del usuario autenticado
$rut = $_SESSION["rut"];
$idPerfil = $_SESSION["idPerfil"];

// Obtener los datos de temperatura, humedad de aire y humedad de suelo de la tabla Temporal
$servername = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";

// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hay errores en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos de temperatura, humedad de aire y humedad de suelo de la tabla Temporal
$sql = "SELECT temperatura, humedadAire, humedadSuelo FROM Temporal";
$result = $conn->query($sql);

// Verificar si se encontraron registros
if ($result->num_rows > 0) {
    // Obtener el primer registro
    $row = $result->fetch_assoc();

    // Asignar los valores a las variables
    $temperatura = $row["temperatura"];
    $humedadAire = $row["humedadAire"];
    $humedadSuelo = $row["humedadSuelo"];
} else {
    // Si no se encontraron registros, asignar valores por defecto
    $temperatura = "N/A";
    $humedadAire = "N/A";
    $humedadSuelo = "N/A";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitoreo</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos.css" />
    <script>
      // Función para realizar la solicitud AJAX y actualizar el contenido
      function actualizarContenido() {
        // Crear el objeto XMLHttpRequest
        var xmlhttp = new XMLHttpRequest();

        // Definir la función de respuesta
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            // Actualizar el contenido de los elementos en la página
            var datos = JSON.parse(this.responseText);
            document.getElementById("temperatura").innerHTML = datos.temperatura + "&deg;C";
            document.getElementById("humedadAire").innerHTML = datos.humedadAire + "%";
            document.getElementById("humedadSuelo").innerHTML = datos.humedadSuelo + "%";
          }
        };

        // Realizar la solicitud AJAX
        xmlhttp.open("GET", "monitoreo_ajax.php", true);
        xmlhttp.send();
      }

      // Ejecutar la función de actualización cada 5 segundos
      setInterval(actualizarContenido, 5000);
    </script>
</head>
<body>
    <div>
        <h1>TerranovaGreenPUQ</h1>
    </div>
    <div class="container marketing">
        <h2 class="text-center" style="font-weight: bold">Monitoreo</h2>
        <div class="row position-relative">
                <div class="col-lg-4 text-center">
                    <img src="imgs/Temp.png" width="300" height="300" />
                    <hr />
                    <h3 class="fw-normal">Temperatura</h3>
                    <h1 style="font-weight: bold"><span id="temperatura"><?php echo $temperatura; ?>&deg;C</span></h1>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="imgs/HumAire.png" width="300" height="300" />
                    <hr />
                    <h3 class="fw-normal">Humedad del Aire</h3>
                    <h1 style="font-weight: bold"><span id="humedadAire"><?php echo $humedadAire; ?>%</span></h1>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="imgs/HumSuelo.png" width="300" height="300" />
                    <hr />
                    <h3 class="fw-normal">Humedad del Suelo</h3>
                    <h1 style="font-weight: bold"><span id="humedadSuelo"><?php echo $humedadSuelo; ?>%</span></h1>
                </div>
            </div>
    </div>
                <br/>
                <br/>
                <br/>
                <div class="text-center mt-3">
                    <?php if ($idPerfil == 1) { ?>
                        <a href="administrador.php" class="btn btn-dark btn-primario">Volver</a>
                    <?php } elseif ($idPerfil == 2) { ?>
                        <a href="trabajador.php" class="btn btn-dark btn-primario">Volver</a>
                    <?php } ?>
                    <a href="logout.php" class="btn btn-secondary ml-2">Cerrar sesión</a>
                </div>
</body>
</html>
