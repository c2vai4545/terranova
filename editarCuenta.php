<?php
session_start();
if ($_SESSION['idPerfil'] !== '1') {
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

    // Obtener los usuarios de la tabla "Usuario"
    $query = "SELECT rut, nombre1, nombre2, apellido1, apellido2, Perfil.nombrePerfil AS perfil FROM Usuario
              LEFT JOIN Perfil ON Usuario.idPerfil = Perfil.idPerfil";
    $stmt = $conn->query($query);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
}

$conn = null;

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rut']) && !empty($_POST['rut'])) {
        $rut = $_POST['rut'];
        $nombre1 = $_POST['nombre1'];
        $nombre2 = $_POST['nombre2'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $perfil = $_POST['perfil'];

        // Actualizar los campos del usuario en la base de datos
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE Usuario SET nombre1 = :nombre1, nombre2 = :nombre2, apellido1 = :apellido1, apellido2 = :apellido2, idPerfil = :perfil WHERE rut = :rut";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':rut', $rut);
            $stmt->bindParam(':nombre1', $nombre1);
            $stmt->bindParam(':nombre2', $nombre2);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':perfil', $perfil);
            $stmt->execute();

            echo "Los datos del usuario se han actualizado correctamente.";
        } catch(PDOException $e) {
            echo "Error al actualizar los datos del usuario: " . $e->getMessage();
        }

        $conn = null;
    }
}

// Procesar formulario de reseteo de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rut-reset']) && !empty($_POST['rut-reset'])) {
        $rutReset = $_POST['rut-reset'];
        
        // Actualizar la contraseña del usuario en la base de datos
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE Usuario SET contraseña = 'Terranova.2023' WHERE rut = :rut";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':rut', $rutReset);
            $stmt->execute();

            echo "La contraseña del usuario se ha reseteado correctamente.";
        } catch(PDOException $e) {
            echo "Error al resetear la contraseña del usuario: " . $e->getMessage();
        }

        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cuenta</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="wrapper">
        <h1 class="text-center">Editar Cuenta</h1>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">RUT</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Perfil</th>
                    <th scope="col">Seleccionar</th>
                    <th scope="col">Resetear clave</th>
                    
                </tr>
                </thead>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['rut']; ?></td>
                        <td><?php echo $usuario['nombre1']; ?></td>
                        <td><?php echo $usuario['apellido1']; ?></td>
                        <td><?php echo $usuario['perfil']; ?></td>
                        <td class="text-center">
                            <input type="radio" name="usuario" value="<?php echo $usuario['rut']; ?>"
                                onclick="mostrarFormulario('<?php echo $usuario['rut']; ?>')">
                        </td>
                        <td>
                            <input type="button" class="btn btn-dark btn-primario" value="Resetear Contraseña" onclick="resetearContraseña('<?php echo $usuario['rut']; ?>')">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div id="formulario-edicion" style="display: none;">
                <h2>Editar Usuario</h2>
                <br/>
                <form id="editar-form" method="POST" action="">
                    <div class="form-group row">
                    <input type="hidden" id="rut" name="rut">
                    <label for="nombre1" class="col-2">Nombre 1:</label>
                    <div class="col-4">
                    <input type="text" id="nombre1" name="nombre1" class="form-control" required>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="nombre2" class="col-2">Nombre 2:</label>
                    <div class="col-4">
                    <input type="text" id="nombre2" name="nombre2" class="form-control">
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="apellido1" class="col-2">Apellido 1:</label>
                    <div class="col-4">
                    <input type="text" id="apellido1" name="apellido1" class="form-control" required>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="apellido2" class="col-2">Apellido 2:</label>
                    <div class="col-4">
                    <input type="text" id="apellido2" name="apellido2" class="form-control">
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="perfil" class="col-2">Perfil:</label>
                    <div class="col-4">
                    <select id="perfil" name="perfil" class="custom-select">
                        <option value="">Seleccione un perfil</option>
                    </select>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-dark btn-primario">Guardar</button>
                </form>
            </div>

            <div id="resetear-contraseña" style="display: none;">
                <br/>
                <h2>Resetear Contraseña</h2>
                <form id="resetear-contraseña-form" method="POST" action="">
                    <input type="hidden" id="rut-reset" name="rut-reset">
                    <p>¿Está seguro de que desea resetear la contraseña del usuario seleccionado?</p>
                    <button type="submit" class="btn btn-warning">Resetear Contraseña</button>
                </form>
            </div>
        </div>
        <br>
            <a href="administrador.php" class="btn btn-secondary">Volver</a>
    </div>
    <script>
        function mostrarFormulario(rut) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'obtenerUsuario.php?rut=' + rut, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resultado = JSON.parse(xhr.responseText);
                    var usuario = resultado.usuario;
                    var perfiles = resultado.perfiles;

                    document.getElementById("rut").value = usuario.rut;
                    document.getElementById("nombre1").value = usuario.nombre1;
                    document.getElementById("nombre2").value = usuario.nombre2;
                    document.getElementById("apellido1").value = usuario.apellido1;
                    document.getElementById("apellido2").value = usuario.apellido2;

                    var perfilSelect = document.getElementById("perfil");
                    perfilSelect.innerHTML = ""; // Limpiar opciones existentes

                    // Agregar opciones de perfil
                    for (var i = 0; i < perfiles.length; i++) {
                        var option = document.createElement("option");
                        option.value = perfiles[i].idPerfil;
                        option.text = perfiles[i].nombrePerfil;
                        perfilSelect.appendChild(option);
                    }

                    // Seleccionar el perfil del usuario
                    perfilSelect.value = usuario.idPerfil;

                    document.getElementById("formulario-edicion").style.display = "block";
                }
            };
            xhr.send();
        }

        function resetearContraseña(rut) {
            document.getElementById("rut-reset").value = rut;
            document.getElementById("resetear-contraseña").style.display = "block";
        }
    </script>
</body>
</html>
