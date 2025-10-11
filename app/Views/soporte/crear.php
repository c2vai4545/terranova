<h1 class="text-center">Crear Ticket</h1>
<?php if (!empty($error ?? '')): ?>
    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body">
        <form method="POST" action="/soporte/crear">
            <div class="mb-3">
                <label class="form-label">Detalles</label>
                <textarea name="problema" rows="4" maxlength="500" class="form-control" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-dark">Ingresar</button>
                <?php if ((string)($_SESSION['idPerfil'] ?? '') === '1'): ?>
                    <a href="/admin" class="btn btn-secondary">Volver</a>
                <?php else: ?>
                    <a href="/worker" class="btn btn-secondary">Volver</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>