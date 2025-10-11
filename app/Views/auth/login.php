<h1 class="text-center">Iniciar sesión</h1>
<?php if (!empty($error ?? '')): ?>
    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form method="post" action="/login">
                    <div class="mb-3">
                        <label for="rut" class="form-label">RUT (8 dígitos)</label>
                        <input type="text" class="form-control" id="rut" name="rut" pattern="\d{8}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-dark">Iniciar sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php /**/ ?>