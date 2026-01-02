<?php require 'includes/header.php'; ?>
<?php require 'includes/modal-system.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0 0 5px 0; color: #111827; font-size: 32px; font-weight: 700;">Dashboard</h1>
        <p style="margin: 0; color: #6b7280; font-size: 16px;">Bienvenido, <?php echo htmlspecialchars($user['nombre_completo'] ?? $_SESSION['user_nombre'] ?? 'Usuario'); ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; border: 1px solid #e5e7eb;">
            <div style="position: absolute; top: 20px; right: 20px;">
                <button style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">⋮</button>
            </div>
            <div style="background: rgba(41, 98, 255, 0.15); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <i class="fas fa-boxes" style="font-size: 24px; color: #2962FF;"></i>
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 700; color: #111827;"><?php echo $data['stats']['total_productos']; ?></h3>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Total de Productos</p>
        </div>

        <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 100%); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(10, 25, 47, 0.3); position: relative;">
            <div style="position: absolute; top: 20px; right: 20px;">
                <button style="background: transparent; border: none; color: rgba(255,255,255,0.8); cursor: pointer; font-size: 18px;">⋮</button>
            </div>
            <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <i class="fas fa-wallet" style="font-size: 24px; color: white;"></i>
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 700; color: white;">$<?php echo number_format($data['stats']['valor_total'], 0); ?></h3>
            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 14px;">Valor Total</p>
        </div>

        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; border: 1px solid #e5e7eb;">
            <div style="position: absolute; top: 20px; right: 20px;">
                <button style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">⋮</button>
            </div>
            <div style="background: rgba(239, 68, 68, 0.15); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <i class="fas fa-exclamation-circle" style="font-size: 24px; color: #ef4444;"></i>
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 700; color: #ef4444;"><?php echo $data['stats']['productos_bajo_stock']; ?></h3>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Productos Bajo Stock</p>
        </div>

        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; border: 1px solid #e5e7eb;">
            <div style="position: absolute; top: 20px; right: 20px;">
                <button style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 18px;">⋮</button>
            </div>
            <div style="background: rgba(245, 158, 11, 0.15); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <i class="fas fa-file-alt" style="font-size: 24px; color: #f59e0b;"></i>
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 700; color: #111827;"><?php echo $data['stats']['total_requisiciones']; ?></h3>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Requisiciones Totales</p>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2 style="margin: 0; color: #111827; font-size: 20px; font-weight: 600;">
                        Inventario 
                        <?php if ($user['rol'] === 'admin' || $user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general'): ?>
                            General
                        <?php else: ?>
                            - <?php echo htmlspecialchars($user['sub_almacen_nombre'] ?? $_SESSION['sub_almacen_nombre'] ?? 'N/A'); ?>
                        <?php endif; ?>
                    </h2>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Productos en stock</p>
                </div>
                
                <?php if ($user['rol'] === 'admin' || in_array($user['rol'], ['gerencia', 'gerencia_general'])): ?>
                    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
                        <select name="sub_almacen" id="sub_almacen" onchange="this.form.submit()" style="background: white; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; color: #111827; font-size: 14px;">
                            <!-- Agregando opción de "Todos" solo para admin -->
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
                            <a href="index.php" style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px;">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div style="padding: 24px;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Código</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Producto</th>
                            <?php if ($user['rol'] === 'admin'): ?>
                                <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Sub-Almacén</th>
                            <?php endif; ?>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Cantidad</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Unidad</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Precio Unit.</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Valor Total</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Stock Mín.</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Estado</th>
                            <th style="padding: 12px 8px; text-align: center; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data['inventario']) > 0): ?>
                            <?php foreach ($data['inventario'] as $item): ?>
                                <?php 
                                    $valor_item = $item['cantidad'] * $item['precio_unitario'];
                                    $bajo_stock = $item['cantidad'] <= $item['stock_minimo'];
                                ?>
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 12px 8px;"><span style="color: #2962FF; font-weight: 600;"><?php echo htmlspecialchars($item['codigo']); ?></span></td>
                                    <td style="padding: 12px 8px;">
                                        <strong style="color: #111827; font-size: 14px;"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                        <?php if ($item['descripcion']): ?>
                                            <br><small style="color: #6b7280;"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($user['rol'] === 'admin'): ?>
                                        <td style="padding: 12px 8px;">
                                            <span style="background: rgba(41, 98, 255, 0.15); color: #2962FF; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                <?php echo htmlspecialchars($item['sub_almacen_nombre']); ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td style="padding: 12px 8px;">
                                        <strong style="color: <?php echo $bajo_stock ? '#ef4444' : '#2962FF'; ?>; font-size: 16px;">
                                            <?php echo $item['cantidad']; ?>
                                        </strong>
                                    </td>
                                    <td style="padding: 12px 8px; color: #6b7280;"><?php echo htmlspecialchars($item['unidad']); ?></td>
                                    <td style="padding: 12px 8px; color: #111827; font-weight: 500;">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                    <td style="padding: 12px 8px; color: #111827; font-weight: 600;">$<?php echo number_format($valor_item, 2); ?></td>
                                    <td style="padding: 12px 8px; color: #6b7280;"><?php echo $item['stock_minimo']; ?></td>
                                    <td style="padding: 12px 8px;">
                                        <?php if ($bajo_stock): ?>
                                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-exclamation-circle"></i> Bajo Stock
                                            </span>
                                        <?php else: ?>
                                            <span style="background: rgba(41, 98, 255, 0.1); color: #2962FF; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-check-circle"></i> Normal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                            <a href="editar-producto.php?id=<?php echo $item['id']; ?>" 
                                               title="Editar producto"
                                               style="background: linear-gradient(135deg, #2962FF 0%, #1d4ed8 100%); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(41, 98, 255, 0.3);"
                                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)';"
                                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(41, 98, 255, 0.3)';">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            
                                            <button onclick="toggleBloqueoProducto(<?php echo $item['id']; ?>, <?php echo $item['activo'] ?? 1; ?>)"
                                                    title="<?php echo ($item['activo'] ?? 1) ? 'Bloquear producto' : 'Desbloquear producto'; ?>"
                                                    style="background: <?php echo ($item['activo'] ?? 1) ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)'; ?>; color: white; padding: 8px 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 2px 8px <?php echo ($item['activo'] ?? 1) ? 'rgba(239, 68, 68, 0.3)' : 'rgba(16, 185, 129, 0.3)'; ?>;"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px <?php echo ($item['activo'] ?? 1) ? 'rgba(239, 68, 68, 0.4)' : 'rgba(16, 185, 129, 0.4)'; ?>';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px <?php echo ($item['activo'] ?? 1) ? 'rgba(239, 68, 68, 0.3)' : 'rgba(16, 185, 129, 0.3)'; ?>';">
                                                <i class="fas fa-<?php echo ($item['activo'] ?? 1) ? 'lock' : 'unlock'; ?>"></i>
                                                <?php echo ($item['activo'] ?? 1) ? 'Bloquear' : 'Desbloquear'; ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($user['rol'] === 'admin') ? '10' : '9'; ?>" style="text-align: center; padding: 60px; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                    <br><span style="font-size: 16px;">No hay productos en el inventario</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function toggleBloqueoProducto(productoId, estadoActual) {
    const accion = estadoActual ? 'bloquear' : 'desbloquear';
    const mensaje = estadoActual 
        ? '¿Está seguro que desea BLOQUEAR este producto?' 
        : '¿Está seguro que desea DESBLOQUEAR este producto?';
    const descripcion = estadoActual
        ? 'El producto no estará disponible para requisiciones mientras esté bloqueado.'
        : 'El producto volverá a estar disponible para requisiciones.';
    
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
                CustomModal.showSuccess(`Producto ${accion === 'bloquear' ? 'bloqueado' : 'desbloqueado'} exitosamente.`);
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + (data.message || 'No se pudo completar la operación'));
            }
        })
        .catch(error => {
            console.error('[v0] Error:', error);
            alert('Error al procesar la solicitud. Por favor, verifica la consola para más detalles.');
        });
    });
}
</script>

<?php require 'includes/footer.php'; ?>
