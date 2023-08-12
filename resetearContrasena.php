<?php
session_start();
if ($_SESSION['perfil'] !== 'administrador') {
    header('Location: administrador.php');
    exit();
}

// Verificar si se pasó el parámetro "rut" en la URL
if (!isset($_GET['rut'])) {
    header('Location: editarCuenta.php');
    exit();
}

$rut = $_GET['rut'];

// Conexión a la base de datos
$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Resetear la contraseña del usuario
    $query = "UPDATE Usuario SET contraseña = 'Terranova.2023' WHERE rut = :rut";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':rut', $rut);
    $stmt->execute();

    echo "Contraseña reseteada exitosamente.";
} catch(PDOException $e) {
    echo "Error al resetear la contraseña: " . $e->getMessage();
}

$conn = null;
?>
