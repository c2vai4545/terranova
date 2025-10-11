<h1 class="text-center">Mi Cuenta</h1>
<?php if (!empty($mensaje ?? '')): ?>
    <div class="alert alert-info text-center"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>
<div class="card p-3">
    <form method="POST" action="/micuenta">
        <div class="row mb-2">
            <label class="col">Nueva Contraseña:</label>
            <div class="col"><input type="password" name="nuevaContrasena" maxlength="30" class="form-control" required></div>
        </div>
        <div class="row mb-2">
            <label class="col">Repetir Contraseña Nueva:</label>
            <div class="col"><input type="password" name="repetirContrasena" maxlength="30" class="form-control" required></div>
        </div>
        <button type="submit" class="btn btn-dark">Confirmar</button>
    </form>
</div>
<br>
<button class="btn btn-secondary" onclick="location.href='<?php echo ((string)($_SESSION['idPerfil'] ?? '') === '1') ? '/admin' : '/worker'; ?>'">Volver</button>