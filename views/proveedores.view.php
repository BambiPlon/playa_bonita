<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Proveedores</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Gestion de proveedores del sistema</p>
        </div>
        <button onclick="location.href='agregar-proveedor.php'" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Proveedor
        </button>
    </div>

    <div class="card" style="overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--gray-50);">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Nombre</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Contacto</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Telefono</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Email</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">RFC</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Estado</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 4rem; color: var(--gray-400);">
                            <i class="fas fa-truck" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                            <span style="font-size: 0.9375rem; font-weight: 500;">No hay proveedores registrados</span>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr style="border-bottom: 1px solid var(--gray-100); transition: background 0.15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 0.75rem 1rem; color: var(--gray-900); font-weight: 600; font-size: 0.8125rem;"><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($proveedor['email']); ?></td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($proveedor['rfc']); ?></td>
                            <td style="padding: 0.75rem 1rem; text-align: center;">
                                <?php if ($proveedor['activo']): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: center;">
                                <div style="display: flex; gap: 4px; justify-content: center;">
                                    <button onclick="editarProveedor(<?php echo $proveedor['id']; ?>)" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                    <?php if ($proveedor['activo']): ?>
                                        <button onclick="toggleEstadoProveedor(<?php echo $proveedor['id']; ?>, 0)" class="btn btn-sm btn-danger" title="Desactivar"><i class="fas fa-ban"></i></button>
                                    <?php else: ?>
                                        <button onclick="toggleEstadoProveedor(<?php echo $proveedor['id']; ?>, 1)" class="btn btn-sm btn-success" title="Activar"><i class="fas fa-check"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function editarProveedor(id) { window.location.href = 'agregar-proveedor.php?id=' + id; }
function toggleEstadoProveedor(id, nuevoEstado) {
    const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
    if (confirm(`Esta seguro de ${accion} este proveedor?`)) {
        fetch('controllers/toggle-estado-proveedor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&estado=${nuevoEstado}`
        })
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); else alert('Error: ' + (data.message || 'No se pudo cambiar el estado')); })
        .catch(() => alert('Error al cambiar el estado'));
    }
}
</script>
