<h1 class="text-center">Mis Tickets de Soporte</h1>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha de Ticket</th>
                <th>Solucionador</th>
                <th>Estado</th>
                <th>Seleccionar</th>
            </tr>
        </thead>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?php echo 'TCK-' . str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($ticket['fechaCreacion']); ?></td>
                <td><?php echo !empty($ticket['solucionador']) ? htmlspecialchars($ticket['solucionador']) : 'Sin Asignar'; ?></td>
                <td><?php echo !empty($ticket['respuesta']) ? 'Solucionado' : 'Pendiente'; ?></td>
                <td>
                    <input type="radio" name="ticket" value="<?php echo (int)$ticket['id']; ?>" onclick="mostrarRespuesta(<?php echo (int)$ticket['id']; ?>)">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div id="respuesta-form" style="display:none;">
        <label class="fw-bold">Problema:</label>
        <div id="problema-label"></div>
        <br>
        <label class="fw-bold">Respuesta:</label>
        <div id="respuesta-label"></div>
    </div>
</div>
<br>
<button class="btn btn-secondary" onclick="location.href='/worker'">Volver</button>
<script>
    function mostrarRespuesta(id) {
        fetch('/soporte/respuesta?id=' + id).then(r => r.json()).then(d => {
            document.getElementById('problema-label').innerText = d.problema || '';
            document.getElementById('respuesta-label').innerText = d.respuesta ? `${d.respuesta} - ${d.fechaRespuesta} - ${d.solucionador}` : 'Sin Asignar';
            document.getElementById('respuesta-form').style.display = 'block';
        });
    }
</script>