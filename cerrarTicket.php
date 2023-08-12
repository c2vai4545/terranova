<?php
session_start();

// Verificar el acceso según el idPerfil
if (!isset($_SESSION['idPerfil'])) {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
    exit();
}

$idPerfil = $_SESSION['idPerfil'];

// Verificar si se envió el formulario de cierre de ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['respuesta'])) {
    $idTicket = $_POST['id'];
    $respuesta = $_POST['respuesta'];
    $solucionador = $_SESSION['rut']; // Obtener el rut del solucionador desde la sesión

    // Validar que los campos no estén vacíos
    if (empty($idTicket) || empty($respuesta)) {
        echo "Error: Los campos son obligatorios";
        exit();
    }

    // Actualizar el ticket en la base de datos
    $host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
    $dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
    $username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
    $password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Actualizar el ticket con la respuesta, la fecha de respuesta y el solucionador
        $query = "UPDATE TicketSoporte SET respuesta = :respuesta, fechaRespuesta = CURRENT_DATE(), solucionador = :solucionador WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':respuesta', $respuesta);
        $stmt->bindParam(':solucionador', $solucionador);
        $stmt->bindParam(':id', $idTicket);
        $stmt->execute();

        echo "Ticket cerrado exitosamente";
    } catch (PDOException $e) {
        echo "Error al cerrar el ticket: " . $e->getMessage();
    }
} else {
    echo "Error: No se recibieron los datos correctamente";
}
