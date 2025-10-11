<div class="wrapper">
    <h1 class="text-center">Crear Ticket</h1>
    <div class="card text-center">
        <form method="POST" action="/soporte/crear">
            <div class="form-group row align-items-start">
                <h3 class="col-5"><label for="problema">Detalles:</label></h3><br>
                <div class="col-7">
                    <textarea id="problema" name="problema" rows="4" maxlength="500" class="form-control" required></textarea><br>
                    <?php if (!empty($error ?? '')): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center">
                <hr />
                <button type="submit" class="btn btn-dark btn-primario">Ingresar</button>
                <?php if ((string)($_SESSION['idPerfil'] ?? '') === '1') { ?>
                    <a href="/admin" class="btn btn-secondary">Volver</a>
                <?php } else { ?>
                    <a href="/worker" class="btn btn-secondary">Volver</a>
                <?php } ?>
            </div>
        </form>
    </div>
</div>