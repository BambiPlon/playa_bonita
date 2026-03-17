<?php require_once 'includes/header.php'; ?>

<main class="main-content">
    <!-- Page header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); letter-spacing: -0.3px; margin: 0 0 4px 0;">Listado de Requisiciones</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Vista detallada de todas las solicitudes</p>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i class="fas fa-filter" style="color: var(--primary); font-size: 0.75rem;"></i>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--gray-700); text-transform: uppercase; letter-spacing: 0.5px;">Filtros</span>
        </div>
        <form method="GET" action="listado-requisiciones.php" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="min-width: 150px; flex: 1;">
                <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Mes</label>
                <select name="mes" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;">
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
            
            <div style="min-width: 110px; flex: 0.6;">
                <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Ano</label>
                <select name="anio" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;">
                    <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($anio_filter == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div style="min-width: 150px; flex: 1;">
                <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Estado</label>
                <select name="estado" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;">
                    <option value="">Todos</option>
                    <option value="pendiente" <?php echo ($estado_filter == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="cotizada" <?php echo ($estado_filter == 'cotizada') ? 'selected' : ''; ?>>Cotizada</option>
                    <option value="en_compras" <?php echo ($estado_filter == 'en_compras') ? 'selected' : ''; ?>>En Compras</option>
                    <option value="en_gerencia" <?php echo ($estado_filter == 'en_gerencia') ? 'selected' : ''; ?>>En Gerencia</option>
                    <option value="aprobada" <?php echo ($estado_filter == 'aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                    <option value="rechazada" <?php echo ($estado_filter == 'rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                    <option value="completada" <?php echo ($estado_filter == 'completada') ? 'selected' : ''; ?>>Completada</option>
                </select>
            </div>
            
            <?php if ($estado_filter || $mes_filter): ?>
                <a href="listado-requisiciones.php" class="btn btn-secondary" style="height: 38px; display: flex; align-items: center;">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Results count -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 500;">
            <?php echo count($requisiciones); ?> requisicion(es) encontrada(s)
        </span>
    </div>

    <!-- Table -->
    <?php if (count($requisiciones) > 0): ?>
        <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: var(--gray-50); border-bottom: 2px solid var(--gray-200);">
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                No. Requisicion
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Solicitante
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Departamento
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Fecha
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Monto
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Estado
                            </th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requisiciones as $index => $req): ?>
                            <?php
                            $estado_label = '';
                            $estado_color = '';
                            $estado_bg = '';
                            $estado_icono = '';
                            switch($req['estado']) {
                                case 'pendiente': 
                                    $estado_label = 'Pendiente'; $estado_color = '#d97706'; $estado_bg = '#fffbeb'; $estado_icono = 'fa-clock'; break;
                                case 'cotizada':
                                    $estado_label = 'Cotizada'; $estado_color = '#7c3aed'; $estado_bg = '#f5f3ff'; $estado_icono = 'fa-file-invoice-dollar'; break;
                                case 'en_compras': 
                                    $estado_label = 'En Compras'; $estado_color = '#2563eb'; $estado_bg = '#eff6ff'; $estado_icono = 'fa-shopping-cart'; break;
                                case 'en_gerencia': 
                                    $estado_label = 'En Gerencia'; $estado_color = '#0284c7'; $estado_bg = '#f0f9ff'; $estado_icono = 'fa-user-tie'; break;
                                case 'aprobada': 
                                    $estado_label = 'Aprobada'; $estado_color = '#059669'; $estado_bg = '#ecfdf5'; $estado_icono = 'fa-check-circle'; break;
                                case 'rechazada': 
                                    $estado_label = 'Rechazada'; $estado_color = '#dc2626'; $estado_bg = '#fef2f2'; $estado_icono = 'fa-times-circle'; break;
                                case 'completada': 
                                    $estado_label = 'Completada'; $estado_color = '#64748b'; $estado_bg = '#f1f5f9'; $estado_icono = 'fa-check-double'; break;
                                default: 
                                    $estado_label = ucfirst($req['estado']); $estado_color = '#94a3b8'; $estado_bg = '#f8fafc'; $estado_icono = 'fa-file-alt';
                            }
                            
                            $monto_display = '-';
                            if ($req['monto_cotizado']) {
                                $porcentajeIva = isset($req['porcentaje_iva']) && $req['porcentaje_iva'] > 0 ? floatval($req['porcentaje_iva']) : 16;
                                $montoConIva = $req['monto_cotizado'] * (1 + ($porcentajeIva / 100));
                                $monto_display = '$' . number_format($montoConIva, 2);
                            }
                            
                            $row_bg = ($index % 2 === 0) ? 'white' : 'var(--gray-50)';
                            ?>
                            <tr style="background: <?php echo $row_bg; ?>; border-bottom: 1px solid var(--gray-100); transition: background 0.15s ease;" 
                                onmouseover="this.style.background='#f0f7ff'" 
                                onmouseout="this.style.background='<?php echo $row_bg; ?>'">
                                
                                <!-- Folio -->
                                <td style="padding: 0.75rem 1rem; white-space: nowrap;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-file-invoice" style="color: var(--primary); font-size: 0.75rem;"></i>
                                        <span style="font-weight: 700; color: var(--gray-800);"><?php echo htmlspecialchars($req['folio']); ?></span>
                                    </div>
                                </td>
                                
                                <!-- Solicitante -->
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-user" style="font-size: 0.5625rem; color: var(--primary);"></i>
                                        </div>
                                        <span style="font-weight: 600; color: var(--gray-700);"><?php echo htmlspecialchars($req['solicitante']); ?></span>
                                    </div>
                                </td>
                                
                                <!-- Departamento -->
                                <td style="padding: 0.75rem 1rem;">
                                    <?php if (!empty($req['sub_almacen_nombre'])): ?>
                                        <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600; background: var(--primary-light); padding: 2px 8px; border-radius: var(--radius-full);">
                                            <?php echo htmlspecialchars($req['sub_almacen_nombre']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Fecha -->
                                <td style="padding: 0.75rem 1rem; text-align: center; white-space: nowrap;">
                                    <span style="color: var(--gray-600); font-weight: 500;">
                                        <?php echo date('d/m/Y', strtotime($req['fecha_solicitud'])); ?>
                                    </span>
                                </td>
                                
                                <!-- Monto -->
                                <td style="padding: 0.75rem 1rem; text-align: right; white-space: nowrap;">
                                    <span style="font-weight: 700; color: <?php echo $monto_display !== '-' ? 'var(--success-dark)' : 'var(--text-muted)'; ?>; font-size: 0.875rem;">
                                        <?php echo $monto_display; ?>
                                    </span>
                                </td>
                                
                                <!-- Estado -->
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.6875rem; font-weight: 700; color: <?php echo $estado_color; ?>; background: <?php echo $estado_bg; ?>; border: 1px solid <?php echo $estado_color; ?>20; white-space: nowrap;">
                                        <i class="fas <?php echo $estado_icono; ?>" style="font-size: 0.5625rem;"></i>
                                        <?php echo $estado_label; ?>
                                    </span>
                                </td>
                                
                                <!-- Acciones -->
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <a href="ver-requisicion.php?id=<?php echo $req['id']; ?>" 
                                       class="btn btn-sm btn-primary" 
                                       style="padding: 4px 12px; font-size: 0.6875rem; border-radius: var(--radius-md);">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 3rem 1.5rem; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--gray-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-clipboard-list" style="font-size: 1.25rem; color: var(--gray-400);"></i>
            </div>
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--gray-700); margin: 0 0 0.25rem;">No hay requisiciones</h3>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">No se encontraron requisiciones con los filtros seleccionados.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
