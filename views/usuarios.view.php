
<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Gestion de Usuarios</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Administrar usuarios y permisos del sistema</p>
        </div>
        <button onclick="window.location.href='agregar-usuario.php'" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Usuario
        </button>
    </div>

    <div class="card" style="overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--gray-50);">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Usuario</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Nombre</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Email</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Rol</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Sub-Almacen</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Estado</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                    <tr style="border-bottom: 1px solid var(--gray-100); transition: background 0.15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.75rem 1rem;"><strong style="color: var(--gray-900); font-size: 0.8125rem;"><?php echo htmlspecialchars($usuario['username']); ?></strong></td>
                        <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                        <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($usuario['email'] ?? '-'); ?></td>
                        <td style="padding: 0.75rem 1rem;">
                            <span class="badge badge-<?php 
                                if ($usuario['rol'] === 'admin') echo 'danger';
                                elseif ($usuario['rol'] === 'compras') echo 'primary';
                                elseif ($usuario['rol'] === 'gerencia' || $usuario['rol'] === 'gerencia_general') echo 'warning';
                                else echo 'primary';
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $usuario['rol'])); ?>
                            </span>
                        </td>
                        <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($usuario['sub_almacen_nombre'] ?? '-'); ?></td>
                        <td style="padding: 0.75rem 1rem;">
                            <?php if ($usuario['activo']): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 999px; font-size: 0.6875rem; font-weight: 600; background: var(--gray-100); color: var(--gray-500);">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem 1rem;">
                            <div style="display: flex; gap: 4px; justify-content: center;">
                                <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="permisos-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-secondary" title="Permisos"><i class="fas fa-key"></i></a>
                                <?php if ($usuario['activo']): ?>
                                    <button onclick="toggleEstadoUsuario(<?php echo $usuario['id']; ?>, 0)" class="btn btn-sm btn-danger" title="Desactivar"><i class="fas fa-ban"></i></button>
                                <?php else: ?>
                                    <button onclick="toggleEstadoUsuario(<?php echo $usuario['id']; ?>, 1)" class="btn btn-sm btn-success" title="Activar"><i class="fas fa-check"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function toggleEstadoUsuario(id, nuevoEstado) {
    const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
    if (confirm(`Esta seguro de ${accion} este usuario?`)) {
        fetch('controllers/toggle-estado-usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&estado=${nuevoEstado}`
        })
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); else alert('Error: ' + (data.message || 'No se pudo cambiar el estado')); })
        .catch(() => alert('Error al cambiar el estado del usuario'));
    }
}
</script>

<?php require 'includes/footer.php'; ?>
