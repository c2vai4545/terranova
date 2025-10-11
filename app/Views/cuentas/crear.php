<div class="wrapper">
    <h1 class="text-center">Crear Cuenta</h1>
    <div class="card">
        <form method="POST" action="/cuentas/crear">
            <div class="row mb-2">
                <label class="col-sm-5">RUT sin dígito verificador:</label>
                <div class="col-sm-6"><input type="text" name="rut" pattern="\d{8}" class="form-control" required></div>
            </div>
            <div class="row mb-2">
                <label class="col-sm-5">Primer Nombre:</label>
                <div class="col-sm-6"><input type="text" name="nombre1" class="form-control" required></div>
            </div>
            <div class="row mb-2">
                <label class="col-sm-5">Segundo Nombre:</label>
                <div class="col-sm-6"><input type="text" name="nombre2" class="form-control"></div>
            </div>
            <div class="row mb-2">
                <label class="col-sm-5">Apellido Paterno:</label>
                <div class="col-sm-6"><input type="text" name="apellido1" class="form-control" required></div>
            </div>
            <div class="row mb-2">
                <label class="col-sm-5">Apellido Materno:</label>
                <div class="col-sm-6"><input type="text" name="apellido2" class="form-control"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-5">Perfil:</label>
                <div class="col-sm-6">
                    <select name="perfil" class="form-select" required>
                        <option value="">Seleccionar Perfil</option>
                        <?php foreach ($perfiles as $perfil): ?>
                            <option value="<?php echo htmlspecialchars($perfil['idPerfil']); ?>"><?php echo htmlspecialchars($perfil['nombrePerfil']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="text-center">
                <hr />
                <button type="submit" class="btn btn-dark btn-primario mt-2">Guardar</button>
                <a href="/cuentas" class="btn btn-secondary btn-volver mt-2">Volver</a>
            </div>
        </form>
    </div>
</div>