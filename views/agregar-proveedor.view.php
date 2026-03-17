<div class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">
                <?php echo $modo_edicion ? 'Editar Proveedor' : 'Agregar Proveedor'; ?>
            </h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">
                <?php echo $modo_edicion ? 'Modificar informacion del proveedor' : 'Registrar un nuevo proveedor en el sistema'; ?>
            </p>
        </div>
        <a href="proveedores.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100);">
            <h2 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--gray-900); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-truck" style="color: var(--primary);"></i>
                Datos del Proveedor
            </h2>
        </div>
        <div style="padding: 1.5rem;">
            <form method="POST" action="agregar-proveedor.php" id="formProveedor" onsubmit="return confirmarGuardarProveedor(event)">
                <?php if ($modo_edicion): ?>
                    <input type="hidden" name="id" value="<?php echo $proveedor['id']; ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building" style="color: var(--primary); margin-right: 4px;"></i> Nombre *
                        </label>
                        <input type="text" name="nombre" class="form-input" required
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['nombre']) : ''; ?>"
                               placeholder="Ej: Suministros SA de CV">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user" style="color: var(--primary); margin-right: 4px;"></i> Contacto
                        </label>
                        <input type="text" name="contacto" class="form-input"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['contacto']) : ''; ?>"
                               placeholder="Ej: Juan Perez">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone" style="color: var(--primary); margin-right: 4px;"></i> Telefono
                        </label>
                        <input type="text" name="telefono" class="form-input"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['telefono']) : ''; ?>"
                               placeholder="Ej: 1234567890">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope" style="color: var(--primary); margin-right: 4px;"></i> Email
                        </label>
                        <input type="email" name="email" class="form-input"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['email']) : ''; ?>"
                               placeholder="Ej: contacto@proveedor.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card" style="color: var(--primary); margin-right: 4px;"></i> RFC
                        </label>
                        <input type="text" name="rfc" class="form-input"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['rfc']) : ''; ?>"
                               placeholder="Ej: PEGJ850101ABC">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary); margin-right: 4px;"></i> Direccion
                        </label>
                        <textarea name="direccion" rows="3" class="form-input" style="resize: vertical;"
                                  placeholder="Direccion completa del proveedor"><?php echo $modo_edicion ? htmlspecialchars($proveedor['direccion']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--gray-100); justify-content: flex-end;">
                    <button type="button" onclick="location.href='proveedores.php'" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $modo_edicion ? 'Actualizar' : 'Guardar'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmarGuardarProveedor(event) {
    event.preventDefault();
    const nombre = document.querySelector('input[name="nombre"]').value;
    const modoEdicion = <?php echo $modo_edicion ? 'true' : 'false'; ?>;
    const accion = modoEdicion ? 'actualizar' : 'guardar';
    
    Swal.fire({
        title: modoEdicion ? 'Actualizar proveedor?' : 'Guardar proveedor?',
        html: '<p>Se ' + accion + 'a el proveedor:</p><p><strong>' + nombre + '</strong></p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Si, ' + accion,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formProveedor').submit();
        }
    });
    return false;
}
</script>
