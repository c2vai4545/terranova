<div class="container mt-5">
    <div class="card">
        <div class="card-body text-center">
            <h1>Cuentas</h1>
            <hr />
            <br />
            <div class="d-flex justify-content-center align-items-start">
                <img class="logo3" src="/imgs/Terra.png" width="215" height="215" />
                <div class="ml-4 w-100">
                    <ul class="list-group list-group-horizontal justify-content-center">
                        <li class="list-group-item flex-fill"><a class="btn btn-dark btn-primario" href="/cuentas/crear">Crear cuenta</a></li>
                        <li class="list-group-item flex-fill"><a class="btn btn-dark btn-primario" href="/cuentas/editar">Editar cuenta</a></li>
                        <li class="list-group-item flex-fill"><a class="btn btn-secondary" href="/admin">Volver</a></li>
                    </ul>
                    <br>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>RUT</th>
                                <th>Nombre</th>
                                <th>Perfil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario['rut'] ?></td>
                                    <td><?= $usuario['nombre1'] ?> <?= $usuario['apellido1'] ?></td>
                                    <td><?= $usuario['nombrePerfil'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
