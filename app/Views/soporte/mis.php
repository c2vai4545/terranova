<div class="wrapper">
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
                        <input type="radio" name="ticket" value="<?php echo (int)$ticket['id']; ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div id="respuesta-form">
            <label class="fw-bold">Problema:</label>
            <div id="problema-label"></div>
            <br>
            <label class="fw-bold">Respuesta:</label>
            <div id="respuesta-label"></div>
        </div>
    </div>
    <br>
    <button class="btn btn-secondary" id="volver-btn">Volver</button>
</div>
<style>
    #respuesta-form {
        display: none;
    }
</style>
<script src="/js/soporte.js"></script>