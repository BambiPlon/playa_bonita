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
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 50px;"></th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 80px;">Cant.</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 80px;">Unidad</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 110px;"><i class="fas fa-warehouse"></i> Almacen</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Proveedor</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 110px;">Precio</th>
                                <th style="color: var(--gray-500); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); width: 100px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $proveedorModel = new Proveedor();
                            $proveedores_list = $proveedorModel->obtenerTodos(true);
                            
                            foreach ($detalles as $detalle): ?>
                            <!-- Filas con hover y mejor espaciado -->
                            <?php 
                            $productosStock = isset($stockDisponible[$detalle['id']]) ? $stockDisponible[$detalle['id']] : [];
                            $tieneStock = !empty($productosStock);
                            ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.2s ease; background: white;" 
                                onmouseover="this.style.background='#f8fafc'" 
                                onmouseout="this.style.background='white'"
                                data-detalle-id="<?php echo $detalle['id']; ?>"
                                id="fila-detalle-<?php echo $detalle['id']; ?>">
                                <!-- Botón quitar producto -->
                                <td style="padding: 10px; text-align: center;">
                                    <button type="button" 
                                            onclick="quitarProductoCotizacion(<?php echo $detalle['id']; ?>, '<?php echo htmlspecialchars(addslashes($detalle['producto_nombre'])); ?>')"
                                            style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                            onmouseover="this.style.background='#dc2626'; this.style.color='white'"
                                            onmouseout="this.style.background='#fee2e2'; this.style.color='#dc2626'"
                                            title="Quitar producto">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <input type="hidden" name="productos_incluidos[]" value="<?php echo $detalle['id']; ?>" class="producto-incluido">
                                </td>
                                <td style="color: #1f2937; padding: 12px; font-weight: 500; font-size: 14px;"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                <td style="padding: 12px; text-align: center;">
                                    <input type="number" 
                                           name="cantidades[<?php echo $detalle['id']; ?>]" 
                                           value="<?php echo $detalle['cantidad']; ?>" 
                                           min="1" 
                                           class="cantidad-input"
                                           data-detalle-id="<?php echo $detalle['id']; ?>"
                                           onchange="actualizarSubtotalFila(<?php echo $detalle['id']; ?>)"
                                           style="width: 70px; text-align: center; font-weight: 600; padding: 8px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: #f8fafc;"
                                           onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                </td>
                                <td style="color: #64748b; padding: 12px; text-align: center; font-size: 13px;"><?php echo ucfirst(htmlspecialchars($detalle['unidad'] ?? 'pieza')); ?></td>
                                <!-- Columna de Stock en Almacen - Botón simplificado -->
                                <td style="padding: 10px; text-align: center;">
                                    <?php if ($tieneStock): 
                                        $stockTotal = array_sum(array_column($productosStock, 'cantidad'));
                                        $primerProducto = $productosStock[0];
                                    ?>
                                        <input type="hidden" name="surtir_almacen[<?php echo $detalle['id']; ?>]" value="" class="surtir-almacen-input" data-detalle-id="<?php echo $detalle['id']; ?>">
                                        <input type="hidden" class="stock-data" data-detalle-id="<?php echo $detalle['id']; ?>" 
                                               data-productos='<?php echo htmlspecialchars(json_encode($productosStock)); ?>'
                                               data-cantidad-requerida="<?php echo $detalle['cantidad']; ?>">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            <button type="button" 
                                                    onclick="toggleSurtirAlmacenBtn(<?php echo $detalle['id']; ?>)"
                                                    class="btn-surtir-almacen"
                                                    data-activo="false"
                                                    data-detalle-id="<?php echo $detalle['id']; ?>"
                                                    style="background: #ecfdf5; color: #059669; border: 2px solid #10b981; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 12px; transition: all 0.2s; display: flex; align-items: center; gap: 6px;"
                                                    title="<?php echo $stockTotal; ?> en stock">
                                                <i class="fas fa-warehouse"></i>
                                                <span>Surtir</span>
                                            </button>
                                            <span style="font-size: 10px; color: #059669; font-weight: 600; background: #d1fae5; padding: 2px 8px; border-radius: 10px;">
                                                <i class="fas fa-box"></i> <?php echo $stockTotal; ?> disponible
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 11px;"><i class="fas fa-times-circle"></i> Sin stock</span>
                                        <input type="hidden" name="surtir_almacen[<?php echo $detalle['id']; ?>]" value="">
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px;" class="proveedor-cell" data-detalle="<?php echo $detalle['id']; ?>;">
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
                                <td colspan="7" style="color: #1f2937; font-weight: 600; padding: 20px; text-align: right; font-size: 14px;">Subtotal (sin IVA):</td>
                                <td style="padding: 20px; text-align: right;">
                                    <strong id="subtotalGeneral" style="color: #2563eb; font-size: 18px; font-weight: 700;">$0.00</strong>
                                </td>
                            </tr>
                            <!-- Agregar selector de porcentaje de IVA -->
                            <tr style="background: var(--gray-50);">
                                <td colspan="7" style="color: #1f2937; font-weight: 600; padding: 16px 20px; text-align: right; font-size: 14px;">
                                    IVA:
                                    <select id="porcentajeIva" name="porcentaje_iva" style="margin-left: 10px; padding: 5px 10px; border: 2px solid #2563eb; border-radius: 6px; background: white; color: #1f2937; font-weight: 600; cursor: pointer; outline: none;">
                                        <option value="16">16%</option>
                                        <option value="8">8%</option>
                                    </select>
                                </td>
                                <td style="padding: 16px 20px; text-align: right;">
                                    <strong id="ivaGeneral" style="color: #6b7280; font-size: 18px; font-weight: 600;">$0.00</strong>
                                </td>
                            </tr>
                            <tr style="background: var(--gray-50); border-top: 2px solid #2563eb;">
                                <td colspan="7" style="color: #1f2937; font-weight: 600; padding: 20px; text-align: right; font-size: 16px;">Total con IVA:</td>
                                <td style="padding: 20px; text-align: right;">
                                    <strong id="totalGeneral" style="color: #2563eb; font-size: 24px; font-weight: 700;">$0.00</strong>
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
    // Funcion para quitar producto de la cotizacion
    function quitarProductoCotizacion(detalleId, nombreProducto) {
        Swal.fire({
            title: 'Quitar producto',
            html: '¿Deseas quitar <strong>' + nombreProducto + '</strong> de la cotizacion?<br><small class="text-muted">Este producto no sera incluido en la cotizacion.</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Si, quitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const fila = document.getElementById('fila-detalle-' + detalleId);
                if (fila) {
                    // Remover el input hidden para que no se envie
                    const inputIncluido = fila.querySelector('.producto-incluido');
                    if (inputIncluido) inputIncluido.remove();
                    
                    // Marcar visualmente como quitado
                    fila.style.opacity = '0.4';
                    fila.style.background = '#fee2e2';
                    fila.style.pointerEvents = 'none';
                    
                    // Deshabilitar todos los inputs de esta fila
                    fila.querySelectorAll('input, select, button').forEach(el => {
                        el.disabled = true;
                    });
                    
                    // Agregar badge de quitado
                    const tdProducto = fila.querySelector('td:nth-child(2)');
                    if (tdProducto) {
                        tdProducto.innerHTML += ' <span style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;">QUITADO</span>';
                    }
                    
                    recalcularTotales();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto quitado',
                        text: nombreProducto + ' no sera incluido en la cotizacion',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    }
    
    // Funcion para actualizar subtotal cuando cambia la cantidad
    function actualizarSubtotalFila(detalleId) {
        const row = document.querySelector('tr[data-detalle-id="' + detalleId + '"]');
        if (!row) return;
        
        const cantidadInput = row.querySelector('.cantidad-input');
        const precioInput = row.querySelector('.precio-cotizado');
        const subtotalCell = row.querySelector('.subtotal');
        
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        const subtotal = cantidad * precio;
        
        subtotalCell.textContent = '$' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        
        // Actualizar data-cantidad-requerida para el botón surtir
        const stockData = row.querySelector('.stock-data');
        if (stockData) {
            stockData.dataset.cantidadRequerida = cantidad;
        }
        
        recalcularTotales();
    }
    
    // Funcion para manejar el toggle de surtir desde almacen con boton
    function toggleSurtirAlmacenBtn(detalleId) {
        const btn = document.querySelector('.btn-surtir-almacen[data-detalle-id="' + detalleId + '"]');
        const inputSurtir = document.querySelector('.surtir-almacen-input[data-detalle-id="' + detalleId + '"]');
        const stockData = document.querySelector('.stock-data[data-detalle-id="' + detalleId + '"]');
        const row = btn.closest('tr');
        const proveedorCell = row.querySelector('.proveedor-cell');
        const precioInput = row.querySelector('.precio-cotizado');
        const subtotalCell = row.querySelector('.subtotal');
        
        const activo = btn.dataset.activo === 'true';
        const productos = JSON.parse(stockData.dataset.productos);
        const cantidadRequerida = parseInt(stockData.dataset.cantidadRequerida) || 0;
        const primerProducto = productos[0];
        const stockDisponible = parseInt(primerProducto.cantidad) || 0;
        
        if (!activo) {
            // Activar surtir desde almacen
            btn.dataset.activo = 'true';
            btn.style.background = '#10b981';
            btn.style.color = 'white';
            btn.style.borderColor = '#059669';
            btn.innerHTML = '<i class="fas fa-check"></i> <span>Almacen</span>';
            
            inputSurtir.value = primerProducto.id;
            
            // Deshabilitar proveedor y precio
            const proveedorSelect = proveedorCell.querySelector('select');
            const nuevoBtn = proveedorCell.querySelector('button');
            if (proveedorSelect) {
                proveedorSelect.disabled = true;
                proveedorSelect.style.opacity = '0.4';
                proveedorSelect.removeAttribute('required');
            }
            if (nuevoBtn) nuevoBtn.style.display = 'none';
            
            precioInput.disabled = true;
            precioInput.style.opacity = '0.4';
            precioInput.value = 0;
            precioInput.removeAttribute('required');
            
            subtotalCell.innerHTML = '<span style="color: #10b981; font-weight: 600;"><i class="fas fa-warehouse"></i> $0.00</span>';
            
            // Verificar stock
            if (stockDisponible < cantidadRequerida) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    html: 'Solo hay <strong>' + stockDisponible + '</strong> unidades disponibles.<br>Se requieren <strong>' + cantidadRequerida + '</strong> unidades.',
                    confirmButtonColor: '#2563eb'
                });
            }
            
            row.style.background = '#ecfdf5';
        } else {
            // Desactivar - Cotizar normalmente
            btn.dataset.activo = 'false';
            btn.style.background = '#ecfdf5';
            btn.style.color = '#059669';
            btn.style.borderColor = '#10b981';
            btn.innerHTML = '<i class="fas fa-warehouse"></i> <span>Surtir</span>';
            
            inputSurtir.value = '';
            
            const proveedorSelect = proveedorCell.querySelector('select');
            const nuevoBtn = proveedorCell.querySelector('button');
            if (proveedorSelect) {
                proveedorSelect.disabled = false;
                proveedorSelect.style.opacity = '1';
                proveedorSelect.setAttribute('required', 'required');
            }
            if (nuevoBtn) nuevoBtn.style.display = 'inline-block';
            
            precioInput.disabled = false;
            precioInput.style.opacity = '1';
            precioInput.setAttribute('required', 'required');
            
            subtotalCell.textContent = '$0.00';
            row.style.background = 'white';
        }
        
        recalcularTotales();
    }
    
    // Funcion para recalcular totales
    function recalcularTotales() {
        calcularTotales();
    }
    
    // Calcular totales automaticamente
    document.querySelectorAll('.precio-cotizado').forEach(input => {
        input.addEventListener('input', calcularTotales);
    });
    
    document.getElementById('porcentajeIva')?.addEventListener('change', calcularTotales);
    
    function calcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('.precio-cotizado').forEach(input => {
            // Saltar los que se surten desde almacen o estan quitados
            const row = input.closest('tr');
            const estaQuitado = !row.querySelector('.producto-incluido');
            if (input.disabled || estaQuitado) return;
            
            const precio = parseFloat(input.value) || 0;
            const cantidad = parseInt(input.dataset.cantidad) || 0;
            const subtotalProducto = precio * cantidad;
            
            const subtotalCell = row.querySelector('.subtotal');
            if (subtotalCell && !subtotalCell.innerHTML.includes('Almacen')) {
                subtotalCell.textContent = '$' + subtotalProducto.toFixed(2);
            }
            
            subtotal += subtotalProducto;
        });
        
        const porcentajeIvaEl = document.getElementById('porcentajeIva');
        const porcentajeIva = porcentajeIvaEl ? parseFloat(porcentajeIvaEl.value) / 100 : 0.16;
        const iva = subtotal * porcentajeIva;
        const total = subtotal + iva;
        
        const subtotalEl = document.getElementById('subtotalGeneral');
        const ivaEl = document.getElementById('ivaGeneral');
        const totalEl = document.getElementById('totalGeneral');
        
        if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);
        if (ivaEl) ivaEl.textContent = '$' + iva.toFixed(2);
        if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
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
        // Validar que todos los campos requeridos estén llenos (excepto los que se surten desde almacén)
        const proveedores = document.querySelectorAll('.proveedor-select');
        const precios = document.querySelectorAll('.precio-cotizado');
        let valid = true;
        let hayProductosACotizar = false;
        let productosDesdeAlmacen = 0;
        
        proveedores.forEach(select => {
            // Solo validar si no está deshabilitado (no se surte desde almacén)
            if (!select.disabled && !select.value) {
                valid = false;
            }
        });
        
        precios.forEach(input => {
            // Solo validar si no está deshabilitado (no se surte desde almacén)
            if (!input.disabled) {
                if (!input.value || parseFloat(input.value) <= 0) {
                    valid = false;
                }
                hayProductosACotizar = true;
            } else {
                productosDesdeAlmacen++;
            }
        });
        
        if (!valid) {
            alertaError('Por favor, completa todos los campos de proveedor y precio cotizado para los productos que no se surten desde almacén');
            return;
        }
        
        const total = document.getElementById('totalGeneral').textContent;
        
        // Construir mensaje con información de productos desde almacén
        let mensajeHtml = `<p>Se enviará la cotización con un total de <strong>${total}</strong></p>`;
        
        if (productosDesdeAlmacen > 0) {
            mensajeHtml += `<div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; padding: 12px; margin: 15px 0; text-align: left;">
                <p style="margin: 0; color: #065f46; font-weight: 600;">
                    <i class="fas fa-warehouse"></i> ${productosDesdeAlmacen} producto(s) se surtirán desde almacén
                </p>
                <p style="margin: 5px 0 0 0; color: #047857; font-size: 13px;">
                    Se registrará automáticamente la salida del inventario.
                </p>
            </div>`;
        }
        
        if (hayProductosACotizar) {
            mensajeHtml += `<p class="text-muted">Los demás productos serán enviados a Gerencia para su aprobación.</p>`;
        } else {
            mensajeHtml += `<p class="text-muted">Todos los productos se surtirán desde almacén. No hay monto a aprobar.</p>`;
        }
        
        Swal.fire({
            title: '¿Enviar cotización a Gerencia?',
            html: mensajeHtml,
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
                                // Detectar si es surtido desde almacén por campo O por precio 0 sin proveedor
                                $precioCotizadoOriginal = floatval($detalle['precio_cotizado'] ?? 0);
                                $tieneProveedorG = !empty($detalle['proveedor_id']) || !empty($detalle['proveedor_nombre']);
                                $surtidoAlmacen = (isset($detalle['surtido_almacen']) && $detalle['surtido_almacen'] == 1)
                                    || ($precioCotizadoOriginal == 0 && !$tieneProveedorG && isset($detalle['aprobado']) && $detalle['aprobado'] == 1);
                                // Si es surtido desde almacén, precio es 0
                                $precioCotizado = $surtidoAlmacen ? 0 : $precioCotizadoOriginal;
                                $subtotal = $precioCotizado * $detalle['cantidad'];
                                // Solo sumar si está aprobado Y no es surtido desde almacén
                                if ($detalle['aprobado'] && !$surtidoAlmacen) {
                                    $subtotalSinIva += $subtotal;
                                }
                            ?>
                            <tr class="<?php echo !$detalle['aprobado'] ? 'rechazado' : ''; ?>" style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s; <?php echo $surtidoAlmacen ? 'background: #ecfdf5;' : ''; ?>" onmouseover="this.style.backgroundColor='<?php echo $surtidoAlmacen ? '#d1fae5' : '#f9fafb'; ?>'" onmouseout="this.style.backgroundColor='<?php echo $surtidoAlmacen ? '#ecfdf5' : 'white'; ?>'">
                                <td class="text-center" style="padding: 16px;">
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="color: #10b981; font-size: 18px;" title="Surtido desde almacén">
                                            <i class="fas fa-warehouse"></i>
                                        </span>
                                        <input type="hidden" name="articulos_aprobados[]" value="<?php echo $detalle['id']; ?>">
                                    <?php else: ?>
                                        <input type="checkbox" 
                                               class="articulo-check" 
                                               name="articulos_aprobados[]" 
                                               value="<?php echo $detalle['id']; ?>"
                                               <?php echo $detalle['aprobado'] ? 'checked' : ''; ?>
                                               style="width: 18px; height: 18px; cursor: pointer; accent-color: #2563eb;">
                                    <?php endif; ?>
                                </td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500;">
                                    <?php echo htmlspecialchars($detalle['producto_nombre']); ?>
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="display: inline-block; margin-left: 8px; background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;">
                                            DESDE ALMACÉN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #1f2937; padding: 16px; text-align: center;">
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="color: #10b981; font-weight: 600;"><?php echo intval($detalle['cantidad']); ?></span>
                                        <input type="hidden" name="cantidades[<?php echo $detalle['id']; ?>]" value="<?php echo intval($detalle['cantidad']); ?>">
                                    <?php elseif (in_array($user['rol'], ['gerencia', 'gerencia_general']) && in_array($requisicion['estado'], ['en_gerencia', 'en_gerencia_general'])): ?>
                                        <input type="number" 
                                               step="1" 
                                               class="form-control form-control-sm cantidad-input" 
                                               name="cantidades[<?php echo $detalle['id']; ?>]" 
                                               value="<?php echo intval($detalle['cantidad']); ?>"
                                               min="1"
                                               data-precio="<?php echo $precioCotizado; ?>"
                                               data-detalle-id="<?php echo $detalle['id']; ?>"
                                               onchange="actualizarSubtotalArticulo(this)"
                                               oninput="actualizarSubtotalArticulo(this)"
                                               style="background: white; border: 2px solid #e5e7eb; color: #1f2937; padding: 8px 12px; border-radius: 8px; width: 100px; transition: border-color 0.2s;" 
                                               onfocus="this.style.borderColor='#2563eb'" 
                                               onblur="this.style.borderColor='#e2e8f0'">
                                    <?php else: ?>
                                        <?php echo intval($detalle['cantidad']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #6b7280; padding: 16px; text-align: center;"><?php echo htmlspecialchars($detalle['unidad']); ?></td>
                                <td style="color: #6b7280; padding: 16px;">
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-warehouse" style="font-size: 11px;"></i> Almacen
                                        </span>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? 'Sin proveedor'); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #1f2937; padding: 16px; font-weight: 500; text-align: right;">
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="color: #10b981; font-weight: 600;">$0.00</span>
                                    <?php else: ?>
                                        <span class="precio-unitario" data-precio="<?php echo $precioCotizado; ?>">$<?php echo number_format($precioCotizado, 2); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px; font-weight: 600; text-align: right;">
                                    <?php if ($surtidoAlmacen): ?>
                                        <span style="color: #10b981;">$0.00</span>
                                    <?php else: ?>
                                        <span class="subtotal-articulo" data-detalle-id="<?php echo $detalle['id']; ?>" style="color: #2563eb;">$<?php echo number_format($subtotal, 2); ?></span>
                                    <?php endif; ?>
                                </td>
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
                                <td colspan="2" style="padding: 16px; text-align: right;"><strong id="subtotalGerencia" style="color: #1f2937; font-size: 18px;">$<?php echo number_format($subtotalSinIva, 2); ?></strong></td>
                            </tr>
                            
                            <!-- Fila de IVA -->
                            <tr style="background: #f9fafb;">
                                <td colspan="6" class="text-right" style="color: #6b7280; font-weight: 600; padding: 16px; font-size: 14px;">
                                    IVA (<span id="porcentajeIvaGerencia"><?php echo number_format($porcentajeIva, 0); ?></span>%):
                                </td>
                                <td colspan="2" style="padding: 16px; text-align: right;"><strong id="ivaGerencia" style="color: #1f2937; font-size: 18px;">$<?php echo number_format($ivaCalculado, 2); ?></strong></td>
                            </tr>
                            
                            <!-- Fila de Total -->
                            <tr class="total-row" style="background: var(--gray-50); border-top: 2px solid #2563eb;">
                                <td colspan="6" class="text-right" style="color: #1f2937; font-weight: 700; padding: 20px; font-size: 16px;"><strong>Total con IVA:</strong></td>
                                <td colspan="2" style="padding: 20px; text-align: right;"><strong id="totalGerencia" style="color: #2563eb; font-size: 24px; font-weight: 700;">$<?php echo number_format($totalConIva, 2); ?></strong></td>
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
    // Función para actualizar el subtotal cuando cambia la cantidad
    function actualizarSubtotalArticulo(input) {
        const precio = parseFloat(input.dataset.precio) || 0;
        const cantidad = parseInt(input.value) || 0;
        const detalleId = input.dataset.detalleId;
        const subtotal = precio * cantidad;
        
        // Actualizar el subtotal del artículo
        const subtotalSpan = document.querySelector(`.subtotal-articulo[data-detalle-id="${detalleId}"]`);
        if (subtotalSpan) {
            subtotalSpan.textContent = '$' + subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        // Recalcular totales generales
        recalcularTotalesGerencia();
    }
    
    // Función para recalcular totales de gerencia
    function recalcularTotalesGerencia() {
        let subtotalGeneral = 0;
        const porcentajeIva = parseFloat(document.getElementById('porcentajeIvaGerencia')?.textContent) || 16;
        
        // Sumar todos los subtotales de artículos aprobados (checkbox marcado)
        document.querySelectorAll('.articulo-check').forEach(checkbox => {
            if (checkbox.checked) {
                const row = checkbox.closest('tr');
                const cantidadInput = row.querySelector('.cantidad-input');
                if (cantidadInput) {
                    const precio = parseFloat(cantidadInput.dataset.precio) || 0;
                    const cantidad = parseInt(cantidadInput.value) || 0;
                    subtotalGeneral += precio * cantidad;
                }
            }
        });
        
        const iva = subtotalGeneral * (porcentajeIva / 100);
        const total = subtotalGeneral + iva;
        
        // Actualizar los elementos del DOM
        const subtotalEl = document.getElementById('subtotalGerencia');
        const ivaEl = document.getElementById('ivaGerencia');
        const totalEl = document.getElementById('totalGerencia');
        
        if (subtotalEl) subtotalEl.textContent = '$' + subtotalGeneral.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (ivaEl) ivaEl.textContent = '$' + iva.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (totalEl) totalEl.textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    // Recalcular totales cuando se marca/desmarca un checkbox
    document.querySelectorAll('.articulo-check').forEach(checkbox => {
        checkbox.addEventListener('change', recalcularTotalesGerencia);
    });
    
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
            
            // Verificar que al menos un artículo esté aprobado O haya productos de almacén
            const checkboxes = document.querySelectorAll('.articulo-check');
            const algnoAprobado = Array.from(checkboxes).some(cb => cb.checked);
            const hayProductosAlmacen = document.querySelectorAll('.badge-almacen, [data-almacen="true"]').length > 0 || 
                                        document.querySelectorAll('tr .fa-warehouse').length > 0;
            const todosDeAlmacen = checkboxes.length === 0;
            
            // Permitir aprobar si: hay al menos uno aprobado, o todos son de almacén, o hay productos de almacén
            if (!algnoAprobado && !todosDeAlmacen && !hayProductosAlmacen) {
                e.preventDefault();
                alert('Debe aprobar al menos un artículo o rechazar la requisición completa');
                return false;
            }
        }
    });
    
    function confirmarAprobacion() {
        const articulosAprobados = document.querySelectorAll('input[name="articulos_aprobados[]"]:checked');
        const checkboxes = document.querySelectorAll('.articulo-check');
        const todosDeAlmacen = checkboxes.length === 0;
        const hayProductosAlmacen = document.querySelectorAll('.badge-almacen, [data-almacen="true"]').length > 0 ||
                                    document.querySelectorAll('tr .fa-warehouse').length > 0;
        
        // Permitir si hay al menos uno aprobado, o todos son de almacén, o hay productos de almacén
        if (articulosAprobados.length === 0 && !todosDeAlmacen && !hayProductosAlmacen) {
            alertaError('Debes aprobar al menos un artículo');
            return;
        }
        
        const accion = <?php echo $user['rol'] === 'gerencia' ? '"enviar a Gerencia Administrativa"' : '"aprobar la requisición"'; ?>;
        const titulo = <?php echo $user['rol'] === 'gerencia' ? '"¿Enviar a Gerencia Administrativa?"' : '"¿Aprobar Requisición?"'; ?>;
        
        // Contar productos de almacén (iconos de almacén sin checkbox)
        const productosAlmacen = document.querySelectorAll('tr .fa-warehouse').length / 2; // Dividir porque hay 2 iconos por fila de almacén
        const totalProductos = articulosAprobados.length + (todosDeAlmacen ? document.querySelectorAll('tbody tr').length : 0);
        
        let mensaje = '';
        if (todosDeAlmacen) {
            mensaje = `<p>Todos los productos serán surtidos desde <strong>Almacén</strong>.</p>
                       <p class="text-muted">Esta acción notificará al siguiente nivel.</p>`;
        } else {
            mensaje = `<p>Has seleccionado <strong>${articulosAprobados.length}</strong> artículos para aprobar.</p>
                       <p class="text-muted">Esta acción notificará al siguiente nivel.</p>`;
        }
        
        Swal.fire({
            title: titulo,
            html: mensaje,
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
                            // Verificar si fue surtido desde almacén
                            // Detectar por campo surtido_almacen = 1 O por precio 0 sin proveedor (datos antiguos)
                            $precioCot = floatval($detalle['precio_cotizado'] ?? 0);
                            $tieneProveedor = !empty($detalle['proveedor_id']) || !empty($detalle['proveedor_nombre']);
                            $surtidoDesdeAlmacen = (isset($detalle['surtido_almacen']) && $detalle['surtido_almacen'] == 1) 
                                || ($precioCot == 0 && !$tieneProveedor && isset($detalle['aprobado']) && $detalle['aprobado'] == 1);
                            
                            // Si fue surtido desde almacén, el precio es 0
                            if ($surtidoDesdeAlmacen) {
                                $precioUnitario = 0;
                                $subtotal = 0;
                            } else {
                                $precioUnitario = floatval($detalle['precio_cotizado'] ?? $detalle['precio_unitario'] ?? 0);
                                $cantidad = floatval($detalle['cantidad']);
                                $subtotal = $precioUnitario * $cantidad;
                            }
                            
                            // Solo sumar al total si está aprobado Y no fue surtido desde almacén
                            if ((!isset($detalle['aprobado']) || $detalle['aprobado']) && !$surtidoDesdeAlmacen) {
                                $subtotalGeneral += $subtotal;
                            }
                        ?>
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease; <?php echo $surtidoDesdeAlmacen ? 'background: #ecfdf5;' : ''; ?>" 
                            onmouseover="this.style.backgroundColor='<?php echo $surtidoDesdeAlmacen ? '#d1fae5' : '#f8fafc'; ?>'" 
                            onmouseout="this.style.backgroundColor='<?php echo $surtidoDesdeAlmacen ? '#ecfdf5' : 'white'; ?>'">
                            <td style="padding: 18px 20px; color: #1f2937; font-weight: 500; font-size: 15px;">
                                <?php echo htmlspecialchars($detalle['producto_nombre']); ?>
                                <?php if ($surtidoDesdeAlmacen): ?>
                                    <span style="display: inline-block; margin-left: 8px; background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;">
                                        <i class="fas fa-warehouse"></i> DESDE ALMACEN
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: center; color: <?php echo $surtidoDesdeAlmacen ? '#10b981' : '#1f2937'; ?>; font-weight: 600; font-size: 15px;">
                                <?php echo $detalle['cantidad']; ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: center; color: #64748b; font-size: 14px;">
                                <?php echo htmlspecialchars($detalle['unidad']); ?>
                            </td>
                            <td style="padding: 18px 20px; font-size: 15px;">
                                <?php if ($surtidoDesdeAlmacen): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                        <i class="fas fa-warehouse"></i> Almacen
                                    </span>
                                <?php else: ?>
                                    <span style="color: #1f2937;"><?php echo htmlspecialchars($detalle['proveedor_nombre'] ?? 'No especificado'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: right; font-weight: 600; font-size: 15px; color: <?php echo $surtidoDesdeAlmacen ? '#10b981' : '#1f2937'; ?>;">
                                <?php echo $surtidoDesdeAlmacen ? '$0.00' : '$' . number_format($precioUnitario, 2); ?>
                            </td>
                            <td style="padding: 18px 20px; text-align: right; font-weight: 700; font-size: 16px; color: <?php echo $surtidoDesdeAlmacen ? '#10b981' : '#2563eb'; ?>;">
                                <?php echo $surtidoDesdeAlmacen ? '$0.00' : '$' . number_format($subtotal, 2); ?>
                            </td>
                            <?php if (in_array($requisicion['estado'], ['aprobada', 'completada', 'rechazada'])): ?>
                            <td style="padding: 18px 20px; text-align: center;">
                                <?php if ($surtidoDesdeAlmacen): ?>
                                    <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 999px; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                                        <i class="fas fa-warehouse" style="margin-right: 4px;"></i>Almacén
                                    </span>
                                <?php elseif ($detalle['aprobado']): ?>
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
    
  <!-- Botones de impresión para compras -->
  <?php if ($user['rol'] === 'compras' && in_array($requisicion['estado'], ['aprobada', 'completada'])): ?>
  <?php 
    // Verificar si hay productos surtidos desde almacén y productos para orden de compra
    $tieneProductosAlmacen = false;
    $tieneProductosCompra = false;
    foreach ($detalles as $det) {
        // Detectar si es de almacén por campo O por precio 0 sin proveedor
        $precioDetB = floatval($det['precio_cotizado'] ?? 0);
        $tieneProvB = !empty($det['proveedor_id']) || !empty($det['proveedor_nombre']);
        $esDeAlmacen = (isset($det['surtido_almacen']) && $det['surtido_almacen'] == 1)
            || ($precioDetB == 0 && !$tieneProvB && isset($det['aprobado']) && $det['aprobado'] == 1);
        
        if ($esDeAlmacen) {
            $tieneProductosAlmacen = true;
        } else if ($det['aprobado']) {
            $tieneProductosCompra = true;
        }
    }
  ?>
  <div style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 12px; flex-wrap: wrap;">
    <?php if ($tieneProductosAlmacen): ?>
        <?php 
        // Verificar si ya fue procesada la salida
        $salidaProcesada = isset($requisicion['salida_almacen_procesada']) && $requisicion['salida_almacen_procesada'] == 1;
        ?>
        <?php if ($salidaProcesada): ?>
            <!-- Ya fue procesada - Solo reimprimir -->
            <a href="imprimir-salida-almacen-requisicion.php?id=<?php echo $requisicion['id']; ?>"
               target="_blank"
               style="background: #6b7280; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3); transition: all 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(107, 114, 128, 0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(107, 114, 128, 0.3)';">
               <i class="fas fa-print"></i> Reimprimir Salida de Almacen
            </a>
            <span style="background: #d1fae5; color: #065f46; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
               <i class="fas fa-check-circle"></i> Salida ya procesada
               <?php if (!empty($requisicion['folio_salida_almacen'])): ?>
               (<?php echo htmlspecialchars($requisicion['folio_salida_almacen']); ?>)
               <?php endif; ?>
            </span>
        <?php else: ?>
            <!-- Primera vez - Procesar y generar salida -->
            <a href="#" onclick="confirmarSalidaAlmacen(<?php echo $requisicion['id']; ?>); return false;"
               style="background: #10b981; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.2s; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)';">
               <i class="fas fa-warehouse"></i> Procesar y Generar Salida de Almacen
            </a>
            <span style="background: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
               <i class="fas fa-exclamation-triangle"></i> Se descontara del inventario
            </span>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($tieneProductosCompra): ?>
    <a href="imprimir-requisiciones.php?id=<?php echo $requisicion['id']; ?>"
       target="_blank"
       style="background: #2563eb; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(37, 99, 235, 0.4)';"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.3)';">
       <i class="fas fa-print"></i> Imprimir Orden de Compra
    </a>
    <?php endif; ?>
    
    <?php if (!$tieneProductosCompra && $tieneProductosAlmacen): ?>
    <span style="background: #f3f4f6; color: #6b7280; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
       <i class="fas fa-info-circle"></i> Todos los productos fueron surtidos desde almacen
    </span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</main>

<!-- Modal de confirmación de salida de almacén -->
<div id="modalConfirmarSalida" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 480px; width: 90%; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #d97706;"></i>
        </div>
        <h3 style="color: #1f2937; font-size: 22px; font-weight: 700; margin-bottom: 12px;">Confirmar Salida de Almacen</h3>
        <p style="color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 8px;">
            Esta accion <strong style="color: #dc2626;">descontara los productos del inventario</strong>.
        </p>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">
            Una vez procesada, podras reimprimir el documento sin afectar el inventario nuevamente.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="ejecutarSalidaAlmacen()" style="background: #10b981; color: white; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 15px;">
                <i class="fas fa-check"></i> Si, procesar salida
            </button>
            <button onclick="cerrarModalSalida()" style="background: #f3f4f6; color: #374151; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 15px;">
                Cancelar
            </button>
        </div>
    </div>
</div>

<script>
var requisicionIdSalida = null;

function confirmarSalidaAlmacen(id) {
    requisicionIdSalida = id;
    document.getElementById('modalConfirmarSalida').style.display = 'flex';
}

function cerrarModalSalida() {
    document.getElementById('modalConfirmarSalida').style.display = 'none';
    requisicionIdSalida = null;
}

function ejecutarSalidaAlmacen() {
    if (requisicionIdSalida) {
        // Abrir el procesamiento en nueva pestaña
        window.open('procesar-salida-almacen.php?id=' + requisicionIdSalida, '_blank');
        
        // Cerrar el modal
        cerrarModalSalida();
        
        // Recargar la pagina actual despues de un breve delay para que se procese
        setTimeout(function() {
            window.location.reload();
        }, 1500);
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalConfirmarSalida').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalSalida();
    }
});
</script>

<?php require 'includes/footer.php'; ?>
