<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener las fechas de inicio y fin del formulario
    $fechaInicio = $_POST["fechaInicio"];
    $fechaFin = $_POST["fechaFin"];

    // Obtener los tipos de lectura seleccionados
    $tiposLectura = $_POST["tiposLectura"];

    // Obtener los datos de la base de datos para los tipos de lectura seleccionados
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

    // Obtener los datos de lectura para los tipos seleccionados
    $lecturas = array();
    foreach ($tiposLectura as $index => $tipoLectura) {
        $sqlLecturas = "SELECT fechaLectura, horaLectura, lectura FROM Lectura WHERE idTipoLectura = '$tipoLectura' AND fechaLectura >= '$fechaInicio' AND fechaLectura <= '$fechaFin'";
        $resultLecturas = $conn->query($sqlLecturas);

        // Guardar los resultados en un arreglo asociativo
        $lecturas[$index] = array(
            "tipoLectura" => $tipoLectura,
            "data" => array()
        );

        while ($row = $resultLecturas->fetch_assoc()) {
            $lecturas[$index]["data"][] = array(
                "fecha" => $row["fechaLectura"],
                "hora" => $row["horaLectura"],
                "lectura" => $row["lectura"]
            );
        }
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gráficos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <?php if (isset($lecturas)) { ?>
        <?php foreach ($lecturas as $index => $lectura) { ?>
            <?php $nombreTipoLectura = obtenerNombreTipoLectura($lectura["tipoLectura"]); ?>
        <h1 class="text-center"><?php echo $nombreTipoLectura; ?></h1>
            <div class="container">
                <div class="card">
                    <div class="row">
                        <div class="col-8">
                            <canvas id="grafico_<?php echo $index; ?>"></canvas>
                            <br/>
                        </div>
                        <div class="col-4">
                            <table class="table">
                            <thead>
                            <tr class="table-dark">
                                <th>Fecha Lectura</th>
                                <th>Hora Lectura</th>
                                <th>Lectura</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($lectura["data"] as $dato) { ?>
                                <tr class="table-success">
                                    <td><?php echo $dato["fecha"]; ?></td>
                                    <td><?php echo $dato["hora"]; ?></td>
                                    <td><?php echo $dato["lectura"]; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                <br/>
                <script>
                    var ctx_<?php echo $index; ?> = document.getElementById('grafico_<?php echo $index; ?>').getContext('2d');
                    var grafico_<?php echo $index; ?> = new Chart(ctx_<?php echo $index; ?>, {
                        type: 'line',
                        data: {
                            labels: [<?php echo obtenerFechas($lectura["data"]); ?>],
                            datasets: [{
                                label: [<?php echo obtenerFechas($lectura["data"]); ?>],
                                data: [<?php echo obtenerValores($lectura["data"]); ?>],
                                backgroundColor: 'transparent',
                                borderColor: getRandomColor(),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    function getRandomColor() {
                        var letters = '0123456789ABCDEF';
                        var color = '#';
                        for (var i = 0; i < 6; i++) {
                            color += letters[Math.floor(Math.random() * 16)];
                        }
                        return color;
                    }
                </script>
    
        <?php } ?>
    <?php } ?>
    <div class="text-center d-grid gap-2 col-6 mx-auto">
    <a href="historico.php" class="btn btn-dark btn-primario btn-lg">Volver</a>
    </div>
    <br/>
    <br/>
    <?php
    function obtenerNombreTipoLectura($idTipoLectura)
    {
        // Obtener el nombre del tipo de lectura desde la base de datos o cualquier otra fuente de datos
        $servername = "localhost";
        $username = "id20883033_c2vai4545";
        $password = "NacionalPUQ1899!";
        $dbname = "id20883033_terranova";

        // Crear la conexión a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar si hay errores en la conexión
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $sql = "SELECT nombre FROM TipoLectura WHERE idTipoLectura = '$idTipoLectura'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["nombre"];
        }

        // Cerrar la conexión a la base de datos
        $conn->close();

        return "";
    }

    function obtenerFechas($datosLectura)
    {
        $fechas = array();
        foreach ($datosLectura as $dato) {
            $fechas[] = '"' . $dato["fecha"] . ' ' . $dato["hora"] . '"';
        }
        return implode(',', $fechas);
    }

    function obtenerValores($datosLectura)
    {
        $valores = array();
        foreach ($datosLectura as $dato) {
            $valores[] = $dato["lectura"];
        }
        return implode(',', $valores);
    }
    ?>
</body>
</html>
