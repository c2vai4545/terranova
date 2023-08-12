<?php
// Obtener los nuevos datos de la base de datos y devolverlos en formato JSON
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

// Consulta para obtener los nuevos datos de temperatura, humedad de aire y humedad de suelo de la tabla Temporal
$sql = "SELECT temperatura, humedadAire, humedadSuelo FROM Temporal";
$result = $conn->query($sql);

// Verificar si se encontraron registros
if ($result->num_rows > 0) {
    // Obtener el primer registro
    $row = $result->fetch_assoc();

    // Crear un array con los nuevos datos
    $data = array(
        "temperatura" => $row["temperatura"],
        "humedadAire" => $row["humedadAire"],
        "humedadSuelo" => $row["humedadSuelo"]
    );

    // Devolver los nuevos datos en formato JSON
    echo json_encode($data);
} else {
    // Si no se encontraron registros, devolver un array vacío
    echo json_encode(array());
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
