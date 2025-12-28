<?php require 'includes/header.php'; ?>
<?php // Eliminando require duplicado del sidebar ya que header.php ya lo incluye ?>

<!-- Cambiando fondo oscuro a fondo claro -->
<main class="main-content" style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); min-height: 100vh;">
    <div style="width: 100%; margin: 0 auto; padding: 20px;">
        <!-- Cambiando header oscuro a header claro con fondo blanco -->
        <div style="background: white; border: 1px solid rgba(41, 98, 255, 0.2); color: #1f2937; padding: 24px 32px; border-radius: 12px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-clipboard-list" style="font-size: 28px; color: #2962FF;"></i>
                <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Requisiciones de Compra</h1>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="margin-bottom: 24px;">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Sección de impresión con fondo claro -->
        <?php if ($user['rol'] === 'compras' || $user['rol'] === 'admin'): ?>
            <div style="background: white; border: 1px solid rgba(41, 98, 255, 0.2); border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-print" style="color: #2962FF;"></i>
                            Imprimir Requisiciones Aprobadas
                        </h3>
                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                            Selecciona las requisiciones aprobadas que deseas imprimir. Los artículos se agruparán por proveedor.
                        </p>
                    </div>
                    <button type="button" id="btn-imprimir-seleccionadas" class="btn btn-success" disabled style="white-space: nowrap; background: #2962FF; border: none;">
                        <i class="fas fa-print"></i> Imprimir Seleccionadas por Proveedor
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sección de filtros con fondo claro -->
        <div style="background: white; border: 1px solid rgba(41, 98, 255, 0.2); border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-filter" style="color: #2962FF; margin-right: 4px;"></i>
                Filtros
            </h3>
            <form method="GET" action="requisiciones.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                <div>
                    <label for="mes" style="display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px;">
                        <i class="fas fa-calendar-alt" style="color: #2962FF; margin-right: 4px;"></i> Mes
                    </label>
                    <!-- Cambiando select oscuro a select claro -->
                    <select name="mes" id="mes" onchange="this.form.submit()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                        <option value="">Todos</option>
                        <option value="01" <?php echo ($mes_filter == '01') ? 'selected' : ''; ?>>Enero</option>
                        <option value="02" <?php echo ($mes_filter == '02') ? 'selected' : ''; ?>>Febrero</option>
                        <option value="03" <?php echo ($mes_filter == '03') ? 'selected' : ''; ?>>Marzo</option>
                        <option value="04" <?php echo ($mes_filter == '04') ? 'selected' : ''; ?>>Abril</option>
                        <option value="05" <?php echo ($mes_filter == '05') ? 'selected' : ''; ?>>Mayo</option>
                        <option value="06" <?php echo ($mes_filter == '06') ? 'selected' : ''; ?>>Junio</option>
                        <option value="07" <?php echo ($mes_filter == '07') ? 'selected' : ''; ?>>Julio</option>
                        <option value="08" <?php echo ($mes_filter == '08') ? 'selected' : ''; ?>>Agosto</option>
                        <option value="09" <?php echo ($mes_filter == '09') ? 'selected' : ''; ?>>Septiembre</option>
                        <option value="10" <?php echo ($mes_filter == '10') ? 'selected' : ''; ?>>Octubre</option>
                        <option value="11" <?php echo ($mes_filter == '11') ? 'selected' : ''; ?>>Noviembre</option>
                        <option value="12" <?php echo ($mes_filter == '12') ? 'selected' : ''; ?>>Diciembre</option>
                    </select>
                </div>
                
                <div>
                    <label for="anio" style="display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px;">
                        <i class="fas fa-calendar" style="color: #2962FF; margin-right: 4px;"></i> Año
                    </label>
                    <select name="anio" id="anio" onchange="this.form.submit()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                        <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($anio_filter == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label for="estado" style="display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px;">
                        <i class="fas fa-tasks" style="color: #2962FF; margin-right: 4px;"></i> Estado
                    </label>
                    <select name="estado" id="estado" onchange="this.form.submit()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo ($estado_filter == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_compras" <?php echo ($estado_filter == 'en_compras') ? 'selected' : ''; ?>>En Compras</option>
                        <option value="en_gerencia" <?php echo ($estado_filter == 'en_gerencia') ? 'selected' : ''; ?>>En Gerencia</option>
                        <option value="aprobada" <?php echo ($estado_filter == 'aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                        <option value="rechazada" <?php echo ($estado_filter == 'rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                        <option value="completada" <?php echo ($estado_filter == 'completada') ? 'selected' : ''; ?>>Completada</option>
                    </select>
                </div>
                
                <?php if ($estado_filter || $mes_filter): ?>
                    <div>
                        <a href="requisiciones.php" class="btn btn-secondary" style="width: 100%; padding: 10px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Grid de cards con fondo claro -->
        <?php if (count($requisiciones) > 0): ?>
            <div class="requisiciones-grid">
                <?php foreach ($requisiciones as $req): ?>
                    <?php
                    // Determinar clase de color según estado
                    $estado_class = '';
                    $estado_icono = '';
                    switch($req['estado']) {
                        case 'pendiente':
                            $estado_class = 'req-card-pendiente';
                            $estado_icono = 'fa-clock';
                            break;
                        case 'en_compras':
                            $estado_class = 'req-card-compras';
                            $estado_icono = 'fa-shopping-cart';
                            break;
                        case 'en_gerencia':
                            $estado_class = 'req-card-gerencia';
                            $estado_icono = 'fa-user-tie';
                            break;
                        case 'aprobada':
                            $estado_class = 'req-card-aprobada';
                            $estado_icono = 'fa-check-circle';
                            break;
                        case 'rechazada':
                            $estado_class = 'req-card-rechazada';
                            $estado_icono = 'fa-times-circle';
                            break;
                        case 'completada':
                            $estado_class = 'req-card-completada';
                            $estado_icono = 'fa-check-double';
                            break;
                        default:
                            $estado_class = 'req-card-default';
                            $estado_icono = 'fa-file-alt';
                    }
                    ?>
                    <div class="req-card <?php echo $estado_class; ?>">
                        <!-- Agregando checkbox para selección de requisiciones aprobadas -->
                        <?php if (($user['rol'] === 'compras' || $user['rol'] === 'admin') && $req['estado'] === 'aprobada'): ?>
                            <div class="req-card-checkbox">
                                <input type="checkbox" class="req-checkbox" data-req-id="<?php echo $req['id']; ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="req-card-header">
                            <div class="req-folio">
                                <i class="fas fa-file-invoice"></i>
                                <strong><?php echo htmlspecialchars($req['folio']); ?></strong>
                            </div>
                            <div class="req-estado">
                                <i class="fas <?php echo $estado_icono; ?>"></i>
                                <span><?php echo ucfirst(str_replace('_', ' ', $req['estado'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="req-card-body">
                            <div class="req-info-row">
                                <span class="req-label"><i class="fas fa-calendar"></i> Fecha:</span>
                                <span class="req-value"><?php echo date('d/m/Y', strtotime($req['fecha_solicitud'])); ?></span>
                            </div>
                            <div class="req-info-row">
                                <span class="req-label"><i class="fas fa-user"></i> Solicitante:</span>
                                <span class="req-value"><?php echo htmlspecialchars($req['solicitante']); ?></span>
                            </div>
                            <div class="req-info-row">
                                <span class="req-label"><i class="fas fa-warehouse"></i> Sub-Almacén:</span>
                                <span class="req-value">
                                    <span class="badge badge-info"><?php echo htmlspecialchars($req['sub_almacen_nombre']); ?></span>
                                </span>
                            </div>
                            <?php if ($user['rol'] === 'admin' || $user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general'): ?>
                                <div class="req-info-row">
                                    <span class="req-label"><i class="fas fa-user-circle"></i> Usuario:</span>
                                    <span class="req-value"><?php echo htmlspecialchars($req['usuario_nombre']); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (($user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general') && $req['monto_cotizado']): ?>
                                <div class="req-info-row">
                                    <span class="req-label"><i class="fas fa-dollar-sign"></i> Monto:</span>
                                    <span class="req-value req-monto">$<?php echo number_format($req['monto_cotizado'], 2); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($req['observaciones']): ?>
                                <div class="req-info-row">
                                    <span class="req-label"><i class="fas fa-comment"></i> Observaciones:</span>
                                    <span class="req-value"><?php echo htmlspecialchars(substr($req['observaciones'], 0, 50)) . (strlen($req['observaciones']) > 50 ? '...' : ''); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="req-card-footer">
                            <a href="ver-requisicion.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                            
                            <!-- Agregar botón para agregar al inventario -->
                            <?php if (($user['rol'] === 'compras' || $user['rol'] === 'admin') && $req['estado'] === 'aprobada' && $req['agregado_a_inventario'] != 1): ?>
                                <a href="agregar-a-inventario.php?id=<?php echo $req['id']; ?>" 
                                   class="btn btn-sm btn-success agregar-inventario-btn"
                                   data-req-id="<?php echo $req['id']; ?>"
                                   data-req-folio="<?php echo htmlspecialchars($req['folio']); ?>">
                                    <i class="fas fa-plus-circle"></i> Agregar al Inventario
                                </a>
                            <?php elseif ($req['agregado_a_inventario'] == 1): ?>
                                <span class="btn btn-sm btn-secondary" disabled>
                                    <i class="fas fa-check"></i> Ya en Inventario
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($user['rol'] === 'admin'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="requisicion_id" value="<?php echo $req['id']; ?>">
                                    <select name="nuevo_estado" class="req-estado-select">
                                        <option value="pendiente" <?php echo ($req['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="en_compras" <?php echo ($req['estado'] == 'en_compras') ? 'selected' : ''; ?>>En Compras</option>
                                        <option value="en_gerencia" <?php echo ($req['estado'] == 'en_gerencia') ? 'selected' : ''; ?>>En Gerencia</option>
                                        <option value="aprobada" <?php echo ($req['estado'] == 'aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                                        <option value="rechazada" <?php echo ($req['estado'] == 'rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                                        <option value="completada" <?php echo ($req['estado'] == 'completada') ? 'selected' : ''; ?>>Completada</option>
                                    </select>
                                    <button type="submit" name="cambiar_estado" class="btn btn-sm btn-success" style="background: #2962FF; border: none;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Cambiando empty state a fondo claro -->
            <div class="empty-state" style="background: white; border: 1px solid rgba(41, 98, 255, 0.2); padding: 60px 20px; border-radius: 12px; text-align: center; color: #6b7280;">
                <i class="fas fa-inbox" style="font-size: 64px; opacity: 0.3; margin-bottom: 20px; color: #2962FF;"></i>
                <h3 style="color: #1f2937;">No hay requisiciones</h3>
                <p>
                    No se encontraron requisiciones
                    <?php if ($estado_filter || $mes_filter): ?>
                        con los filtros seleccionados
                    <?php endif; ?>
                </p>
                <?php if ($estado_filter || $mes_filter): ?>
                    <a href="requisiciones.php" class="btn btn-primary" style="background: #2962FF; border: none; margin-top: 16px;">Limpiar Filtros</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.req-checkbox');
    const btnImprimir = document.getElementById('btn-imprimir-seleccionadas');
    
    if (!btnImprimir) return;
    
    function actualizarBotonImprimir() {
        const seleccionadas = Array.from(checkboxes).filter(cb => cb.checked);
        btnImprimir.disabled = seleccionadas.length === 0;
        btnImprimir.innerHTML = `<i class="fas fa-print"></i> Imprimir ${seleccionadas.length} Requisicion(es) por Proveedor`;
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarBotonImprimir);
    });
    
    btnImprimir.addEventListener('click', function() {
        const seleccionadas = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.reqId);
        
        if (seleccionadas.length > 0) {
            // Crear un formulario temporal para enviar por POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'imprimir-requisiciones.php'; // Cambiando action a imprimir-requisiciones.php
            form.target = '_blank';
            
            // Agregar los IDs como inputs
            seleccionadas.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'requisiciones[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    });
});
</script>

<?php require 'includes/footer.php'; ?>
