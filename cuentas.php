<?php
session_start();

if (isset($_SESSION["idPerfil"]) && $_SESSION["idPerfil"] == 1) {
    // Código para el perfil de administrador
    if (isset($_POST["crearCuenta"])) {
        header("Location: crearCuenta.php");
        exit();
    } elseif (isset($_POST["editarCuenta"])) {
        header("Location: editarCuenta.php");
        exit();
    } elseif (isset($_POST["volver"])) {
        header("Location: administrador.php");
        exit();
    }
} else {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cuentas</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                <h1>Cuentas</h1>
                <hr/>
                <br/>
                <div class="d-flex justify-content-center align-items-start">
                    <img class="logo3" src="imgs/Terra.png" width="215" height="215"/>
                    <form method="post" action="" class="ml-4">
                        <ul class="list-group">
                            <br/>
                            <li class="list-group-item flex-fill"><input type="submit" class="btn btn-dark btn-primario" name="crearCuenta" value="Crear cuenta"></li>
                            <li class="list-group-item flex-fill"><input type="submit" class="btn btn-dark btn-primario" name="editarCuenta" value="Editar cuenta"></li>
                            <li class="list-group-item flex-fill"><input type="submit" class="btn btn-secondary" name="volver" value="Volver"></li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
