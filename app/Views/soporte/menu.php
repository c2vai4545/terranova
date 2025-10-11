<div class="container mt-5">
    <div class="card">
        <div class="card-body text-center">
            <h1>Soporte</h1>
            <hr />
            <br />
            <div class="d-flex justify-content-center align-items-start">
                <img class="logo3" src="/imgs/Terra.png" width="200" height="200" />
                <div class="mt-3 w-100">
                    <ul class="list-group list-group-horizontal justify-content-center">
                        <li class="list-group-item flex-fill"><a class="btn btn-dark btn-primario" href="/soporte/crear">Crear Ticket</a></li>
                        <?php if ((string)($_SESSION['idPerfil'] ?? '') === '1'): ?>
                            <li class="list-group-item flex-fill"><a class="btn btn-dark btn-primario" href="/soporte/admin">Ver Tickets</a></li>
                            <li class="list-group-item flex-fill"><a class="btn btn-secondary" href="/admin">Volver</a></li>
                        <?php else: ?>
                            <li class="list-group-item flex-fill"><a class="btn btn-dark btn-primario" href="/soporte/mis">Mis Tickets</a></li>
                            <li class="list-group-item flex-fill"><a class="btn btn-secondary" href="/worker">Volver</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>