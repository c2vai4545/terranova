<h1 class="text-center">Tickets de Soporte</h1>
<div class="wrapper">
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Ticket</th>
                    <th>Usuario</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo 'TCK-' . str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($ticket['fechaCreacion']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['creador']); ?></td>
                    <td class="text-center">
                        <input type="radio" name="ticket" value="<?php echo (int)$ticket['id']; ?>" onclick="mostrarProblema(<?php echo (int)$ticket['id']; ?>)">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div id="problema" class="fw-bold"></div>
        <div id="respuesta-form" style="display:none;">
            <label for="respuesta">Respuesta:</label>
            <textarea id="respuesta" name="respuesta" rows="4" maxlength="500" class="form-control"></textarea>
            <br>
            <button class="btn btn-dark" onclick="cerrarTicket()">Cerrar Ticket</button>
        </div>
    </div>
    <br>
    <div>
        <button class="btn btn-secondary" onclick="location.href='/admin'">Volver</button>
    </div>
</div>
<script>
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
</script>