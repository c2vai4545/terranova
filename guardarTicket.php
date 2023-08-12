<?php
session_start();

// Verificar el acceso según el idPerfil
if (!isset($_SESSION['idPerfil'])) {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
    exit();
}

$idPerfil = $_SESSION['idPerfil'];

// Verificar el acceso a la página según el idPerfil
if ($idPerfil !== '1') {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si el idPerfil no es válido
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['problema'])) {
    $problema = $_POST['problema'];
    $creador = $_SESSION['rut']; // Reemplazar con el campo adecuado que contiene el rut del usuario

    $host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
    $dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
    $username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
    $password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertar el nuevo ticket de soporte
        $query = "INSERT INTO TicketSoporte (fechaCreacion, problema, creador) VALUES (CURRENT_DATE(), :problema, :creador)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':problema', $problema);
        $stmt->bindParam(':creador', $creador);
        $stmt->execute();

        // Redireccionar a la página de tickets de soporte
        header('Location: ticketSoporte.php');
        exit();
    } catch (PDOException $e) {
        echo "Error al guardar el ticket: " . $e->getMessage();
    }
}
?>
