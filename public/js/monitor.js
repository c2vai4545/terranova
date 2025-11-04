function actualizarContenido() {
    fetch('/monitor/data').then(r => r.json()).then(datos => {
        document.getElementById('temperatura').innerHTML = (datos.temperatura !== null ? datos.temperatura + '°C' : 'N/A');
        document.getElementById('humedadAire').innerHTML = (datos.humedadAire !== null ? datos.humedadAire + '%' : 'N/A');
        document.getElementById('humedadSuelo').innerHTML = (datos.humedadSuelo !== null ? datos.humedadSuelo + '%' : 'N/A');
    });
}
setInterval(actualizarContenido, 5000);