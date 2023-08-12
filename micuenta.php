<?php
session_start();

$host = "DIRECCION DEL HOST QUITADO POR SEGURIDAD";
$dbname = "NOMBRE DE BASE DE DATOS QUITADO POR SEGURIDAD";
$username = "USUARIO DE HOST QUITADO POR SEGURIDAD";
$password = "CONTRASENA DE HOST QUITADO POR SEGURIDAD";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error de conexión a la base de datos: " . $e->getMessage());
}

if (!isset($_SESSION['idPerfil'])) {
  header('Location: login.php'); // Redireccionar a la página de inicio de sesión si no se ha iniciado sesión
  exit();
}

$idPerfil = $_SESSION['idPerfil'];
$rut = $_SESSION['rut'];

$mensaje = ''; // Variable para almacenar el mensaje de alerta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nuevaContrasena = $_POST['nuevaContrasena'];
  $repetirContrasena = $_POST['repetirContrasena'];

  // Verificar que las contraseñas coincidan
  if ($nuevaContrasena !== $repetirContrasena) {
    $mensaje = 'Las contraseñas no coinciden';
  } else {
    // Verificar que la contraseña cumpla con los requisitos
    if (strlen($nuevaContrasena) < 8 ||
        !preg_match('/[A-Z]/', $nuevaContrasena) ||
        !preg_match('/[a-z]/', $nuevaContrasena) ||
        !preg_match('/[0-9]/', $nuevaContrasena)) {
      $mensaje = 'La contraseña no cumple con los requisitos';
    } else {
      // Verificar que la nueva contraseña no sea igual a la contraseña actual
      $query = "SELECT contraseña FROM Usuario WHERE rut = :rut";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':rut', $rut);
      $stmt->execute();
      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($nuevaContrasena === $usuario['contraseña']) {
        $mensaje = 'La nueva contraseña no puede ser igual a la contraseña actual';
      } else {
        // Actualizar la contraseña en la base de datos
        if (strlen($nuevaContrasena) > 30) {
          $mensaje = 'La nueva contraseña supera la longitud máxima permitida';
        } else {
          $query = "UPDATE Usuario SET contraseña = :contrasena WHERE rut = :rut";
          $stmt = $pdo->prepare($query);
          $stmt->bindParam(':contrasena', $nuevaContrasena);
          $stmt->bindParam(':rut', $rut);
          $stmt->execute();

          $mensaje = 'Contraseña cambiada exitosamente';

          // Redirigir a logout.php
          header('Location: logout.php');
          exit();
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mi Cuenta</title>
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
        <div class="wrapper">
            <h1 class="text-center">Mi Cuenta</h1>
            <div class="card">
                <?php if (!empty($mensaje)): ?>
                    <script>alert('<?php echo $mensaje; ?>');</script>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group row">
                        <label for="nuevaContrasena" class="col">Nueva Contraseña:</label>
                        <div class="col">
                        <input type="password" name="nuevaContrasena" maxlength="30" class="form-control" required>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="repetirContrasena" class="col">Repetir Contraseña Nueva:</label>
                    <div class="col">
                    <input type="password" name="repetirContrasena" maxlength="30" class="form-control" required>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-dark btn-primario">Confirmar</button>
                </form>
            </div>
        
                <br/>
                <button class="btn btn-secondary" onclick="volver()">Volver</button>
            </div>
                <script>
                    function volver() {
                    <?php if ($idPerfil == 1): ?>
                        location.href = "administrador.php";
                    <?php elseif ($idPerfil == 2): ?>
                        location.href = "trabajador.php";
                    <?php endif; ?>
                    }
                </script>
            
</body>
</html>
