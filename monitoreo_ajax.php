<?php
// Obtener los datos de temperatura, humedad de aire y humedad de suelo de la tabla Temporal
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

// Consulta para obtener los datos de temperatura, humedad de aire y humedad de suelo de la tabla Temporal
$sql = "SELECT temperatura, humedadAire, humedadSuelo FROM Temporal";
$result = $conn->query($sql);

// Verificar si se encontraron registros
if ($result->num_rows > 0) {
    // Obtener el primer registro
    $row = $result->fetch_assoc();

    // Crear un array con los datos
    $datos = array(
        "temperatura" => $row["temperatura"],
        "humedadAire" => $row["humedadAire"],
        "humedadSuelo" => $row["humedadSuelo"]
    );

    // Convertir el array a formato JSON
    $jsonDatos = json_encode($datos);

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo $jsonDatos;
} else {
    // Si no se encontraron registros, devolver un objeto JSON con valores nulos
    $datos = array(
        "temperatura" => null,
        "humedadAire" => null,
        "humedadSuelo" => null
    );

    // Convertir el array a formato JSON
    $jsonDatos = json_encode($datos);

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo $jsonDatos;
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
