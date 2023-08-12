<?php
session_start();

// Verificar el acceso según el idPerfil
if (!isset($_SESSION['idPerfil'])) {
    header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
    exit();
}

$idPerfil = $_SESSION['idPerfil'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Soporte</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                <h1>Soporte</h1>
                <hr/>
                <br/>
                <div class="d-flex justify-content-center align-items-start">
                    
                    
                        <?php if ($idPerfil === '1'): ?>
                        <img class="logo3" src="imgs/Terra.png" width="200" height="200"/>
                            <div class="list-group mt-3">
                                <li class="list-group-item flex-fill"><button onclick="window.location.href='crearTicket.php'" class="btn btn-dark btn-primario">Crear Ticket</button></li>
                                <li class="list-group-item flex-fill"><button onclick="window.location.href='ticketSoporte.php'" class="btn btn-dark btn-primario" >Ver Tickets</button></li>
                                <li class="list-group-item flex-fill"><button onclick="window.location.href='administrador.php'" class="btn btn-secondary" >Volver</button></li>
                            </div>
                        <?php elseif ($idPerfil === '2'): ?>
                        <img class="logo3" src="imgs/Terra.png" width="200" height="200"/>
                            <div class="list-group mt-3">
                                <li class="list-group-item flex-fill"><button class="btn btn-dark btn-primario" onclick="window.location.href='crearTicket.php'">Crear Ticket</button></li>
                                <li class="list-group-item flex-fill"><button class="btn btn-dark btn-primario" onclick="window.location.href='misTickets.php'">Mis Tickets</button></li>
                                <li class="list-group-item flex-fill"><button class="btn btn-secondary" onclick="window.location.href='trabajador.php'">Volver</button></li>
                            </div>
                        <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
