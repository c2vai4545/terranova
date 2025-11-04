function mostrarRespuesta(id) {
    fetch('/soporte/respuesta?id=' + id).then(r => r.json()).then(d => {
        document.getElementById('problema-label').innerText = d.problema || '';
        document.getElementById('respuesta-label').innerText = d.respuesta ? `${d.respuesta} - ${d.fechaRespuesta} - ${d.solucionador}` : 'Sin Asignar';
        document.getElementById('respuesta-form').style.display = 'block';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const radioButtons = document.querySelectorAll('input[name="ticket"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (event) => {
            mostrarRespuesta(event.target.value);
        });
    });

    const volverBtn = document.getElementById('volver-btn');
    if (volverBtn) {
        volverBtn.addEventListener('click', () => {
            location.href = '/worker';
        });
    }
});