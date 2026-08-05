<?php require 'includes/header.php'; ?>
<?php require 'includes/modal-system.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="margin: 0 0 4px 0; color: var(--gray-900); font-size: 1.5rem; font-weight: 700; letter-spacing: -0.3px;">Dashboard</h1>
        <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Bienvenido, <?php echo htmlspecialchars($user['nombre_completo'] ?? $_SESSION['user_nombre'] ?? 'Usuario'); ?></p>
    </div>

    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Total Productos -->
        <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: var(--primary-light); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-cubes" style="font-size: 1.125rem; color: var(--primary);"></i>
                </div>
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Productos</p>
                    <h3 style="margin: 2px 0 0; font-size: 1.625rem; font-weight: 700; color: var(--gray-900); letter-spacing: -0.5px;"><?php echo $data['stats']['total_productos']; ?></h3>
                </div>
            </div>
        </div>

        <!-- Valor Total -->
        <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: var(--success-light); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-dollar-sign" style="font-size: 1.125rem; color: var(--success);"></i>
                </div>
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Valor Total</p>
                    <h3 style="margin: 2px 0 0; font-size: 1.625rem; font-weight: 700; color: var(--gray-900); letter-spacing: -0.5px;">$<?php echo number_format($data['stats']['valor_total'], 0); ?></h3>
                </div>
            </div>
        </div>

        <!-- Bajo Stock -->
        <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: var(--danger-light); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.125rem; color: var(--danger);"></i>
                </div>
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Bajo Stock</p>
                    <h3 style="margin: 2px 0 0; font-size: 1.625rem; font-weight: 700; color: <?php echo $data['stats']['productos_bajo_stock'] > 0 ? 'var(--danger)' : 'var(--gray-900)'; ?>; letter-spacing: -0.5px;"><?php echo $data['stats']['productos_bajo_stock']; ?></h3>
                </div>
            </div>
        </div>

        <!-- Requisiciones Pendientes -->
        <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: var(--warning-light); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-clipboard-list" style="font-size: 1.125rem; color: var(--warning);"></i>
                </div>
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Req. Pendientes</p>
                    <h3 style="margin: 2px 0 0; font-size: 1.625rem; font-weight: 700; color: <?php echo $data['stats']['requisiciones_pendientes'] > 0 ? 'var(--warning)' : 'var(--gray-900)'; ?>; letter-spacing: -0.5px;"><?php echo $data['stats']['requisiciones_pendientes']; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div style="background: white; border-radius: 14px; overflow: hidden; border: 1px solid var(--border-color);">
        <div style="padding: 1.125rem 1.25rem; border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h2 style="margin: 0; color: var(--gray-900); font-size: 1.0625rem; font-weight: 700; letter-spacing: -0.2px;">
                        Inventario 
                        <?php if ($user['rol'] === 'admin' || $user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general'): ?>
                            General
                        <?php else: ?>
                            - <?php echo htmlspecialchars($user['sub_almacen_nombre'] ?? $_SESSION['sub_almacen_nombre'] ?? 'N/A'); ?>
                        <?php endif; ?>
                    </h2>
                    <p style="margin: 4px 0 0 0; color: var(--text-muted); font-size: 0.8125rem;">Productos en stock</p>
                </div>
                
                <?php if ($user['rol'] === 'admin' || in_array($user['rol'], ['gerencia', 'gerencia_general'])): ?>
                    <form method="GET" action="index.php" style="display: flex; gap: 8px; align-items: center;">
                        <select name="sub_almacen" id="sub_almacen" onchange="this.form.submit()" style="background: white; border: 1px solid var(--gray-300); border-radius: 10px; padding: 0.5rem 0.75rem; color: var(--text-primary); font-size: 0.8125rem;">
                            <?php if ($user['rol'] === 'admin'): ?>
                                <option value="">Todos los almacenes</option>
                            <?php endif; ?>
                            <?php foreach ($data['sub_almacenes'] as $almacen): ?>
                                <option value="<?php echo $almacen['id']; ?>" 
                                    <?php echo ($sub_almacen_filter == $almacen['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($almacen['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($sub_almacen_filter && $sub_almacen_filter != 100): ?>
                            <a href="index.php" class="btn btn-danger btn-sm" style="height: 34px; display: flex; align-items: center;">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--gray-50);">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Codigo</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                        <?php if ($user['rol'] === 'admin'): ?>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Sub-Almacen</th>
                        <?php endif; ?>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Cantidad</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Unidad</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Precio Unit.</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Valor Total</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Stock Min.</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Estado</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data['inventario']) > 0): ?>
                        <?php foreach ($data['inventario'] as $item): ?>
                            <?php 
                                $valor_item = $item['cantidad'] * $item['precio_unitario'];
                                $bajo_stock = $item['cantidad'] <= $item['stock_minimo'];
                            ?>
                            <tr style="border-bottom: 1px solid var(--gray-100); transition: background 0.15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.75rem 1rem;"><span style="color: var(--primary); font-weight: 600; font-size: 0.8125rem;"><?php echo htmlspecialchars($item['codigo']); ?></span></td>
                                <td style="padding: 0.75rem 1rem;">
                                    <strong style="color: var(--gray-900); font-size: 0.8125rem;"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                    <?php if ($item['descripcion']): ?>
                                        <br><small style="color: var(--text-muted);"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <?php if ($user['rol'] === 'admin'): ?>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="background: var(--primary-light); color: var(--primary); padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">
                                            <?php echo htmlspecialchars($item['sub_almacen_nombre']); ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td style="padding: 0.75rem 1rem;">
                                    <strong style="color: <?php echo $bajo_stock ? 'var(--danger)' : 'var(--gray-900)'; ?>; font-size: 0.9375rem;">
                                        <?php echo $item['cantidad']; ?>
                                    </strong>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--text-muted);"><?php echo htmlspecialchars($item['unidad']); ?></td>
                                <td style="padding: 0.75rem 1rem; color: var(--gray-900); font-weight: 500;">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                <td style="padding: 0.75rem 1rem; color: var(--gray-900); font-weight: 600;">$<?php echo number_format($valor_item, 2); ?></td>
                                <td style="padding: 0.75rem 1rem; color: var(--text-muted);"><?php echo $item['stock_minimo']; ?></td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?php if ($bajo_stock): ?>
                                        <span style="background: var(--danger-light); color: var(--danger); padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-exclamation-circle" style="font-size: 10px;"></i> Bajo
                                        </span>
                                    <?php else: ?>
                                        <span style="background: var(--success-light); color: var(--success); padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-check-circle" style="font-size: 10px;"></i> OK
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <a href="editar-producto.php?id=<?php echo $item['id']; ?>" 
                                           title="Editar producto"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        
                                        <button onclick="toggleBloqueoProducto(<?php echo $item['id']; ?>, <?php echo $item['activo'] ?? 1; ?>)"
                                                title="<?php echo ($item['activo'] ?? 1) ? 'Bloquear producto' : 'Desbloquear producto'; ?>"
                                                class="btn btn-sm <?php echo ($item['activo'] ?? 1) ? 'btn-danger' : 'btn-success'; ?>">
                                            <i class="fas fa-<?php echo ($item['activo'] ?? 1) ? 'lock' : 'unlock'; ?>"></i>
                                            <?php echo ($item['activo'] ?? 1) ? 'Bloquear' : 'Activar'; ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($user['rol'] === 'admin') ? '10' : '9'; ?>" style="text-align: center; padding: 4rem; color: var(--gray-400);">
                                <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                <span style="font-size: 0.9375rem; font-weight: 500;">No hay productos en el inventario</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function toggleBloqueoProducto(productoId, estadoActual) {
    const accion = estadoActual ? 'bloquear' : 'desbloquear';
    const mensaje = estadoActual 
        ? 'Bloquear este producto?' 
        : 'Desbloquear este producto?';
    const descripcion = estadoActual
        ? 'El producto no estara disponible para requisiciones mientras este bloqueado.'
        : 'El producto volvera a estar disponible para requisiciones.';
    
    CustomModal.showConfirm(mensaje, descripcion, () => {
        const formData = new FormData();
        formData.append('accion', 'toggle_bloqueo');
        formData.append('producto_id', productoId);
        formData.append('nuevo_estado', estadoActual ? 0 : 1);
        
        fetch('controllers/ProductoController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                CustomModal.showSuccess('Producto ' + (accion === 'bloquear' ? 'bloqueado' : 'desbloqueado') + ' exitosamente.');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + (data.message || 'No se pudo completar la operacion'));
            }
        })
        .catch(error => {
            alert('Error al procesar la solicitud');
        });
    });
}
</script>

<?php require 'includes/footer.php'; ?>
