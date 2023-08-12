<?php
// Obtener los datos enviados
$temp = $_POST['temp'];
$humSue = $_POST['humSue'];
$humAir = $_POST['humAir'];

// Conexión a la base de datos
$servername = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo "Error en la conexión a la base de datos: " . $conn->connect_error;
    exit;
}

// Establecer el huso horario a Punta Arenas, Chile
date_default_timezone_set('America/Punta_Arenas');

// Eliminar los datos existentes en la tabla Temporal
$sql_delete = "TRUNCATE TABLE Temporal";
if ($conn->query($sql_delete) !== TRUE) {
    echo "Error al eliminar los datos de la tabla Temporal: " . $conn->error;
    exit;
}

// Insertar los datos en la tabla Temporal
$sql_insert = "INSERT INTO Temporal (temperatura, humedadAire, humedadSuelo) VALUES ('$temp', '$humAir', '$humSue')";
if ($conn->query($sql_insert) !== TRUE) {
    echo "Error al guardar los datos en la tabla Temporal: " . $conn->error;
    exit;
}

// Verificar la última vez que se guardaron los datos en la tabla Lectura
$sql_last_entry = "SELECT fechaLectura, horaLectura FROM Lectura ORDER BY fechaLectura DESC, horaLectura DESC LIMIT 1";
$result_last_entry = $conn->query($sql_last_entry);

if ($result_last_entry->num_rows > 0) {
    $row = $result_last_entry->fetch_assoc();
    $last_entry_timestamp = strtotime($row['fechaLectura'] . ' ' . $row['horaLectura']);
    $current_timestamp = time();

    // Verificar si han pasado al menos 2 horas desde el último registro
    $elapsed_time = $current_timestamp - $last_entry_timestamp;
    $elapsed_hours = floor($elapsed_time / (60 * 60));

    if ($elapsed_hours >= 2) {
        // Insertar los datos en la tabla Lectura
        $date = date("Y-m-d"); // Obtener la fecha actual
        $time = date("H:i:s"); // Obtener la hora actual

        // Insertar la variable 'temp' en idTipoLectura 1
        $sql_temp = "INSERT INTO Lectura (idTipoLectura, fechaLectura, horaLectura, lectura) VALUES (1, '$date', '$time', '$temp')";
        if ($conn->query($sql_temp) !== TRUE) {
            echo "Error al guardar la variable 'temp': " . $conn->error;
            exit;
        }

        // Insertar la variable 'humSue' en idTipoLectura 3
        $sql_humSue = "INSERT INTO Lectura (idTipoLectura, fechaLectura, horaLectura, lectura) VALUES (3, '$date', '$time', '$humSue')";
        if ($conn->query($sql_humSue) !== TRUE) {
            echo "Error al guardar la variable 'humSue': " . $conn->error;
            exit;
        }

        // Insertar la variable 'humAir' en idTipoLectura 2
        $sql_humAir = "INSERT INTO Lectura (idTipoLectura, fechaLectura, horaLectura, lectura) VALUES (2, '$date', '$time', '$humAir')";
        if ($conn->query($sql_humAir) !== TRUE) {
            echo "Error al guardar la variable 'humAir': " . $conn->error;
            exit;
        }
    }
}

// Cerrar la conexión
$conn->close();

// Mostrar mensaje de éxito
echo "Los datos se guardaron correctamente en la base de datos.";
?>
