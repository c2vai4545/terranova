<div>
    <h1>TerranovaGreenPUQ</h1>
</div>
<div class="container marketing">
    <h2 class="text-center" style="font-weight: bold">Monitoreo</h2>
    <div class="row text-center">
        <div class="col-lg-4">
            <img src="/imgs/Temp.png" width="300" height="300" />
            <hr />
            <h3 class="fw-normal">Temperatura</h3>
            <h1 style="font-weight: bold"><span id="temperatura"><?php echo isset($data['temperatura']) ? htmlspecialchars($data['temperatura']) . '°C' : 'N/A'; ?></span></h1>
        </div>
        <div class="col-lg-4">
            <img src="/imgs/HumAire.png" width="300" height="300" />
            <hr />
            <h3 class="fw-normal">Humedad del Aire</h3>
            <h1 style="font-weight: bold"><span id="humedadAire"><?php echo isset($data['humedadAire']) ? htmlspecialchars($data['humedadAire']) . '%' : 'N/A'; ?></span></h1>
        </div>
        <div class="col-lg-4">
            <img src="/imgs/HumSuelo.png" width="300" height="300" />
            <hr />
            <h3 class="fw-normal">Humedad del Suelo</h3>
            <h1 style="font-weight: bold"><span id="humedadSuelo"><?php echo isset($data['humedadSuelo']) ? htmlspecialchars($data['humedadSuelo']) . '%' : 'N/A'; ?></span></h1>
        </div>
    </div>
    <br />
    <br />
    <br />
    <div class="text-center mt-3">
        <?php if ((string)($_SESSION['idPerfil'] ?? '') === '1') { ?>
            <a href="/admin" class="btn btn-dark btn-primario">Volver</a>
        <?php } elseif ((string)($_SESSION['idPerfil'] ?? '') === '2') { ?>
            <a href="/worker" class="btn btn-dark btn-primario">Volver</a>
        <?php } ?>
        <a href="/logout" class="btn btn-secondary ml-2">Cerrar sesión</a>
    </div>
</div>
<script>
    function actualizarContenido() {
        fetch('/monitor/data').then(r => r.json()).then(datos => {
            document.getElementById('temperatura').innerHTML = (datos.temperatura !== null ? datos.temperatura + '°C' : 'N/A');
            document.getElementById('humedadAire').innerHTML = (datos.humedadAire !== null ? datos.humedadAire + '%' : 'N/A');
            document.getElementById('humedadSuelo').innerHTML = (datos.humedadSuelo !== null ? datos.humedadSuelo + '%' : 'N/A');
        });
    }
    setInterval(actualizarContenido, 5000);
</script>