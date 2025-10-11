<h1 class="text-center">Soporte</h1>
<div class="d-grid gap-2 col-6 mx-auto">
    <a href="/soporte/crear" class="btn btn-dark">Crear Ticket</a>
    <?php if ((string)($_SESSION['idPerfil'] ?? '') === '1'): ?>
        <a href="/soporte/admin" class="btn btn-dark">Ver Tickets</a>
        <a href="/admin" class="btn btn-secondary">Volver</a>
    <?php else: ?>
        <a href="/soporte/mis" class="btn btn-dark">Mis Tickets</a>
        <a href="/worker" class="btn btn-secondary">Volver</a>
    <?php endif; ?>
</div>