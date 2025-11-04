document.addEventListener('DOMContentLoaded', function() {
    function mostrarProblema(id) {
        fetch('/soporte/problema?id=' + id)
            .then(r => r.text())
            .then(t => {
                document.getElementById('problema').innerText = t;
                document.getElementById('respuesta-form').style.display = 'block';
            });
    }

    function cerrarTicket() {
        const id = document.querySelector('input[name="ticket"]:checked').value;
        const respuesta = document.getElementById('respuesta').value;
        if (!respuesta.trim()) {
            alert('La respuesta no puede estar vacía.');
            return;
        }
        fetch('/soporte/cerrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&respuesta=${encodeURIComponent(respuesta)}`
            })
            .then(r => r.text())
            .then(t => {
                alert(t);
                location.reload();
            });
    }

    document.querySelectorAll('input[name="ticket"]').forEach(radio => {
        radio.addEventListener('change', function() {
            mostrarProblema(this.value);
        });
    });

    const cerrarTicketBtn = document.getElementById('cerrar-ticket-btn');
    if (cerrarTicketBtn) {
        cerrarTicketBtn.addEventListener('click', cerrarTicket);
    }

    const volverBtn = document.getElementById('volver-btn');
    if (volverBtn) {
        volverBtn.addEventListener('click', function() {
            location.href = '/admin';
        });
    }
});