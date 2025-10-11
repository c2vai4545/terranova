<h1 class="text-center">Editar Cuenta</h1>
<div class="card p-3">
    <table class="table">
        <thead>
            <tr>
                <th>RUT</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Perfil</th>
                <th>Seleccionar</th>
                <th>Resetear clave</th>
            </tr>
        </thead>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['rut']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre1']); ?></td>
                <td><?php echo htmlspecialchars($usuario['apellido1']); ?></td>
                <td><?php echo htmlspecialchars((string)$usuario['idPerfil']); ?></td>
                <td class="text-center">
                    <input type="radio" name="usuario" value="<?php echo htmlspecialchars($usuario['rut']); ?>" onclick="mostrarFormulario('<?php echo htmlspecialchars($usuario['rut']); ?>')">
                </td>
                <td>
                    <button class="btn btn-dark" onclick="resetearContrasena('<?php echo htmlspecialchars($usuario['rut']); ?>')">Resetear Contraseña</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div id="formulario-edicion" style="display:none;">
        <h2>Editar Usuario</h2>
        <form method="POST" action="/cuentas/editar">
            <input type="hidden" id="rut" name="rut">
            <div class="row mb-2">
                <label class="col-2">Nombre 1:</label>
                <div class="col-4"><input type="text" id="nombre1" name="nombre1" class="form-control" required></div>
            </div>
            <div class="row mb-2">
                <label class="col-2">Nombre 2:</label>
                <div class="col-4"><input type="text" id="nombre2" name="nombre2" class="form-control"></div>
            </div>
            <div class="row mb-2">
                <label class="col-2">Apellido 1:</label>
                <div class="col-4"><input type="text" id="apellido1" name="apellido1" class="form-control" required></div>
            </div>
            <div class="row mb-2">
                <label class="col-2">Apellido 2:</label>
                <div class="col-4"><input type="text" id="apellido2" name="apellido2" class="form-control"></div>
            </div>
            <div class="row mb-3">
                <label class="col-2">Perfil:</label>
                <div class="col-4">
                    <select id="perfil" name="perfil" class="form-select"></select>
                </div>
            </div>
            <button type="submit" class="btn btn-dark">Guardar</button>
        </form>
    </div>
</div>
<br>
<a href="/admin" class="btn btn-secondary">Volver</a>

<script>
    function mostrarFormulario(rut) {
        fetch('/cuentas/usuario?rut=' + encodeURIComponent(rut))
            .then(r => r.json())
            .then(d => {
                const u = d.usuario,
                    perfiles = d.perfiles;
                document.getElementById('rut').value = u.rut;
                document.getElementById('nombre1').value = u.nombre1 || '';
                document.getElementById('nombre2').value = u.nombre2 || '';
                document.getElementById('apellido1').value = u.apellido1 || '';
                document.getElementById('apellido2').value = u.apellido2 || '';
                const select = document.getElementById('perfil');
                select.innerHTML = '';
                perfiles.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.idPerfil;
                    opt.text = p.nombrePerfil;
                    select.appendChild(opt);
                });
                select.value = u.idPerfil;
                document.getElementById('formulario-edicion').style.display = 'block';
            });
    }

    function resetearContrasena(rut) {
        fetch('/cuentas/reset?rut=' + encodeURIComponent(rut))
            .then(r => r.text())
            .then(t => alert(t));
    }
</script>