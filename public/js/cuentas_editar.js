document.addEventListener('DOMContentLoaded', function() {
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

    document.querySelectorAll('input[name="usuario"]').forEach(radio => {
        radio.addEventListener('change', function() {
            mostrarFormulario(this.value);
        });
    });

    document.querySelectorAll('.reset-password-btn').forEach(button => {
        button.addEventListener('click', function() {
            const rut = this.dataset.rut;
            resetearContrasena(rut);
        });
    });

    document.getElementById('form-editar-usuario').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir el envío por defecto del formulario

        const rut = document.getElementById('rut').value;
        const nombre1 = document.getElementById('nombre1').value;
        const nombre2 = document.getElementById('nombre2').value;
        const apellido1 = document.getElementById('apellido1').value;
        const apellido2 = document.getElementById('apellido2').value;
        const idPerfil = document.getElementById('perfil').value;

        const formData = new FormData();
        formData.append('rut', rut);
        formData.append('nombre1', nombre1);
        formData.append('nombre2', nombre2);
        formData.append('apellido1', apellido1);
        formData.append('apellido2', apellido2);
        formData.append('idPerfil', idPerfil);

        fetch('/cuentas/editar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Ha ocurrido un error desconocido.'));
            }
        })
        .catch(error => {
            console.error('Error al actualizar el usuario:', error);
            alert('Error al actualizar el usuario.');
        });
    });
});