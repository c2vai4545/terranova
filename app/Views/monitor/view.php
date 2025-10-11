<h2 class="text-center">Monitoreo</h2>
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