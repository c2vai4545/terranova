<?php
// Conexión a la base de datos
$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del formulario
    $rut = $_POST['rut'];
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $perfil = $_POST['perfil'];

    // Actualizar los datos del usuario en la base de datos
    $query = "UPDATE Usuario
              SET nombre1 = :nombre1,
                  nombre2 = :nombre2,
                  apellido1 = :apellido1,
                  apellido2 = :apellido2,
                  idPerfil = :perfil
              WHERE rut = :rut";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nombre1', $nombre1);
    $stmt->bindParam(':nombre2', $nombre2);
    $stmt->bindParam(':apellido1', $apellido1);
    $stmt->bindParam(':apellido2', $apellido2);
    $stmt->bindParam(':perfil', $perfil);
    $stmt->bindParam(':rut', $rut);
    $stmt->execute();

    // Redirigir a editarCuenta.php
    header('Location: editarCuenta.php');
    exit();
} catch(PDOException $e) {
    echo "Error al guardar la edición: " . $e->getMessage();
}

$conn = null;
?>
