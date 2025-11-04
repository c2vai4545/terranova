document.addEventListener('DOMContentLoaded', function() {
    const mensaje = document.getElementById('mensaje-alert');
    if (mensaje && mensaje.dataset.message) {
        alert(mensaje.dataset.message);
    }

    const volverBtn = document.getElementById('volver-btn');
    if (volverBtn) {
        volverBtn.addEventListener('click', function() {
            window.location.href = this.dataset.redirect;
        });
    }
});