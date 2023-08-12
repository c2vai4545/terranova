<?php
session_start();

// Verificar si ya hay una sesión iniciada
if (isset($_SESSION['rut']) && isset($_SESSION['idPerfil'])) {
    $idPerfil = $_SESSION['idPerfil'];

    // Redirigir al perfil correspondiente
    if ($idPerfil == 1) {
        header("Location: administrador.php");
        exit();
    } elseif ($idPerfil == 2) {
        header("Location: trabajador.php");
        exit();
    }
}

// Verificar si se envió el formulario de inicio de sesión
if (isset($_POST["rut"]) && isset($_POST["contrasena"])) {
    // Obtener los datos de inicio de sesión enviados por el formulario
    $rut = $_POST["rut"];
    $contrasena = $_POST["contrasena"];

    // Validar la longitud del rut
    if (strlen($rut) !== 8) {
        $error = "El RUT debe tener exactamente 8 caracteres.";
    } elseif (!ctype_digit($rut)) {
        $error = "El RUT debe contener solo números.";
    } else {
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

        // Consultar la tabla Usuario para verificar los datos de inicio de sesión
        $sql = "SELECT idPerfil FROM Usuario WHERE rut = '$rut' AND contraseña = '$contrasena'";
        $result = $conn->query($sql);

        // Verificar si se encontró un usuario válido
        if ($result->num_rows == 1) {
            // Obtener el perfil del usuario
            $row = $result->fetch_assoc();
            $idPerfil = $row["idPerfil"];

            // Guardar la información de inicio de sesión en la sesión
            $_SESSION["rut"] = $rut;
            $_SESSION["idPerfil"] = $idPerfil;

            // Redirigir al perfil correspondiente
            if ($idPerfil == 1) {
                header("Location: administrador.php");
                exit();
            } elseif ($idPerfil == 2) {
                header("Location: trabajador.php");
                exit();
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }

        // Cerrar la conexión a la base de datos
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesión</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Iniciar sesión</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="login.php">
                            <div class="form-group">
                                <label for="rut">RUT:</label>
                                <input type="text" class="form-control" id="rut" name="rut" pattern="\d{8}" required>
                                <small class="form-text text-muted">El RUT debe tener 8 dígitos numéricos.</small>
                            </div>
                            <div class="form-group">
                                <label for="contrasena">Contraseña:</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-dark btn-block btn-iniciar-sesion">Iniciar sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="piepag">
        <footer class="py-3 my-4">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3"></ul>
            <img class="logo2" src="imgs/Terra.png" width="40" height="40" />
            <p class="text-center text-muted">© 2023 TerranovaGreenPUQ</p>
        </footer>
    </div>
</body>
</html>
