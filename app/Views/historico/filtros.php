<h2 class="text-center" style="font-weight: bold">Histórico</h2>
<form method="POST" action="/historico/graficos">
    <div class="row">
        <div class="col-6">
            <h5>Fecha de inicio</h5>
            <input type="date" class="form-control" name="fechaInicio" required>
        </div>
        <div class="col-6">
            <h5>Fecha de término</h5>
            <input type="date" class="form-control" name="fechaFin" required>
        </div>
    </div>
    <div class="card mt-3 p-3">
        <h5>Seleccione tipo(s) de lectura:</h5>
        <?php foreach ($tipos as $row): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tiposLectura[]" value="<?php echo htmlspecialchars($row['idTipoLectura']); ?>">
                <label class="form-check-label"><?php echo htmlspecialchars($row['nombre']); ?></label>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-dark mt-3">Mostrar</button>
    </div>
</form>