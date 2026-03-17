<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Plantillas</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Crea plantillas de productos para agilizar tus requisiciones</p>
        </div>
        <a href="nueva-plantilla.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-plus-circle"></i> Nueva Plantilla
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="animation: slideDown 0.3s ease-out; margin-bottom: 1rem;">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>" style="margin-right: 0.5rem;"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <?php if (count($plantillas) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem;">
            <?php foreach ($plantillas as $plantilla): ?>
                <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.04);"
                     onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)'; this.style.transform='translateY(-2px)'"
                     onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">
                    
                    <!-- Header card -->
                    <div style="padding: 1.25rem 1.25rem 0.75rem; border-bottom: 1px solid var(--gray-100);">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                <div style="width: 40px; height: 40px; background: var(--primary-light); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-file-alt" style="color: var(--primary); font-size: 0.875rem;"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <h3 style="margin: 0; font-size: 0.9375rem; font-weight: 700; color: var(--gray-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($plantilla['nombre']); ?>
                                    </h3>
                                    <?php if (!empty($plantilla['descripcion'])): ?>
                                        <p style="margin: 2px 0 0; font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($plantilla['descripcion']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.6875rem; font-weight: 700; color: var(--primary); background: var(--primary-light); white-space: nowrap; flex-shrink: 0;">
                                <i class="fas fa-boxes" style="font-size: 0.5625rem;"></i>
                                <?php echo $plantilla['total_productos']; ?> producto<?php echo $plantilla['total_productos'] != 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Info -->
                    <div style="padding: 0.75rem 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-calendar-alt" style="font-size: 0.625rem;"></i>
                                Creada: <?php echo date('d/m/Y', strtotime($plantilla['fecha_creacion'])); ?>
                            </span>
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-sync-alt" style="font-size: 0.625rem;"></i>
                                Modificada: <?php echo date('d/m/Y', strtotime($plantilla['fecha_actualizacion'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--gray-100); display: flex; gap: 0.5rem;">
                        <a href="nueva-requisicion.php?plantilla_id=<?php echo $plantilla['id']; ?>" 
                           class="btn btn-primary btn-sm" 
                           style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.75rem; padding: 6px 10px; border-radius: var(--radius-md);">
                            <i class="fas fa-paper-plane"></i> Usar como Req
                        </a>
                        <a href="editar-plantilla.php?id=<?php echo $plantilla['id']; ?>" 
                           class="btn btn-secondary btn-sm" 
                           style="display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.75rem; padding: 6px 10px; border-radius: var(--radius-md);">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" onclick="confirmarEliminar(<?php echo $plantilla['id']; ?>, '<?php echo htmlspecialchars(addslashes($plantilla['nombre'])); ?>')"
                                class="btn btn-sm"
                                style="display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.75rem; padding: 6px 10px; border-radius: var(--radius-md); background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 4rem 2rem; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
            <div style="width: 64px; height: 64px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-file-alt" style="font-size: 1.5rem; color: var(--gray-400);"></i>
            </div>
            <h3 style="color: var(--gray-700); font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">No tienes plantillas</h3>
            <p style="color: var(--text-muted); font-size: 0.8125rem; margin-bottom: 1.25rem;">Crea tu primera plantilla para agilizar el proceso de requisiciones.</p>
            <a href="nueva-plantilla.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-plus-circle"></i> Crear Plantilla
            </a>
        </div>
    <?php endif; ?>
</main>

<script>
function confirmarEliminar(id, nombre) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Eliminar plantilla',
            html: 'Se eliminara la plantilla <strong>' + nombre + '</strong> permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = 'plantillas.php?eliminar=' + id;
            }
        });
    } else {
        if (confirm('Eliminar la plantilla "' + nombre + '"?')) {
            window.location.href = 'plantillas.php?eliminar=' + id;
        }
    }
}
</script>
