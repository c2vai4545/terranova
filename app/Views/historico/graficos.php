<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php if (!empty($lecturas)): ?>
    <div id="lecturas-data" data-lecturas="<?php echo htmlspecialchars(json_encode($lecturas), ENT_QUOTES, 'UTF-8'); ?>"></div>
    <?php foreach ($lecturas as $index => $lectura): ?>
        <h3 class="text-center"><?php echo htmlspecialchars($lectura['tipoNombre']); ?></h3>
        <div class="row">
            <div class="col-8">
                <canvas id="grafico_<?php echo $index; ?>"></canvas>
            </div>
            <div class="col-4">
                <table class="table">
                    <thead>
                        <tr class="table-dark">
                            <th>Fecha Lectura</th>
                            <th>Hora Lectura</th>
                            <th>Lectura</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lectura['data'] as $dato): ?>
                            <tr class="table-success">
                                <td><?php echo htmlspecialchars($dato['fechaLectura']); ?></td>
                                <td><?php echo htmlspecialchars($dato['horaLectura']); ?></td>
                                <td><?php echo htmlspecialchars((string)$dato['lectura']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br />
    <?php endforeach; ?>
<?php endif; ?>
<div class="text-center d-grid gap-2 col-6 mx-auto">
    <a href="/historico" class="btn btn-dark btn-primario btn-lg">Volver</a>
    <br />
    <br />
</div>
<script src="/js/historico_graficos.js"></script>