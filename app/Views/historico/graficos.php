<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php if (!empty($lecturas)): ?>
    <?php foreach ($lecturas as $index => $lectura): ?>
        <h3 class="text-center">Tipo <?php echo htmlspecialchars((string)$lectura['tipoLectura']); ?></h3>
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
        <script>
            (function() {
                const ctx = document.getElementById('grafico_<?php echo $index; ?>').getContext('2d');
                const labels = [<?php echo implode(',', array_map(fn($d) => '"' . $d['fechaLectura'] . ' ' . $d['horaLectura'] . '"', $lectura['data'])); ?>];
                const dataVals = [<?php echo implode(',', array_map(fn($d) => (string)$d['lectura'], $lectura['data'])); ?>];
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: labels,
                            data: dataVals,
                            backgroundColor: 'transparent',
                            borderColor: '#3c763d',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })();
        </script>
        <br />
    <?php endforeach; ?>
<?php endif; ?>
<div class="text-center d-grid gap-2 col-6 mx-auto">
    <a href="/historico" class="btn btn-dark btn-lg">Volver</a>
</div>