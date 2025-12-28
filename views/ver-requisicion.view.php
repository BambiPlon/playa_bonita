<?php require 'includes/header.php'; ?>
<?php // Eliminando require duplicado del sidebar ya que header.php ya lo incluye ?>

<main class="main-content" style="width: 100%; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); min-height: 100vh;">
    <!-- Cambiando header oscuro a header claro -->
    <div style="background: white; color: #1f2937; padding: 25px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(16, 185, 129, 0.2);">
        <h1 style="margin: 0; font-size: 28px; display: flex; align-items: center; gap: 12px;">
            <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-file-alt" style="color: #10b981; font-size: 24px;"></i>
            </div>
            Detalle de Requisición
        </h1>
        <a href="requisiciones.php" class="btn btn-secondary" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 2px solid #10b981; padding: 10px 20px; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Cambiando cards oscuros a claros -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(16, 185, 129, 0.2);">
            <h2 style="margin: 0; font-size: 24px; color: #1f2937;">Requisición <?php echo htmlspecialchars($requisicion['folio']); ?></h2>
            <span class="badge badge-<?php 
                echo $requisicion['estado'] === 'pendiente' ? 'warning' : 
                     ($requisicion['estado'] === 'aprobada' ? 'success' : 
                     ($requisicion['estado'] === 'rechazada' ? 'danger' : 'info')); 
            ?>">
                <?php echo ucfirst(str_replace('_', ' ', $requisicion['estado'])); ?>
            </span>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <strong style="color: #6b7280;">Solicitante:</strong>
                <span style="color: #1f2937;"><?php echo htmlspecialchars($requisicion['solicitante']); ?></span>
            </div>
            <div class="info-item">
                <strong style="color: #6b7280;">Sub-Almacén:</strong>
                <span style="color: #1f2937;"><?php echo htmlspecialchars($requisicion['sub_almacen_nombre']); ?></span>
            </div>
            <div class="info-item">
                <strong style="color: #6b7280;">Fecha de Solicitud:</strong>
                <span style="color: #1f2937;"><?php echo date('d/m/Y', strtotime($requisicion['fecha_solicitud'])); ?></span>
            </div>
            <div class="info-item">
                <strong style="color: #6b7280;">Usuario:</strong>
                <span style="color: #1f2937;"><?php echo htmlspecialchars($requisicion['usuario_nombre']); ?></span>
            </div>
            <?php if ($requisicion['monto_cotizado']): ?>
            <div class="info-item">
                <strong style="color: #6b7280;">Monto Cotizado:</strong>
                <span style="color: #10b981; font-weight: 600;">$<?php echo number_format($requisicion['monto_cotizado'], 2); ?></span>
            </div>
            <div class="info-item">
                <strong style="color: #6b7280;">Fecha de Cotización:</strong>
                <span style="color: #1f2937;"><?php echo date('d/m/Y H:i', strtotime($requisicion['fecha_cotizacion'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($requisicion['observaciones']): ?>
        <div class="mt-3">
            <strong style="color: #6b7280;">Observaciones:</strong>
            <p style="color: #1f2937; margin-top: 8px;"><?php echo nl2br(htmlspecialchars($requisicion['observaciones'])); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if ($requisicion['justificacion_rechazo']): ?>
        <div class="mt-3 alert alert-warning">
            <strong><i class="fas fa-exclamation-triangle"></i> Justificación de Rechazo/Modificación:</strong>
            <p style="color: #1f2937;"><?php echo nl2br(htmlspecialchars($requisicion['justificacion_rechazo'])); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Agregando tabla de productos visible para TODOS los estados y roles -->
    <!--Tabla de Productos Solicitados - Siempre Visible -->
    <div class="card mt-3" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.12); padding: 0; margin-bottom: 30px; border: 1px solid rgba(41, 98, 255, 0.1); overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 24px 30px; margin-bottom: 0;">
            <h3 style="color: white; margin: 0; font-weight: 600; font-size: 20px;">
                <i class="fas fa-box"></i> Productos Solicitados
            </h3>
        </div>
        <div class="card-body" style="padding: 30px;">
            <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(10, 25, 47, 0.08);">
                <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #1d3557 0%, #2962FF 100%);">
                            <th style="color: white; padding: 18px 20px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Producto</th>
                            <th style="color: white; padding: 18px 20px; text-align: center; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Cantidad</th>
                            <th style="color: white; padding: 18px 20px; text-align: center; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Unidad</th>
                            <?php if ($requisicion['estado'] !== 'pendiente'): ?>
                            <th style="color: white; padding: 18px 20px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Proveedor</th>
                            <th style="color: white; padding: 18px 20px; text-align: right; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Precio Cotizado</th>
                            <th style="color: white; padding: 18px 20px; text-align: right; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Subtotal</th>
                            <?php endif; ?>
                            <?php if (in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada'])): ?>
                            <th style="color: white; padding: 18px 20px; text-align: center; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalMostrado = 0;
                        foreach ($detalles as $detalle): 
                            $precioCotizado = $detalle['precio_cotizado'] ?? 0;
                            $subtotal = $precioCotizado * $detalle['cantidad'];
                            if ($detalle['aprobado']) {
                                $totalMostrado += $subtotal;
                            }
                        ?>
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.2s ease; background: white;" 
                            onmouseover="this.style.background='#f8fafc'" 
                            onmouseout="this.style.background='white'">
                            <td style="color: #1f2937; padding: 20px; font-weight: 500;"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                            <td style="color: #1f2937; padding: 20px; text-align: center; font-weight: 500;"><?php echo $detalle['cantidad']; ?></td>
                            <td style="color: #64748b; padding: 20px; text-align: center;"><?php echo htmlspecialchars($detalle['unidad']); ?></td>
                            <?php if ($requisicion['estado'] !== 'pendiente'): ?>
                            <td style="color: #1f2937; padding: 20px;"><?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? 'No asignado'); ?></td>
                            <td style="color: #1f2937; padding: 20px; text-align: right; font-weight: 500;">$<?php echo number_format($precioCotizado, 2); ?></td>
                            <td style="color: #2962FF; padding: 20px; text-align: right; font-weight: 600; font-size: 15px;">$<?php echo number_format($subtotal, 2); ?></td>
                            <?php endif; ?>
                            <?php if (in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada'])): ?>
                            <td style="padding: 20px; text-align: center;">
                                <?php if ($detalle['aprobado']): ?>
                                    <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">Aprobado</span>
                                <?php else: ?>
                                    <span style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($requisicion['estado'] !== 'pendiente' && $requisicion['monto_cotizado']): ?>
                        <tr style="background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%); border-top: 2px solid #2962FF;">
                            <td colspan="<?php echo in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada']) ? '5' : '4'; ?>" style="color: #1f2937; font-weight: 600; padding: 24px 20px; text-align: right; font-size: 16px;">
                                <?php echo in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada']) ? 'Total Aprobado:' : 'Total Cotizado:'; ?>
                            </td>
                            <td colspan="<?php echo in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada']) ? '2' : '1'; ?>" style="padding: 24px 20px; text-align: right;">
                                <strong style="color: #2962FF; font-size: 24px; font-weight: 700; text-shadow: 0 2px 4px rgba(41, 98, 255, 0.1);">
                                    $<?php echo number_format(in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada']) ? $totalMostrado : $requisicion['monto_cotizado'], 2); ?>
                                </strong>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (in_array($requisicion['estado'], ['aprobada', 'rechazada', 'completada'])): ?>
            <!-- Mostrar justificaciones de productos rechazados si existen -->
            <?php 
            $productosRechazados = array_filter($detalles, function($d) { return !$d['aprobado'] && $d['justificacion_rechazo']; });
            if (count($productosRechazados) > 0): 
            ?>
            <div class="mt-4" style="margin-top: 24px; padding: 20px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px;">
                <h4 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Productos Rechazados:
                </h4>
                <?php foreach ($productosRechazados as $prod): ?>
                <div style="margin-bottom: 8px;">
                    <strong style="color: #78350f;"><?php echo htmlspecialchars($prod['producto_nombre']); ?>:</strong>
                    <span style="color: #92400e;"><?php echo htmlspecialchars($prod['justificacion_rechazo']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulario de cotización por artículo para compras -->
    <?php if ($user['rol'] === 'compras' && $requisicion['estado'] === 'pendiente'): ?>
    <!-- Rediseño completo de la card de cotización con tema azul marino minimalista -->
    <div class="card mt-3" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.12); padding: 0; margin-bottom: 30px; border: 1px solid rgba(41, 98, 255, 0.1); overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 24px 30px; margin-bottom: 0;">
            <h3 style="color: white; margin: 0; font-weight: 600; font-size: 20px;">Cotizar Requisición por Artículo</h3>
        </div>
        <div class="card-body" style="padding: 30px;">
            <form action="cotizar-requisicion.php" method="POST" id="formCotizar">
                <input type="hidden" name="requisicion_id" value="<?php echo $requisicion['id']; ?>">
                
                <!-- Nueva tabla con diseño claro y espaciado -->
                <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(10, 25, 47, 0.08);">
                    <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1d3557 0%, #2962FF 100%);">
                                <th style="color: white; padding: 18px 20px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Producto</th>
                                <th style="color: white; padding: 18px 20px; text-align: center; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Cantidad</th>
                                <th style="color: white; padding: 18px 20px; text-align: center; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Unidad</th>
                                <th style="color: white; padding: 18px 20px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Proveedor</th>
                                <th style="color: white; padding: 18px 20px; text-align: right; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Precio Cotizado</th>
                                <th style="color: white; padding: 18px 20px; text-align: right; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $proveedorModel = new Proveedor();
                            $proveedores_list = $proveedorModel->obtenerTodos(true);
                            
                            foreach ($detalles as $detalle): ?>
                            <!-- Filas con hover y mejor espaciado -->
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.2s ease; background: white;" 
                                onmouseover="this.style.background='#f8fafc'" 
                                onmouseout="this.style.background='white'">
                                <td style="color: #1f2937; padding: 20px; font-weight: 500;"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                <td style="color: #1f2937; padding: 20px; text-align: center; font-weight: 500;"><?php echo $detalle['cantidad']; ?></td>
                                <td style="color: #64748b; padding: 20px; text-align: center;"><?php echo htmlspecialchars($detalle['unidad']); ?></td>
                                <td style="padding: 20px;">
                                    <!-- Select y botón mejorados con tema azul -->
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <select class="form-control proveedor-select" 
                                                name="proveedores[<?php echo $detalle['id']; ?>]" 
                                                style="flex: 1; background: #f8fafc; border: 2px solid #e2e8f0; color: #1f2937; padding: 10px 14px; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; outline: none;"
                                                onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($proveedores_list as $prov): ?>
                                                <option value="<?php echo $prov['id']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" onclick="abrirModalProveedor(<?php echo $detalle['id']; ?>)" 
                                                style="background: #2962FF; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; white-space: nowrap; font-weight: 500; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(41, 98, 255, 0.25);"
                                                onmouseover="this.style.background='#1d4ed8'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)'"
                                                onmouseout="this.style.background='#2962FF'; this.style.boxShadow='0 2px 8px rgba(41, 98, 255, 0.25)'">
                                            <i class="fas fa-plus"></i> Nuevo
                                        </button>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <!-- Input de precio mejorado -->
                                    <input type="number" 
                                           step="0.01" 
                                           class="form-control precio-cotizado" 
                                           name="precios[<?php echo $detalle['id']; ?>]" 
                                           value="<?php echo $detalle['precio_unitario'] ?? 0; ?>"
                                           data-cantidad="<?php echo $detalle['cantidad']; ?>"
                                           style="background: #f8fafc; border: 2px solid #e2e8f0; color: #1f2937; padding: 10px 14px; border-radius: 8px; text-align: right; font-weight: 500; font-size: 14px; transition: all 0.2s ease; outline: none;"
                                           onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)'"
                                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                                           required>
                                </td>
                                <td class="subtotal" style="color: #2962FF; font-weight: 700; padding: 20px; text-align: right; font-size: 16px;">$0.00</td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- Fila de total con diseño destacado -->
                            <tr style="background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%); border-top: 2px solid #2962FF;">
                                <td colspan="5" style="color: #1f2937; font-weight: 600; padding: 24px 20px; text-align: right; font-size: 16px;">Total Cotizado:</td>
                                <td style="padding: 24px 20px; text-align: right;">
                                    <strong id="totalGeneral" style="color: #2962FF; font-size: 24px; font-weight: 700; text-shadow: 0 2px 4px rgba(41, 98, 255, 0.1);">$0.00</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Botón de envío mejorado -->
                <div class="mt-3" style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="button" onclick="confirmarCotizacion()" class="btn btn-primary" 
                            style="background: linear-gradient(135deg, #2962FF 0%, #1d4ed8 100%); color: white; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(41, 98, 255, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(41, 98, 255, 0.3)'">
                        <i class="fas fa-paper-plane"></i> Enviar Cotización a Gerencia
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal para agregar proveedor rápido -->
    <div id="modalProveedor" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%;">
            <h3 style="color: #1f2937; margin-bottom: 20px;">Agregar Proveedor</h3>
            <form id="formNuevoProveedor">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; color: #10b981; font-weight: 600; margin-bottom: 5px;">Nombre del Proveedor *</label>
                    <input type="text" id="nombreProveedor" required style="width: 100%; padding: 10px; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; color: #10b981; font-weight: 600; margin-bottom: 5px;">Contacto</label>
                    <input type="text" id="contactoProveedor" style="width: 100%; padding: 10px; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; color: #10b981; font-weight: 600; margin-bottom: 5px;">Teléfono</label>
                    <input type="text" id="telefonoProveedor" style="width: 100%; padding: 10px; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px;">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" style="flex: 1; background: #10b981; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">Guardar</button>
                    <button type="button" onclick="cerrarModalProveedor()" style="flex: 1; background: #6b7280; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    // Calcular totales automáticamente
    document.querySelectorAll('.precio-cotizado').forEach(input => {
        input.addEventListener('input', calcularTotales);
    });
    
    function calcularTotales() {
        let total = 0;
        document.querySelectorAll('.precio-cotizado').forEach(input => {
            const precio = parseFloat(input.value) || 0;
            const cantidad = parseInt(input.dataset.cantidad) || 0;
            const subtotal = precio * cantidad;
            
            const row = input.closest('tr');
            row.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
            
            total += subtotal;
        });
        
        document.getElementById('totalGeneral').textContent = '$' + total.toFixed(2);
    }
    
    let detalleIdActual = null;
    
    function abrirModalProveedor(detalleId) {
        detalleIdActual = detalleId;
        document.getElementById('modalProveedor').style.display = 'flex';
    }
    
    function cerrarModalProveedor() {
        document.getElementById('modalProveedor').style.display = 'none';
        document.getElementById('formNuevoProveedor').reset();
        detalleIdActual = null;
    }
    
    document.getElementById('formNuevoProveedor').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const datos = {
            nombre: document.getElementById('nombreProveedor').value,
            contacto: document.getElementById('contactoProveedor').value,
            telefono: document.getElementById('telefonoProveedor').value
        };
        
        fetch('agregar-proveedor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Agregar el nuevo proveedor al select correspondiente
                const select = document.querySelector(`select[name="proveedores[${detalleIdActual}]"]`);
                const option = document.createElement('option');
                option.value = data.proveedor_id;
                option.textContent = data.proveedor_nombre;
                option.selected = true;
                select.appendChild(option);
                
                // Actualizar también todos los demás selects
                document.querySelectorAll('.proveedor-select').forEach(otroSelect => {
                    if (otroSelect !== select) {
                        const optionCopy = document.createElement('option');
                        optionCopy.value = data.proveedor_id;
                        optionCopy.textContent = data.proveedor_nombre;
                        otroSelect.appendChild(optionCopy);
                    }
                });
                
                cerrarModalProveedor();
                alert('Proveedor agregado exitosamente');
            } else {
                alert('Error al agregar proveedor: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar proveedor');
        });
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modalProveedor').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalProveedor();
        }
    });
    
    // Calcular totales al cargar
    calcularTotales();
    
    function confirmarCotizacion() {
        // Validar que todos los campos requeridos estén llenos
        const proveedores = document.querySelectorAll('.proveedor-select');
        const precios = document.querySelectorAll('.precio-cotizado');
        let valid = true;
        
        proveedores.forEach(select => {
            if (!select.value) {
                valid = false;
            }
        });
        
        precios.forEach(input => {
            if (!input.value || parseFloat(input.value) <= 0) {
                valid = false;
            }
        });
        
        if (!valid) {
            alertaError('Por favor, completa todos los campos de proveedor y precio cotizado');
            return;
        }
        
        const total = document.getElementById('totalGeneral').textContent;
        
        Swal.fire({
            title: '¿Enviar cotización a Gerencia?',
            html: `<p>Se enviará la cotización con un total de <strong>${total}</strong></p>
                   <p class="text-muted">Esta acción notificará a Gerencia para su aprobación.</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formCotizar').submit();
            }
        });
    }
    </script>
    <?php endif; ?>

    <!-- Formulario de aprobación parcial con justificación para gerencia -->
    <?php if (in_array($user['rol'], ['gerencia', 'gerencia_general']) && 
              (($user['rol'] === 'gerencia' && $requisicion['estado'] === 'en_gerencia') || 
               ($user['rol'] === 'gerencia_general' && $requisicion['estado'] === 'en_gerencia_general'))): ?>
    
    <!-- Eliminando tabla de productos duplicada para gerencia ya que ahora está visible arriba -->
    
    <div class="card mt-3" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.2);">
        <div class="card-header">
            <h3 style="color: #1f2937; margin: 0;">Revisar y Aprobar Requisición</h3>
            <?php if ($user['rol'] === 'gerencia_general'): ?>
                <span class="badge badge-warning">Aprobación Final</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form action="aprobar-requisicion.php" method="POST" id="formAprobar">
                <input type="hidden" name="requisicion_id" value="<?php echo $requisicion['id']; ?>">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <?php if ($user['rol'] === 'gerencia'): ?>
                        Puedes aprobar la requisición completa o desmarcar los artículos que no serán aprobados.
                        Al aprobar, la requisición se enviará a Gerencia General para aprobación final.
                    <?php else: ?>
                        Esta es la aprobación final. Revisa cuidadosamente antes de aprobar.
                    <?php endif; ?>
                    Si rechazas artículos o la requisición completa, debes proporcionar una justificación.
                </div>
                
                <div class="table-responsive">
                    <!-- Aplicando diseño claro minimalista con azul marino -->
                    <table class="data-table" style="width: 100%; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1d3557 0%, #2962FF 100%);">
                                <th width="50" style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Aprobar</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Producto</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Cantidad</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Unidad</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Proveedor</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Precio Cotizado</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Subtotal</th>
                                <th style="color: white; padding: 16px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Justificación (si rechaza)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCotizado = 0;
                            foreach ($detalles as $detalle): 
                                $precioCotizado = $detalle['precio_cotizado'] ?? 0;
                                $subtotal = $precioCotizado * $detalle['cantidad'];
                                if ($detalle['aprobado']) {
                                    $totalCotizado += $subtotal;
                                }
                            ?>
                            <tr class="<?php echo !$detalle['aprobado'] ? 'rechazado' : ''; ?>" style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                <td class="text-center" style="padding: 16px;">
                                    <input type="checkbox" 
                                           class="articulo-check" 
                                           name="articulos_aprobados[]" 
                                           value="<?php echo $detalle['id']; ?>"
                                           <?php echo $detalle['aprobado'] ? 'checked' : ''; ?>
                                           style="width: 18px; height: 18px; cursor: pointer; accent-color: #2962FF;">
                                </td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500;"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                <td style="color: #1f2937; padding: 16px;">
                                    <?php if (in_array($user['rol'], ['gerencia', 'gerencia_general']) && in_array($requisicion['estado'], ['en_gerencia', 'en_gerencia_general'])): ?>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control form-control-sm cantidad-input" 
                                               name="cantidades[<?php echo $detalle['id']; ?>]" 
                                               value="<?php echo $detalle['cantidad']; ?>"
                                               min="0"
                                               style="background: white; border: 2px solid #e5e7eb; color: #1f2937; padding: 8px 12px; border-radius: 8px; width: 100px; transition: border-color 0.2s;" 
                                               onfocus="this.style.borderColor='#2962FF'" 
                                               onblur="this.style.borderColor='#e5e7eb'">
                                    <?php else: ?>
                                        <?php echo $detalle['cantidad']; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #6b7280; padding: 16px;"><?php echo htmlspecialchars($detalle['unidad']); ?></td>
                                <td style="color: #6b7280; padding: 16px;"><?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? ''); ?></td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500;">$<?php echo number_format($precioCotizado, 2); ?></td>
                                <td style="color: #2962FF; padding: 16px; font-weight: 600;">$<?php echo number_format($subtotal, 2); ?></td>
                                <td style="padding: 16px;">
                                    <input type="text" 
                                           class="form-control form-control-sm justificacion-articulo" 
                                           name="justificaciones[<?php echo $detalle['id']; ?>]"
                                           value="<?php echo htmlspecialchars($detalle['justificacion_rechazo'] ?? ''); ?>"
                                           placeholder="Requerido si desmarca el artículo"
                                           style="background: white; border: 2px solid #e5e7eb; color: #1f2937; padding: 8px 12px; border-radius: 8px; transition: border-color 0.2s;"
                                           onfocus="this.style.borderColor='#2962FF'" 
                                           onblur="this.style.borderColor='#e5e7eb'">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="total-row" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-top: 2px solid #2962FF;">
                                <td colspan="6" class="text-right" style="color: #1f2937; font-weight: 700; padding: 20px; font-size: 16px;"><strong>Total Cotizado:</strong></td>
                                <td colspan="2" style="padding: 20px;"><strong style="color: #2962FF; font-size: 24px; font-weight: 700;">$<?php echo number_format($totalCotizado, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="form-group mt-3">
                    <label for="justificacion_general" style="color: #374151; font-weight: 600; margin-bottom: 8px; display: block;">Justificación General (opcional)</label>
                    <textarea class="form-control" 
                              id="justificacion_general" 
                              name="justificacion_general" 
                              rows="3"
                              placeholder="Agregue observaciones o comentarios adicionales"
                              style="background: white; border: 2px solid #e5e7eb; color: #1f2937; padding: 12px; border-radius: 8px; transition: border-color 0.2s; resize: vertical;"
                              onfocus="this.style.borderColor='#2962FF'" 
                              onblur="this.style.borderColor='#e5e7eb'"><?php echo htmlspecialchars($requisicion['justificacion_rechazo'] ?? ''); ?></textarea>
                </div>
                
                <div class="mt-3" style="margin-top: 24px; display: flex; gap: 12px;">
                    <!-- Actualizando botones con diseño azul marino -->
                    <button type="submit" name="accion" value="aprobar" class="btn btn-success" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(41, 98, 255, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(41, 98, 255, 0.3)'">
                        <i class="fas fa-check"></i> 
                        <?php echo $user['rol'] === 'gerencia' ? 'Enviar a Gerencia General' : 'Aprobar Requisición Final'; ?>
                    </button>
                    <button type="submit" name="accion" value="rechazar" class="btn btn-danger" id="btnRechazar" style="background: #ef4444; color: white; border: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(239, 68, 68, 0.3)'">
                        <i class="fas fa-times"></i> Rechazar Requisición Completa
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    // Validar que si desmarca un artículo, debe proporcionar justificación
    document.getElementById('formAprobar').addEventListener('submit', function(e) {
        const accion = e.submitter.value;
        
        if (accion === 'rechazar') {
            const justificacion = document.getElementById('justificacion_general').value.trim();
            if (!justificacion) {
                e.preventDefault();
                alert('Debe proporcionar una justificación para rechazar la requisición completa');
                document.getElementById('justificacion_general').focus();
                return false;
            }
        }
        
        if (accion === 'aprobar') {
            let error = false;
            document.querySelectorAll('.articulo-check').forEach(checkbox => {
                if (!checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const justInput = row.querySelector('.justificacion-articulo');
                    if (!justInput.value.trim()) {
                        error = true;
                        justInput.classList.add('is-invalid');
                    } else {
                        justInput.classList.remove('is-invalid');
                    }
                }
            });
            
            if (error) {
                e.preventDefault();
                alert('Debe proporcionar justificación para cada artículo que desmarcó');
                return false;
            }
            
            // Verificar que al menos un artículo esté aprobado
            const algnoAprobado = Array.from(document.querySelectorAll('.articulo-check'))
                .some(cb => cb.checked);
            
            if (!algnoAprobado) {
                e.preventDefault();
                alert('Debe aprobar al menos un artículo o rechazar la requisición completa');
                return false;
            }
        }
    });
    
    function confirmarAprobacion() {
        const articulosAprobados = document.querySelectorAll('input[name="articulos_aprobados[]"]:checked');
        
        if (articulosAprobados.length === 0) {
            alertaError('Debes aprobar al menos un artículo');
            return;
        }
        
        const accion = <?php echo $user['rol'] === 'gerencia' ? '"enviar a Gerencia General"' : '"aprobar la requisición"'; ?>;
        const titulo = <?php echo $user['rol'] === 'gerencia' ? '"¿Enviar a Gerencia General?"' : '"¿Aprobar Requisición?"'; ?>;
        
        Swal.fire({
            title: titulo,
            html: `<p>Has seleccionado <strong>${articulosAprobados.length}</strong> artículos para aprobar.</p>
                   <p class="text-muted">Esta acción notificará al siguiente nivel.</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('button[name="accion"][value="aprobar"]').click();
            }
        });
    }

    function confirmarRechazo() {
        const justificacion = document.getElementById('justificacion_general').value.trim();
        
        Swal.fire({
            title: '¿Rechazar Requisición?',
            html: `<div style="text-align: left;">
                    <p>Esta acción rechazará toda la requisición.</p>
                    <label style="display: block; margin-top: 10px; font-weight: 600;">Justificación *</label>
                    <textarea id="justificacionSwal" class="swal2-input" style="width: 100%; height: 100px;" placeholder="Explica el motivo del rechazo...">${justificacion}</textarea>
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const justif = document.getElementById('justificacionSwal').value.trim();
                if (!justif) {
                    Swal.showValidationMessage('La justificación es obligatoria');
                    return false;
                }
                return justif;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('justificacion_general').value = result.value;
                document.querySelector('button[name="accion"][value="rechazar"]').click();
            }
        });
    }
    </script>
    <?php endif; ?>
    
    <!-- Eliminando sección duplicada de productos aprobados/rechazados ya que está visible arriba -->
</main>

<?php require 'includes/footer.php'; ?>
