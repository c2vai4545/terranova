<div class="card">
    <div class="card-body">
        <h2 class="text-center">Bienvenido/a, <?php echo htmlspecialchars($nombre ?? ''); ?></h2>
        <div class="image-container">
            <img src="/imgs/Terra.png" width="50" height="50" class="img-fluid">
        </div>
        <hr />
        <h3 class="text-center">Funciones:</h3>
        <br />
        <br />
        <ul class="list-group list-group-horizontal">
            <li class="list-group-item flex-fill"><a href="/monitor" class="btn btn-dark btn-primario">Monitoreo en Tiempo Real</a></li>
            <li class="list-group-item flex-fill"><a href="/micuenta" class="btn btn-dark btn-primario">Mi Cuenta</a></li>
            <li class="list-group-item flex-fill"><a href="/soporte" class="btn btn-dark btn-primario">Soporte</a></li>
        </ul>
        <img src="/imgs/inv.png" width="350" height="350" class="rounded mx-auto d-block">
        <div class="text-center mt-3">
            <form action="/logout" method="POST" style="display:inline;">
    <button type="submit" class="btn btn-secondary">Cerrar sesión</button>
</form>
        </div>
    </div>
</div>