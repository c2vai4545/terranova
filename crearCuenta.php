<?php
session_start();
if (!isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] !== '1') {
    header('Location: administrador.php');
    exit();
}

// Conexión a la base de datos
$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los perfiles
    $query = "SELECT idPerfil, nombrePerfil FROM Perfil";
    $stmt = $conn->query($query);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error al obtener los datos: " . $e->getMessage();
}

$conn = null;

// Variables para mostrar la alerta
$alerta = false;
$alertaMensaje = "";

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario
    $rut = $_POST['rut'];
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $perfil = $_POST['perfil'];

    // Validar los campos obligatorios
    if (empty($rut) || empty($nombre1) || empty($apellido1) || empty($perfil)) {
        $alerta = true;
        $alertaMensaje = "Por favor, completa los campos obligatorios.";
    }

    // Validar formato del RUT
    if (!preg_match('/^[0-9]{8}$/', $rut)) {
        $alerta = true;
        $alertaMensaje = "El RUT debe contener 8 dígitos numéricos.";
    }

    if (!$alerta) {
        // Conexión a la base de datos
        $host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
        $dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
        $username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
        $password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si el RUT ya existe en la base de datos
            $query = "SELECT COUNT(*) AS count FROM Usuario WHERE rut = :rut";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':rut', $rut);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $alerta = true;
                $alertaMensaje = "El RUT ingresado ya existe.";
            } else {
                // Insertar los datos en la tabla "Usuario"
                $query = "INSERT INTO Usuario (rut, nombre1, nombre2, apellido1, apellido2, idPerfil, contraseña) VALUES (:rut, :nombre1, :nombre2, :apellido1, :apellido2, :idPerfil, 'Terranova.2023')";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':rut', $rut);
                $stmt->bindParam(':nombre1', $nombre1);
                $stmt->bindParam(':nombre2', $nombre2);
                $stmt->bindParam(':apellido1', $apellido1);
                $stmt->bindParam(':apellido2', $apellido2);
                $stmt->bindParam(':idPerfil', $perfil);
                $stmt->execute();

                $alertaMensaje = "Cuenta creada exitosamente.";
            }
        } catch(PDOException $e) {
            $alerta = true;
            $alertaMensaje = "Error al crear la cuenta: " . $e->getMessage();
        }

        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Cuenta</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    
    
    <?php if ($alerta): ?>
        <script>alert("<?php echo $alertaMensaje; ?>");</script>
    <?php endif; ?>
    <div class="wrapper">
        <h1 class="text-center">Crear Cuenta</h1>
        <div class="card">
                    <form method="POST" action="">
                        <div class="form-group row">
                            <label class="col-sm-5">RUT sin dígito verificador:</label>
                            <div class="col-sm-6">
                            <input type="text" name="rut" pattern="[0-9]{8}" class="form-control" required><br>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-5">Primer Nombre:</label>
                            <div class="col-sm-6">
                            <input type="text" name="nombre1" class="form-control" required><br>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5">Segundo Nombre:</label>
                            <div class="col-sm-6">
                            <input type="text" name="nombre2" class="form-control"><br>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5">Apellido Paterno:</label>
                            <div class="col-sm-6">
                            <input type="text" name="apellido1" class="form-control" required><br>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5">Apellido Materno:</label>
                            <div class="col-sm-6">
                            <input type="text" name="apellido2" class="form-control"><br>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5">Perfil:</label>
                            <div class="col-sm-6">
                            <select name="perfil" class="custom-select" required>
                                <option value="">Seleccionar Perfil</option>
                                <?php foreach ($perfiles as $perfil): ?>
                                    <option value="<?php echo $perfil['idPerfil']; ?>">
                                        <?php echo $perfil['nombrePerfil']; ?>
                                    </option>
                                <?php endforeach; ?>
                                </select><br>
                            </div>
                        </div>
                        <div class="text-center">
                            <hr/>
                            <input type="submit" class="btn btn-dark btn-primario mt-2" value="Guardar">
                            <a href="cuentas.php" class="btn btn-secondary btn-volver mt-2">Volver</a>
                        </div>
                    </form>
        </div>
    </div>
    
</body>
</html>
