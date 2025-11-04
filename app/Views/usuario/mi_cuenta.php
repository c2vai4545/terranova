<div class="wrapper">
    <h1 class="text-center">Mi Cuenta</h1>
    <div class="card">
        <?php if (!empty($mensaje ?? '')): ?>
            <div id="mensaje-alert" data-message="<?php echo htmlspecialchars($mensaje); ?>"></div>
        <?php endif; ?>
        <form method="POST" action="/micuenta">
            <div class="form-group row">
                <label class="col">Nueva Contraseña:</label>
                <div class="col"><input type="password" name="nuevaContrasena" maxlength="30" class="form-control" required></div>
            </div>
            <div class="form-group row">
                <label class="col">Repetir Contraseña Nueva:</label>
                <div class="col"><input type="password" name="repetirContrasena" maxlength="30" class="form-control" required></div>
            </div>
            <button type="submit" class="btn btn-dark btn-primario">Confirmar</button>
        </form>
    </div>
    <br />
    <button id="volver-btn" class="btn btn-secondary" data-redirect="<?php echo ((string)($_SESSION['idPerfil'] ?? '') === '1') ? '/admin' : '/worker'; ?>">Volver</button>
</div>
<script src="/js/usuario_mi_cuenta.js"></script>