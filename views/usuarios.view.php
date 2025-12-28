

<main class="main-content">
    <!-- Header mejorado con mejor espaciado y diseño -->
    <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2.5rem; border-radius: 12px; margin-bottom: 2rem; color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 12px; backdrop-filter: blur(10px);">
                <i class="fas fa-users" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: white;">Gestión de Usuarios</h1>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 1rem;">Administrar usuarios y permisos del sistema</p>
            </div>
        </div>
    </div>

    <!-- Botón de agregar con mejor diseño -->
    <div style="margin-bottom: 1.5rem;">
        <button onclick="window.location.href='agregar-usuario.php'" style="background: #2962FF; color: white; border: none; padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; box-shadow: 0 2px 4px rgba(41, 98, 255, 0.3);" onmouseover="this.style.background='#1E40AF'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)'" onmouseout="this.style.background='#2962FF'; this.style.boxShadow='0 2px 4px rgba(41, 98, 255, 0.3)'">
            <i class="fas fa-plus"></i> Agregar Usuario
        </button>
    </div>

    <!-- Tarjeta con diseño mejorado y sombras suaves -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <!-- Cabecera de tabla con mejor estilo -->
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Usuario</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Nombre Completo</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Rol</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Sub-Almacén</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                    <!-- Filas con hover effect y mejor espaciado -->
                    <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <td style="padding: 1rem;">
                            <strong style="color: #111827; font-weight: 600;"><?php echo htmlspecialchars($usuario['username']); ?></strong>
                        </td>
                        <td style="padding: 1rem; color: #6b7280;">
                            <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                        </td>
                        <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;">
                            <?php echo htmlspecialchars($usuario['email'] ?? '-'); ?>
                        </td>
                        <td style="padding: 1rem;">
                            <!-- Badges de rol con colores mejorados y diseño moderno -->
                            <span style="display: inline-block; padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; <?php 
                                if ($usuario['rol'] === 'admin') {
                                    echo 'background: #fee2e2; color: #991b1b;';
                                } elseif ($usuario['rol'] === 'compras') {
                                    echo 'background: #dbeafe; color: #1e40af;';
                                } elseif ($usuario['rol'] === 'gerencia') {
                                    echo 'background: #fef3c7; color: #92400e;';
                                } elseif ($usuario['rol'] === 'gerencia_general') {
                                    echo 'background: rgba(41, 98, 255, 0.15); color: #2962FF;';
                                } else {
                                    echo 'background: #e0e7ff; color: #3730a3;';
                                }
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $usuario['rol'])); ?>
                            </span>
                        </td>
                        <td style="padding: 1rem; color: #6b7280;">
                            <?php echo htmlspecialchars($usuario['sub_almacen_nombre'] ?? '-'); ?>
                        </td>
                        <td style="padding: 1rem;">
                            <!-- Badge de estado con diseño mejorado -->
                            <?php if ($usuario['activo']): ?>
                                <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(41, 98, 255, 0.15); color: #2962FF;">
                                    <span style="width: 6px; height: 6px; background: #2962FF; border-radius: 50%;"></span>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #f3f4f6; color: #6b7280;">
                                    <span style="width: 6px; height: 6px; background: #9ca3af; border-radius: 50%;"></span>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem;">
                            <!-- Botones de acción con diseño circular moderno -->
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; transition: all 0.3s; box-shadow: 0 1px 3px rgba(59, 130, 246, 0.3);" 
                                   title="Editar"
                                   onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.4)'" 
                                   onmouseout="this.style.background='#3b82f6'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(59, 130, 246, 0.3)'">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="permisos-usuario.php?id=<?php echo $usuario['id']; ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #2962FF; color: white; border-radius: 8px; text-decoration: none; transition: all 0.3s; box-shadow: 0 1px 3px rgba(41, 98, 255, 0.3);" 
                                   title="Permisos"
                                   onmouseover="this.style.background='#1E40AF'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)'" 
                                   onmouseout="this.style.background='#2962FF'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(41, 98, 255, 0.3)'">
                                    <i class="fas fa-key"></i>
                                </a>
                                <!-- Botones de acción con diseño circular moderno -->
                                <?php if ($usuario['activo']): ?>
                                    <button onclick="toggleEstadoUsuario(<?php echo $usuario['id']; ?>, 0)" 
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #ef4444; color: white; border-radius: 8px; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 1px 3px rgba(239, 68, 68, 0.3);" 
                                       title="Desactivar"
                                       onmouseover="this.style.background='#dc2626'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)'" 
                                       onmouseout="this.style.background='#ef4444'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(239, 68, 68, 0.3)'">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php else: ?>
                                    <button onclick="toggleEstadoUsuario(<?php echo $usuario['id']; ?>, 1)" 
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #2962FF; color: white; border-radius: 8px; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 1px 3px rgba(41, 98, 255, 0.3);" 
                                       title="Activar"
                                       onmouseover="this.style.background='#1E40AF'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)'" 
                                       onmouseout="this.style.background='#2962FF'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(41, 98, 255, 0.3)'">
                                        <i class="fas fa-check"></i>
                                    </button>
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

<!-- Agregar script para cambiar estado de usuario -->
<script>
function toggleEstadoUsuario(id, nuevoEstado) {
    const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
    if (confirm(`¿Está seguro de ${accion} este usuario?`)) {
        fetch('controllers/toggle-estado-usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&estado=${nuevoEstado}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
            }
        })
        .catch(error => {
            alert('Error al cambiar el estado del usuario');
            console.error('Error:', error);
        });
    }
}
</script>

<?php require 'includes/footer.php'; ?>
