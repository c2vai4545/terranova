<?php
session_start();

// Verificar el acceso según el idPerfil
if (!isset($_SESSION['idPerfil'])) {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
    exit();
}

$idPerfil = $_SESSION['idPerfil'];
$rutCreador = $_SESSION['rut']; // Reemplazar con el campo adecuado que contiene el rut del usuario

// Verificar si se envió el formulario de creación de ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['problema'])) {
    $problema = $_POST['problema'];

    // Validar que el campo problema no esté vacío
    if (empty($problema)) {
        $error = "El campo problema es obligatorio";
    } else {
        // Guardar el ticket en la base de datos
        $host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
        $dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
        $username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
        $password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insertar el nuevo ticket en la tabla TicketSoporte
            $query = "INSERT INTO TicketSoporte (fechaCreacion, problema, creador) VALUES (CURRENT_DATE(), :problema, :creador)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':problema', $problema);
            $stmt->bindParam(':creador', $rutCreador);
            $stmt->execute();

            // Redireccionar a la página de tickets después de guardar el ticket
            header('Location: soporte.php');
            exit();
        } catch (PDOException $e) {
            echo "Error al guardar el ticket: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Ticket</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="wrapper">
        <h1 class="text-center">Crear Ticket</h1>
        <div class="card text-center">
                    
                    <form method="POST" action="crearTicket.php">
                        <div class="form-group row align-items-start">
                            <h3 class="col-5"><label for="problema">Detalles:</label></h3><br>
                        
                            <div class="col-7">
                            <textarea id="problema" name="problema" rows="4" cols="50" maxlength="500" class="form-control" required></textarea><br>

                            <?php if (isset($error)) : ?>
                                <p><?php echo $error; ?></p>
                            <?php endif; ?>
                            </div>
                        </div>
                        
                    <div class="text-center">
                        <hr/>
                        <button type="submit" class="btn btn-dark btn-primario">Ingresar</button>
                        <?php if ($idPerfil == 1) { ?>
                        <a href="administrador.php" class="btn btn-secondary">Volver</a>
                            <?php } elseif ($idPerfil == 2) { ?>
                        <a href="trabajador.php" class="btn btn-secondary">Volver</a>
                        <?php } ?>
                    </div>
                    </form>
                
        </div>
    </div>

</body>
</html>
