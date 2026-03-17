<?php require 'includes/header.php'; ?>
<?php // Eliminando require duplicado del sidebar ya que header.php ya lo incluye ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Detalle de Requisicion</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Informacion completa de la requisicion</p>
        </div>
        <a href="requisiciones.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card" style="margin-bottom: 1.5rem; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 1.0625rem; color: var(--gray-900); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-invoice" style="color: var(--primary);"></i>
                <?php echo htmlspecialchars($requisicion['folio']); ?>
            </h2>
            <span class="badge badge-<?php 
                if ($requisicion['estado'] === 'pendiente') echo 'warning';
                elseif ($requisicion['estado'] === 'aprobada') echo 'success';
                elseif ($requisicion['estado'] === 'rechazada') echo 'danger';
                else echo 'primary';
            ?>">
                <?php echo ucfirst(str_replace('_', ' ', $requisicion['estado'])); ?>
            </span>
        </div>
        
        <div style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-calendar-alt" style="color: var(--primary); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Fecha</div>
                        <div style="color: var(--gray-900); font-size: 0.875rem; font-weight: 600;"><?php echo date('d/m/Y', strtotime($requisicion['fecha_solicitud'])); ?></div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-user" style="color: var(--primary); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Solicitante</div>
                        <div style="color: var(--gray-900); font-size: 0.875rem; font-weight: 600;"><?php echo htmlspecialchars($requisicion['solicitante']); ?></div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-building" style="color: var(--primary); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Departamento</div>
                        <div style="color: var(--gray-900); font-size: 0.875rem; font-weight: 600;"><?php echo htmlspecialchars($requisicion['sub_almacen_nombre'] ?? 'No asignado'); ?></div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-user-circle" style="color: var(--primary); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Usuario</div>
                        <div style="color: var(--gray-900); font-size: 0.875rem; font-weight: 600;"><?php echo htmlspecialchars($requisicion['usuario_nombre']); ?></div>
                    </div>
                </div>
                
                <?php if ($requisicion['monto_cotizado']): ?>
                <?php 
                $porcentajeIvaInfo = isset($requisicion['porcentaje_iva']) && $requisicion['porcentaje_iva'] > 0 ? floatval($requisicion['porcentaje_iva']) : 16;
                $montoConIvaInfo = $requisicion['monto_cotizado'] * (1 + ($porcentajeIvaInfo / 100));
                ?>
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--success-light); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-dollar-sign" style="color: var(--success); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Monto (IVA <?php echo $porcentajeIvaInfo; ?>%)</div>
                        <div style="color: var(--success); font-size: 1.125rem; font-weight: 700;">$<?php echo number_format($montoConIvaInfo, 2); ?></div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                    <div style="width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-clock" style="color: var(--primary); font-size: 0.875rem;"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Cotizacion</div>
                        <div style="color: var(--gray-900); font-size: 0.875rem; font-weight: 600;"><?php echo date('d/m/Y H:i', strtotime($requisicion['fecha_cotizacion'])); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($requisicion['observaciones']): ?>
            <div style="margin-top: 1rem; padding: 1rem; background: var(--gray-50); border-radius: 10px;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <i class="fas fa-comment-alt" style="color: var(--primary); font-size: 0.75rem;"></i>
                    <span style="color: var(--text-muted); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Justificacion</span>
                </div>
                <p style="color: var(--gray-900); margin: 0; line-height: 1.5; font-size: 0.8125rem;"><?php echo nl2br(htmlspecialchars($requisicion['observaciones'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($requisicion['justificacion_rechazo']): ?>
            <div style="margin-top: 1rem; padding: 1rem; background: var(--warning-light); border-radius: 10px; border-left: 3px solid var(--warning);">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle" style="color: var(--warning); font-size: 0.75rem;"></i>
                    <span style="color: #92400e; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Justificacion de Rechazo</span>
                </div>
                <p style="color: #92400e; margin: 0; line-height: 1.5; font-size: 0.8125rem; font-weight: 500;"><?php echo nl2br(htmlspecialchars($requisicion['justificacion_rechazo'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!--Formulario de cotización por artículo para compras -->
    <?php if ($user['rol'] === 'compras' && $requisicion['estado'] === 'pendiente'): ?>
    <div class="card" style="margin-bottom: 1.5rem; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100);">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--gray-900); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-invoice-dollar" style="color: var(--primary);"></i>
                Cotizar Requisicion por Articulo
            </h3>
        </div>
        <div style="padding: 1.5rem;">
            <form action="cotizar-requisicion.php" method="POST" id="formCotizar">
                <input type="hidden" name="requisicion_id" value="<?php echo $requisicion['id']; ?>">
                
                <!-- Nueva tabla con diseño claro y espaciado -->
                <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(10, 25, 47, 0.08);">
                    <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: var(--gray-50);">
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Cantidad</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Unidad</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Proveedor</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Precio Cotizado</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Subtotal</th>
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
                                <td style="color: #64748b; padding: 20px; text-align: center;"><?php echo ucfirst(htmlspecialchars($detalle['unidad'] ?? 'pieza')); ?></td>
                                <td style="padding: 20px;">
                                    <!-- Select y botón mejorados con tema azul -->
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <select class="form-control proveedor-select" 
                                                name="proveedores[<?php echo $detalle['id']; ?>]" 
                                                style="flex: 1; background: #f8fafc; border: 2px solid #e2e8f0; color: #1f2937; padding: 10px 14px; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; outline: none;"
                                                onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($proveedores_list as $prov): ?>
                                                <option value="<?php echo $prov['id']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" onclick="abrirModalProveedor(<?php echo $detalle['id']; ?>)" 
                                                style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; white-space: nowrap; font-weight: 500; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);"
                                                onmouseover="this.style.background='#1d4ed8'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.4)'"
                                                onmouseout="this.style.background='#2563eb'; this.style.boxShadow='0 2px 8px rgba(37, 99, 235, 0.25)'">
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
                                           placeholder="Precio sin IVA"
                                           style="background: #f8fafc; border: 2px solid #e2e8f0; color: #1f2937; padding: 10px 14px; border-radius: 8px; text-align: right; font-weight: 500; font-size: 14px; transition: all 0.2s ease; outline: none;"
                                           onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                                           required>
                                </td>
                                <td class="subtotal" style="color: #2563eb; font-weight: 700; padding: 20px; text-align: right; font-size: 16px;">$0.00</td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- Fila de total con diseño destacado -->
                            <tr style="background: var(--gray-50); border-top: 2px solid #2563eb;">
                                <td colspan="5" style="color: #1f2937; font-weight: 600; padding: 24px 20px; text-align: right; font-size: 14px;">Subtotal (sin IVA):</td>
                                <td style="padding: 24px 20px; text-align: right;">
                                    <strong id="subtotalGeneral" style="color: #2563eb; font-size: 18px; font-weight: 700;">$0.00</strong>
                                </td>
                            </tr>
                            <!-- Agregar selector de porcentaje de IVA -->
                            <tr style="background: var(--gray-50);">
                                <td colspan="5" style="color: #1f2937; font-weight: 600; padding: 20px; text-align: right; font-size: 14px;">
                                    IVA:
                                    <select id="porcentajeIva" name="porcentaje_iva" style="margin-left: 10px; padding: 5px 10px; border: 2px solid #2563eb; border-radius: 6px; background: white; color: #1f2937; font-weight: 600; cursor: pointer; outline: none;">
                                        <option value="16">16%</option>
                                        <option value="8">8%</option>
                                    </select>
                                </td>
                                <td style="padding: 20px; text-align: right;">
                                    <strong id="ivaGeneral" style="color: #6b7280; font-size: 18px; font-weight: 600;">$0.00</strong>
                                </td>
                            </tr>
                            <!-- </CHANGE> -->
                            <tr style="background: var(--gray-50); border-top: 2px solid #2563eb;">
                                <td colspan="5" style="color: #1f2937; font-weight: 600; padding: 24px 20px; text-align: right; font-size: 16px;">Total con IVA:</td>
                                <td style="padding: 24px 20px; text-align: right;">
                                    <strong id="totalGeneral" style="color: #2563eb; font-size: 24px; font-weight: 700; text-shadow: 0 2px 4px rgba(37, 99, 235, 0.1);">$0.00</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Botón de envío mejorado -->
                <div class="mt-3" style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="button" onclick="confirmarCotizacion()" class="btn btn-primary" 
                            style="background: #2563eb; color: white; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3); transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(37, 99, 235, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(37, 99, 235, 0.3)'">
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
    
    document.getElementById('porcentajeIva').addEventListener('change', calcularTotales);
    
    function calcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('.precio-cotizado').forEach(input => {
            const precio = parseFloat(input.value) || 0;
            const cantidad = parseInt(input.dataset.cantidad) || 0;
            const subtotalProducto = precio * cantidad;
            
            const row = input.closest('tr');
            row.querySelector('.subtotal').textContent = '$' + subtotalProducto.toFixed(2);
            
            subtotal += subtotalProducto;
        });
        
        const porcentajeIva = parseFloat(document.getElementById('porcentajeIva').value) / 100;
        const iva = subtotal * porcentajeIva;
        const total = subtotal + iva;
        
        document.getElementById('subtotalGeneral').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('ivaGeneral').textContent = '$' + iva.toFixed(2);
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
    
    <div class="card" style="margin-bottom: 1.5rem; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--gray-900); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-clipboard-check" style="color: var(--primary);"></i>
                Revisar y Aprobar
            </h3>
            <?php if ($user['rol'] === 'gerencia_general'): ?>
                <span class="badge badge-warning">Aprobacion Final</span>
            <?php endif; ?>
        </div>
        <div style="padding: 1.5rem;">
            <form action="aprobar-requisicion.php" method="POST" id="formAprobar">
                <input type="hidden" name="requisicion_id" value="<?php echo $requisicion['id']; ?>">
                
                <div style="padding: 1rem 1.25rem; background: var(--primary-light); border-left: 3px solid var(--primary); border-radius: 10px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle" style="color: var(--primary); font-size: 1rem;"></i>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: var(--primary-hover); font-size: 0.8125rem; line-height: 1.5;">
                            <?php if ($user['rol'] === 'gerencia'): ?>
                                Puedes aprobar la requisición completa o desmarcar los artículos que no serán aprobados.
                                Al aprobar, la requisición se enviará a Gerencia Administrativa para aprobación final.
                            <?php else: ?>
                                Esta es la aprobación final. Revisa cuidadosamente antes de aprobar.
                            <?php endif; ?>
                            Si rechazas artículos o la requisición completa, debes proporcionar una justificación.
                        </p>
                    </div>
                </div>
                
                <!-- Tabla de productos con diseño mejorado -->
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; background: white; border-radius: 10px; overflow: hidden;">
                        <thead>
                            <tr style="background: var(--gray-50);">
                                <th width="50" style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Aprobar</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Cantidad</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Unidad</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Proveedor</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Precio</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Subtotal</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Justificacion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotalSinIva = 0;
                            $porcentajeIva = isset($requisicion['porcentaje_iva']) ? $requisicion['porcentaje_iva'] : 16;
                            foreach ($detalles as $detalle): 
                                $precioCotizado = $detalle['precio_cotizado'] ?? 0;
                                $subtotal = $precioCotizado * $detalle['cantidad'];
                                if ($detalle['aprobado']) {
                                    $subtotalSinIva += $subtotal;
                                }
                            ?>
                            <tr class="<?php echo !$detalle['aprobado'] ? 'rechazado' : ''; ?>" style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                <td class="text-center" style="padding: 16px;">
                                    <input type="checkbox" 
                                           class="articulo-check" 
                                           name="articulos_aprobados[]" 
                                           value="<?php echo $detalle['id']; ?>"
                                           <?php echo $detalle['aprobado'] ? 'checked' : ''; ?>
                                           style="width: 18px; height: 18px; cursor: pointer; accent-color: #2563eb;">
                                </td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500;"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                <td style="color: #1f2937; padding: 16px; text-align: center;">
                                    <?php if (in_array($user['rol'], ['gerencia', 'gerencia_general']) && in_array($requisicion['estado'], ['en_gerencia', 'en_gerencia_general'])): ?>
                                        <input type="number" 
                                               step="1" 
                                               class="form-control form-control-sm cantidad-input" 
                                               name="cantidades[<?php echo $detalle['id']; ?>]" 
                                               value="<?php echo intval($detalle['cantidad']); ?>"
                                               min="1"
                                               style="background: white; border: 2px solid #e5e7eb; color: #1f2937; padding: 8px 12px; border-radius: 8px; width: 100px; transition: border-color 0.2s;" 
                                               onfocus="this.style.borderColor='#2563eb'" 
                                               onblur="this.style.borderColor='#e2e8f0'">
                                    <?php else: ?>
                                        <?php echo intval($detalle['cantidad']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #6b7280; padding: 16px; text-align: center;"><?php echo htmlspecialchars($detalle['unidad']); ?></td>
                                <td style="color: #6b7280; padding: 16px;"><?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? ''); ?></td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500; text-align: right;">$<?php echo number_format($precioCotizado, 2); ?></td>
                                <td style="color: #2563eb; padding: 16px; font-weight: 600; text-align: right;">$<?php echo number_format($subtotal, 2); ?></td>
                                <td style="padding: 16px;">
                                    <input type="text" 
                                           class="form-control form-control-sm justificacion-articulo" 
                                           name="justificaciones[<?php echo $detalle['id']; ?>]"
                                           value="<?php echo htmlspecialchars($detalle['justificacion_rechazo'] ?? ''); ?>"
                                           placeholder="Requerido si desmarca el artículo"
                                           style="background: white; border: 2px solid #e2e8f0; color: #1f2937; padding: 8px 12px; border-radius: 8px; transition: border-color 0.2s;"
                                           onfocus="this.style.borderColor='#2563eb'" 
                                           onblur="this.style.borderColor='#e2e8f0'">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php 
                            $ivaCalculado = $subtotalSinIva * ($porcentajeIva / 100);
                            $totalConIva = $subtotalSinIva + $ivaCalculado;
                            ?>
                            
                            <!-- Fila de Subtotal -->
                            <tr style="background: #f9fafb; border-top: 1px solid #e5e7eb;">
                                <td colspan="6" class="text-right" style="color: #6b7280; font-weight: 600; padding: 16px; font-size: 14px;">Subtotal (Sin IVA):</td>
                                <td colspan="2" style="padding: 16px; text-align: right;"><strong style="color: #1f2937; font-size: 18px;">$<?php echo number_format($subtotalSinIva, 2); ?></strong></td>
                            </tr>
                            
                            <!-- Fila de IVA -->
                            <tr style="background: #f9fafb;">
                                <td colspan="6" class="text-right" style="color: #6b7280; font-weight: 600; padding: 16px; font-size: 14px;">
                                    <!-- Mostrar el porcentaje correcto de IVA -->
                                    IVA (<?php echo number_format($porcentajeIva, 0); ?>%):
                                </td>
                                <td colspan="2" style="padding: 16px; text-align: right;"><strong style="color: #1f2937; font-size: 18px;">$<?php echo number_format($ivaCalculado, 2); ?></strong></td>
                            </tr>
                            
                            <!-- Fila de Total -->
                            <tr class="total-row" style="background: var(--gray-50); border-top: 2px solid #2563eb;">
                                <td colspan="6" class="text-right" style="color: #1f2937; font-weight: 700; padding: 20px; font-size: 16px;"><strong>Total con IVA:</strong></td>
                                <td colspan="2" style="padding: 20px; text-align: right;"><strong style="color: #2563eb; font-size: 24px; font-weight: 700;">$<?php echo number_format($totalConIva, 2); ?></strong></td>
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
                              onfocus="this.style.borderColor='#2563eb'" 
                              onblur="this.style.borderColor='#e2e8f0'"><?php echo htmlspecialchars($requisicion['justificacion_rechazo'] ?? ''); ?></textarea>
                </div>
                
                <div class="mt-3" style="margin-top: 24px; display: flex; gap: 12px;">
                    <!-- Actualizando botones con diseño azul marino -->
                    <button type="submit" name="accion" value="aprobar" class="btn btn-success" style="background: #2563eb; color: white; border: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(37, 99, 235, 0.3)'">
                        <i class="fas fa-check"></i> 
                        <?php echo $user['rol'] === 'gerencia' ? 'Enviar a Gerencia Administrativa' : 'Aprobar Requisición Final'; ?>
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
        
        const accion = <?php echo $user['rol'] === 'gerencia' ? '"enviar a Gerencia Administrativa"' : '"aprobar la requisición"'; ?>;
        const titulo = <?php echo $user['rol'] === 'gerencia' ? '"¿Enviar a Gerencia Administrativa?"' : '"¿Aprobar Requisición?"'; ?>;
        
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

    <!-- Agregando tabla de productos para requisiciones aprobadas y completadas -->
    <?php if (!($user['rol'] === 'compras' && $requisicion['estado'] === 'pendiente')): ?>
    <div class="card" style="margin-bottom: 1.5rem; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100);">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--gray-900); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-box" style="color: var(--primary);"></i>
                Productos Solicitados
            </h3>
        </div>
        
        <div style="padding: 0;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--gray-50);">
                            <th style="padding: 0.75rem 1rem; text-align: left; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Cantidad</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Unidad</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Proveedor</th>
                            <th style="padding: 0.75rem 1rem; text-align: right; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Precio</th>
                            <th style="padding: 0.75rem 1rem; text-align: right; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Subtotal</th>
                            <?php if (in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada'])): ?>
                            <th style="padding: 16px 20px; text-align: center; color: var(--gray-500); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Estado</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotalGeneral = 0;
                        $porcentajeIva = isset($requisicion['porcentaje_iva']) && $requisicion['porcentaje_iva'] > 0 
                            ? floatval($requisicion['porcentaje_iva']) / 100 
                            : 0.16;
                        
                        foreach ($detalles as $detalle): 
                            // Usar precio_cotizado si existe, si no usar precio_unitario del inventario
                            $precioUnitario = floatval($detalle['precio_cotizado'] ?? $detalle['precio_unitario'] ?? 0);
                            $cantidad = floatval($detalle['cantidad']);
                            $subtotal = $precioUnitario * $cantidad;
                            
                            // Solo sumar al total si está aprobado o si no hay campo 'aprobado' (para estados anteriores a la aprobación)
                            if (!isset($detalle['aprobado']) || $detalle['aprobado']) {
                                $subtotalGeneral += $subtotal;
                            }
                        ?>
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease;" 
                            onmouseover="this.style.backgroundColor='#f8fafc'" 
                            onmouseout="this.style.backgroundColor='white'">
                            <td style="padding: 18px 20px; color: #1f2937; font-weight: 500; font-size: 15px;">
                                <?php echo htmlspecialchars($detalle['producto_nombre']); ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: center; color: #1f2937; font-weight: 600; font-size: 15px;">
                                <?php echo $detalle['cantidad']; ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: center; color: #64748b; font-size: 14px;">
                                <?php echo htmlspecialchars($detalle['unidad']); ?>
                            </td>
                            <td style="padding: 18px 20px; color: #1f2937; font-size: 15px;">
                                <?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? 'No especificado'); ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: right; color: #1f2937; font-weight: 600; font-size: 15px;">
                                $<?php echo number_format($precioUnitario, 2); ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: right; color: #2563eb; font-weight: 700; font-size: 16px;">
                                $<?php echo number_format($subtotal, 2); ?>
                            </td>
                            <?php if (in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada'])): ?>
                            <td style="padding: 18px 20px; text-align: center;">
                                <?php if ($detalle['aprobado']): ?>
                                    <span style="background: var(--success-light); color: var(--success); padding: 4px 12px; border-radius: 999px; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                                        <i class="fas fa-check-circle" style="margin-right: 4px;"></i>Aprobado
                                    </span>
                                <?php else: ?>
                                    <span style="background: var(--danger-light); color: var(--danger); padding: 4px 12px; border-radius: 999px; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                                        <i class="fas fa-times-circle" style="margin-right: 4px;"></i>Rechazado
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Subtotal -->
                        <tr style="background: var(--gray-50);">
                            <td colspan="<?php echo in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada']) ? '6' : '5'; ?>" 
                                style="padding: 18px 20px; text-align: right; color: #1f2937; font-weight: 600; font-size: 15px;">
                                Subtotal (Sin IVA):
                            </td>
                            <td style="padding: 18px 20px; text-align: right; color: #1f2937; font-weight: 700; font-size: 18px;">
                                $<?php echo number_format($subtotalGeneral, 2); ?>
                            </td>
                        </tr>
                        
                        <!-- IVA -->
                        <?php 
                        $ivaTotal = $subtotalGeneral * $porcentajeIva;
                        $porcentajeIvaDisplay = intval($porcentajeIva * 100);
                        ?>
                        <tr style="background: var(--gray-50);">
                            <td colspan="<?php echo in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada']) ? '6' : '5'; ?>" 
                                style="padding: 18px 20px; text-align: right; color: #1f2937; font-weight: 600; font-size: 15px;">
                                IVA (<?php echo $porcentajeIvaDisplay; ?>%):
                            </td>
                            <td style="padding: 18px 20px; text-align: right; color: #64748b; font-weight: 700; font-size: 18px;">
                                $<?php echo number_format($ivaTotal, 2); ?>
                            </td>
                        </tr>
                        
                        <!-- Total con IVA -->
                        <?php $totalConIva = $subtotalGeneral + $ivaTotal; ?>
                        <tr style="background: #eff6ff; border-top: 3px solid #2563eb;">
                            <td colspan="<?php echo in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada']) ? '6' : '5'; ?>" 
                                style="padding: 20px; text-align: right; color: #1e40af; font-weight: 700; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total con IVA:
                            </td>
                            <td style="padding: 20px; text-align: right; color: #2563eb; font-weight: 900; font-size: 24px;">
                                $<?php echo number_format($totalConIva, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Punto 24: Agregar botón de imprimir para compras -->
    <?php if ($user['rol'] === 'compras' && in_array($requisicion['estado'], ['aprobada', 'completada'])): ?>
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <a href="imprimir-requisiciones.php?id=<?php echo $requisicion['id']; ?>" 
               target="_blank"
               style="background: #2563eb; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(37, 99, 235, 0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.3)';">
                <i class="fas fa-print"></i> Imprimir Orden de Compra
            </a>
        </div>
    <?php endif; ?>

</main>

<?php require 'includes/footer.php'; ?>
