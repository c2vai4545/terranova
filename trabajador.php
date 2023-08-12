<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["rut"]) || !isset($_SESSION["idPerfil"])) {
    header("Location: login.php");
    exit();
}

// Obtener el rut y perfil del usuario autenticado
$rut = $_SESSION["rut"];
$idPerfil = $_SESSION["idPerfil"];

// Realizar la verificación de inicio de sesión aquí
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

// Consultar la tabla Usuario para obtener los datos del usuario
$sql = "SELECT nombre1, apellido1 FROM Usuario WHERE rut = '$rut'";
$result = $conn->query($sql);

// Verificar si se encontró un usuario válido
if ($result->num_rows == 1) {
    // Obtener los datos del usuario
    $row = $result->fetch_assoc();
    $nombre1 = $row["nombre1"];
    $apellido1 = $row["apellido1"];
} else {
    // No se encontró un usuario válido
    $conn->close();
    die("Error: No se encontró un usuario válido.");
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Trabajador</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center">Bienvenido/a, <?php echo $nombre1 . " " . $apellido1; ?></h2>
                <div class="image-container">
                    <img src="imgs/Terra.png" width="50" height="50" class="img-fluid">
                </div>
                <hr/>
                <h3 class="text-center">Funciones:</h3>
                <br/>
                <br/>
                
                <ul class="list-group list-group-horizontal">
                    <li class="list-group-item flex-fill"><a href="monitoreo.php" class="btn btn-dark btn-primario">Monitoreo en Tiempo Real</a></li>
                    <li class="list-group-item flex-fill"><a href="micuenta.php" class="btn btn-dark btn-primario">Mi Cuenta</a></li>
                    <li class="list-group-item flex-fill"><a href="soporte.php" class="btn btn-dark btn-primario">Soporte</a></li>
                </ul>
                <img src="imgs/inv.png" width="350" height="350" class="rounded mx-auto d-block">       
                <div class="text-center mt-3">
                    <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
