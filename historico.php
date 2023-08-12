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

// Obtener los tipos de lectura disponibles
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

// Consulta para obtener los tipos de lectura
$sqlTiposLectura = "SELECT idTipoLectura, nombre FROM TipoLectura";
$resultTiposLectura = $conn->query($sqlTiposLectura);

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Historico</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div>
        <h1>TerranovaGreenPUQ</h1>
    </div>
    <div class="container">
        <h1 class="text-center" style="font-weight: bold">Histórico</h1>
        <form method="POST" action="graficos.php">
            <div class="form-group row">
                <div class="col-6">
                <h3 for="fechaInicio" class="col">Fecha de inicio:</h3>
                <br/>
                <div class="col">
                <input type="date" class="text-center form-control" id="fechaInicio" name="fechaInicio" required>
                </div>
            </div>

            <div class="col-6">
                <h3 for="fechaFin" class="col">Fecha de término:</h3>
                <br/>
                <div class="col">
                <input type="date" class="text-center form-control" id="fechaFin" name="fechaFin" required>
                </div>
            </div>
            </div>
            <div class="card">
                <h3>Seleccione cual tipo de Lectura desea ver:</h3>
                <hr/>
                
                <?php while ($row = $resultTiposLectura->fetch_assoc()) { ?>
                <div class="form-group row justify-content-md-center">
                    <div class="text-left col-3">
                    <h5 for="<?php echo $row['nombre']; ?>" class="font-weight-bold"><?php echo $row['nombre']; ?>:</h5>
                    </div>
                    <div class="col-1">
                    <input type="checkbox" id="<?php echo $row['nombre']; ?>" name="tiposLectura[]" value="<?php echo $row['idTipoLectura']; ?>">
                    </div>
                </div>
                <?php } ?>
                
                    <button type="submit" class="btn btn-dark btn-primario">Mostrar</button>
                
            </div>
        </form>
    </div>
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