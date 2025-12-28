<main class="main-content">
    <div style="padding: 20px; width: 100%; margin: 0 auto;">
        <!-- Header con gradiente azul marino -->
        <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); border-radius: 16px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-truck" style="font-size: 2rem; color: white;"></i>
                </div>
                <div>
                    <h2 style="color: white; margin: 0; font-size: 2rem; font-weight: 700;">Proveedores</h2>
                    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0;">Gestión de proveedores del sistema</p>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <!-- Botón con estilo azul marino -->
                <button onclick="location.href='agregar-proveedor.php'" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); transition: all 0.3s;">
                    <i class="fas fa-plus"></i> Agregar Proveedor
                </button>
            </div>
        </div>

        <!-- Tabla con diseño minimalista -->
        <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); overflow: hidden; border: 1px solid rgba(41, 98, 255, 0.08);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.05) 0%, rgba(41, 98, 255, 0.1) 100%);">
                        <th style="padding: 1.25rem; text-align: left; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Nombre</th>
                        <th style="padding: 1.25rem; text-align: left; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Contacto</th>
                        <th style="padding: 1.25rem; text-align: left; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Teléfono</th>
                        <th style="padding: 1.25rem; text-align: left; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Email</th>
                        <th style="padding: 1.25rem; text-align: left; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">RFC</th>
                        <th style="padding: 1.25rem; text-align: center; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Estado</th>
                        <th style="padding: 1.25rem; text-align: center; color: #2962FF; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7" style="padding: 5rem 1.25rem; text-align: center;">
                            <div style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.05) 0%, rgba(41, 98, 255, 0.1) 100%); width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-truck" style="font-size: 3rem; color: #2962FF; opacity: 0.3;"></i>
                            </div>
                            <p style="margin: 0; font-size: 1.25rem; color: #0a192f; font-weight: 600;">No hay proveedores registrados</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;" onmouseover="this.style.background='rgba(41, 98, 255, 0.02)'" onmouseout="this.style.background='white'">
                            <td style="padding: 1.25rem; color: #0a192f; font-weight: 600;"><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                            <td style="padding: 1.25rem; color: #6b7280;"><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
                            <td style="padding: 1.25rem; color: #6b7280;"><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                            <td style="padding: 1.25rem; color: #6b7280;"><?php echo htmlspecialchars($proveedor['email']); ?></td>
                            <td style="padding: 1.25rem; color: #6b7280;"><?php echo htmlspecialchars($proveedor['rfc']); ?></td>
                            <td style="padding: 1.25rem; text-align: center;">
                                <?php if ($proveedor['activo']): ?>
                                    <span style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.2) 100%); color: #22c55e; padding: 0.375rem 1rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(34, 197, 94, 0.3);">Activo</span>
                                <?php else: ?>
                                    <span style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.2) 100%); color: #ef4444; padding: 0.375rem 1rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.3);">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1.25rem; text-align: center;">
                                <button onclick="editarProveedor(<?php echo $proveedor['id']; ?>)" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; margin-right: 0.5rem; box-shadow: 0 2px 8px rgba(41, 98, 255, 0.2); transition: all 0.3s;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($proveedor['activo']): ?>
                                    <button onclick="toggleEstadoProveedor(<?php echo $proveedor['id']; ?>, 0)" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; transition: all 0.3s;" title="Desactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php else: ?>
                                    <button onclick="toggleEstadoProveedor(<?php echo $proveedor['id']; ?>, 1)" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; transition: all 0.3s;" title="Activar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<!-- NO cerrar main aquí, lo cierra footer.php -->
</main>

<script>
function editarProveedor(id) {
    window.location.href = 'agregar-proveedor.php?id=' + id;
}

function toggleEstadoProveedor(id, nuevoEstado) {
    const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
    if (confirm(`¿Está seguro de ${accion} este proveedor?`)) {
        fetch('controllers/toggle-estado-proveedor.php', {
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
            alert('Error al cambiar el estado del proveedor');
            console.error('Error:', error);
        });
    }
}
</script>
