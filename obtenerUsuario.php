<?php
// Conexión a la base de datos
$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el usuario seleccionado
    $rut = $_GET['rut'];
    $query = "SELECT rut, nombre1, nombre2, apellido1, apellido2, Usuario.idPerfil, Perfil.nombrePerfil AS perfil
              FROM Usuario
              LEFT JOIN Perfil ON Usuario.idPerfil = Perfil.idPerfil
              WHERE rut = :rut";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':rut', $rut);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener todos los perfiles
    $query = "SELECT idPerfil, nombrePerfil FROM Perfil";
    $stmt = $conn->query($query);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combinar los datos del usuario y los perfiles en un solo arreglo
    $resultado = [
        'usuario' => $usuario,
        'perfiles' => $perfiles
    ];

    // Devolver el resultado en formato JSON
    header('Content-Type: application/json');
    echo json_encode($resultado);
} catch(PDOException $e) {
    echo "Error al obtener el usuario: " . $e->getMessage();
}

$conn = null;
?>

