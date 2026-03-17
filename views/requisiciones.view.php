<?php require_once 'includes/header.php'; ?>
<?php require 'includes/modal-system.php'; ?>

<main class="main-content">
    <!-- Page header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); letter-spacing: -0.3px; margin: 0 0 4px 0;">Requisiciones</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Gestiona y da seguimiento a las solicitudes</p>
        </div>
        <a href="nueva-requisicion.php" class="btn btn-primary" style="padding: 0.625rem 1.25rem; border-radius: var(--radius-full); font-size: 0.8125rem;">
            <i class="fas fa-plus"></i> Nueva Requisicion
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Print button -->
    <?php if ($user['rol'] === 'compras' || $user['rol'] === 'admin'): ?>
        <div style="margin-bottom: 1rem;">
            <button id="btn-imprimir-seleccionadas" disabled class="btn btn-secondary" style="opacity: 0.5;">
                <i class="fas fa-print"></i> Imprimir <span id="print-count">0</span> seleccionada(s)
            </button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i class="fas fa-filter" style="color: var(--primary); font-size: 0.75rem;"></i>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--gray-700); text-transform: uppercase; letter-spacing: 0.5px;">Filtros</span>
        </div>
        <form method="GET" action="requisiciones.php" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="min-width: 150px; flex: 1;">
                <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Mes</label>
                <select name="mes" id="mes" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--gray-200)'">
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
                <select name="anio" id="anio" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;">
                    <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($anio_filter == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div style="min-width: 150px; flex: 1;">
                <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Estado</label>
                <select name="estado" id="estado" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px; cursor: pointer;">
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
                <a href="requisiciones.php" class="btn btn-secondary" style="height: 38px; display: flex; align-items: center;">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            <?php endif; ?>
        </form>
        
        <!-- Show hidden -->
        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-100);">
            <form method="GET" action="requisiciones.php" id="form-mostrar-ocultas">
                <?php if ($mes_filter): ?><input type="hidden" name="mes" value="<?php echo htmlspecialchars($mes_filter); ?>"><?php endif; ?>
                <?php if ($anio_filter): ?><input type="hidden" name="anio" value="<?php echo htmlspecialchars($anio_filter); ?>"><?php endif; ?>
                <?php if ($estado_filter): ?><input type="hidden" name="estado" value="<?php echo htmlspecialchars($estado_filter); ?>"><?php endif; ?>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-muted); font-size: 0.8125rem; font-weight: 500;">
                    <input type="checkbox" name="mostrar_ocultas" id="mostrar_ocultas" value="1"
                        <?php echo $mostrar_ocultas ? 'checked' : ''; ?> onchange="this.form.submit()"
                        style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary);">
                    <i class="fas fa-eye-slash" style="font-size: 0.75rem;"></i>
                    Mostrar ocultas
                </label>
            </form>
        </div>
    </div>

    <!-- View Toggle -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 500;">
            <?php echo count($requisiciones); ?> requisicion(es)
        </span>
        <div style="display: flex; background: var(--gray-100); border-radius: var(--radius-md); padding: 3px; gap: 2px;">
            <button type="button" id="btn-vista-cards" onclick="cambiarVista('cards')"
                    style="display: flex; align-items: center; gap: 5px; padding: 5px 12px; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; background: var(--primary); color: white;">
                <i class="fas fa-th-large" style="font-size: 0.625rem;"></i> Cards
            </button>
            <button type="button" id="btn-vista-lista" onclick="cambiarVista('lista')"
                    style="display: flex; align-items: center; gap: 5px; padding: 5px 12px; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; background: transparent; color: var(--gray-500);">
                <i class="fas fa-list" style="font-size: 0.625rem;"></i> Lista
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <?php if (count($requisiciones) > 0): ?>
        <div class="requisiciones-grid" id="vista-cards">
            <?php foreach ($requisiciones as $req): ?>
                <?php
                $estado_class = '';
                $estado_icono = '';
                $estado_label = '';
                $estado_color = '';
                $estado_bg = '';
                switch($req['estado']) {
                    case 'pendiente': 
                        $estado_class = 'req-card-pendiente'; 
                        $estado_icono = 'fa-clock'; 
                        $estado_label = 'Pendiente';
                        $estado_color = '#d97706';
                        $estado_bg = '#fffbeb';
                        break;
                    case 'en_compras': 
                        $estado_class = 'req-card-compras'; 
                        $estado_icono = 'fa-shopping-cart'; 
                        $estado_label = 'En Compras';
                        $estado_color = '#2563eb';
                        $estado_bg = '#eff6ff';
                        break;
                    case 'en_gerencia': 
                        $estado_class = 'req-card-gerencia'; 
                        $estado_icono = 'fa-user-tie'; 
                        $estado_label = 'En Gerencia';
                        $estado_color = '#0284c7';
                        $estado_bg = '#f0f9ff';
                        break;
                    case 'aprobada': 
                        $estado_class = 'req-card-aprobada'; 
                        $estado_icono = 'fa-check-circle'; 
                        $estado_label = 'Aprobada';
                        $estado_color = '#059669';
                        $estado_bg = '#ecfdf5';
                        break;
                    case 'rechazada': 
                        $estado_class = 'req-card-rechazada'; 
                        $estado_icono = 'fa-times-circle'; 
                        $estado_label = 'Rechazada';
                        $estado_color = '#dc2626';
                        $estado_bg = '#fef2f2';
                        break;
                    case 'completada': 
                        $estado_class = 'req-card-completada'; 
                        $estado_icono = 'fa-check-double'; 
                        $estado_label = 'Completada';
                        $estado_color = '#64748b';
                        $estado_bg = '#f1f5f9';
                        break;
                    default: 
                        $estado_class = 'req-card-default'; 
                        $estado_icono = 'fa-file-alt';
                        $estado_label = ucfirst($req['estado']);
                        $estado_color = '#94a3b8';
                        $estado_bg = '#f8fafc';
                }
                $esServicio = (isset($req['tipo_requisicion']) && $req['tipo_requisicion'] === 'servicio') || strpos($req['folio'], 'SRV-') === 0;
                ?>
                <div class="req-card <?php echo $estado_class; ?>">
                    <!-- Checkbox -->
                    <?php if (($user['rol'] === 'compras' || $user['rol'] === 'admin') && $req['estado'] === 'aprobada'): ?>
                        <div class="req-card-checkbox">
                            <input type="checkbox" class="req-checkbox" data-req-id="<?php echo $req['id']; ?>">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Header with folio and status -->
                    <div class="req-card-header">
                        <div class="req-folio">
                            <i class="fas <?php echo $esServicio ? 'fa-tools' : 'fa-file-invoice'; ?>"></i>
                            <span><?php echo htmlspecialchars($req['folio']); ?></span>
                            <?php if ($esServicio): ?>
                                <span class="req-tipo-badge servicio">SRV</span>
                            <?php endif; ?>
                        </div>
                        <div class="req-estado">
                            <i class="fas <?php echo $estado_icono; ?>" style="font-size: 9px;"></i>
                            <?php echo $estado_label; ?>
                        </div>
                    </div>
                    
                    <!-- Body with info -->
                    <div class="req-card-body">
                        <!-- Date and Solicitante row -->
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0; border-bottom: 1px dashed var(--gray-100);">
                            <div style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--gray-500);">
                                <i class="fas fa-calendar-alt" style="font-size: 0.6875rem; color: var(--primary); width: 14px; text-align: center;"></i>
                                <span style="font-weight: 600; color: var(--gray-700);"><?php echo date('d/m/Y', strtotime($req['fecha_solicitud'])); ?></span>
                            </div>
                        </div>

                        <!-- Solicitante -->
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-user" style="font-size: 0.625rem; color: var(--primary);"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 600; color: var(--gray-800);"><?php echo htmlspecialchars($req['solicitante']); ?></div>
                                <?php if (!empty($req['sub_almacen_nombre'])): ?>
                                    <div style="font-size: 0.6875rem; color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($req['sub_almacen_nombre']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($user['rol'] === 'admin' || $user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general'): ?>
                            <?php if (!empty($req['usuario_nombre'])): ?>
                            <div style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: var(--text-light);">
                                <i class="fas fa-id-badge" style="font-size: 0.625rem; width: 14px; text-align: center;"></i>
                                <?php echo htmlspecialchars($req['usuario_nombre']); ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Monto -->
                        <?php 
                        $mostrarMonto = false;
                        if (($user['rol'] === 'compras' || $user['rol'] === 'gerencia' || $user['rol'] === 'gerencia_general') && $req['monto_cotizado']) {
                            $mostrarMonto = true;
                        }
                        if ($user['rol'] === 'departamento' && $req['usuario_id'] == $user['id'] && $req['monto_cotizado']) {
                            $mostrarMonto = true;
                        }
                        ?>
                        <?php if ($mostrarMonto): ?>
                            <div class="req-monto">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-dollar-sign" style="color: white; font-size: 0.625rem;"></i>
                                    </div>
                                    <?php 
                                    $porcentajeIva = isset($req['porcentaje_iva']) && $req['porcentaje_iva'] > 0 ? floatval($req['porcentaje_iva']) : 16;
                                    $montoConIva = $req['monto_cotizado'] * (1 + ($porcentajeIva / 100));
                                    ?>
                                    <span style="color: var(--success-dark); font-weight: 700; font-size: 1.125rem;">$<?php echo number_format($montoConIva, 2); ?></span>
                                    <span style="color: var(--text-light); font-size: 0.625rem; font-weight: 600; background: var(--gray-100); padding: 1px 6px; border-radius: var(--radius-full);">IVA <?php echo $porcentajeIva; ?>%</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($req['observaciones']): ?>
                            <div style="display: flex; align-items: flex-start; gap: 0.375rem; font-size: 0.75rem; color: var(--text-light); font-style: italic; margin-top: 0.125rem;">
                                <i class="fas fa-comment-alt" style="font-size: 0.625rem; margin-top: 2px; width: 14px; text-align: center; flex-shrink: 0;"></i>
                                <span><?php echo htmlspecialchars(substr($req['observaciones'], 0, 60)) . (strlen($req['observaciones']) > 60 ? '...' : ''); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer with actions -->
                    <div class="req-card-footer">
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap; flex: 1;">
                            <a href="ver-requisicion.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                            
                            <!-- Inventario button (solo productos) -->
                            <?php if (!$esServicio): ?>
                                <?php if (($user['rol'] === 'compras' || $user['rol'] === 'admin') && $req['estado'] === 'aprobada' && $req['agregado_a_inventario'] != 1): ?>
                                    <a href="agregar-a-inventario.php?id=<?php echo $req['id']; ?>" 
                                       class="btn btn-sm btn-success agregar-inventario-btn"
                                       data-req-id="<?php echo $req['id']; ?>"
                                       data-req-folio="<?php echo htmlspecialchars($req['folio']); ?>">
                                        <i class="fas fa-plus-circle"></i> Inventario
                                    </a>
                                <?php elseif ($req['agregado_a_inventario'] == 1): ?>
                                    <span class="btn btn-sm" style="background: var(--gray-100); color: var(--gray-500); cursor: default; font-weight: 600;">
                                        <i class="fas fa-check"></i> En Inventario
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($req['estado'] === 'aprobada'): ?>
                                    <span class="btn btn-sm" style="background: var(--success-light); color: var(--success); cursor: default; font-weight: 600;">
                                        <i class="fas fa-check-circle"></i> Aprobado
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <!-- Ocultar button (solo compras, admin y el creador - gerencia usa auto-ocultar) -->
                            <?php if (
                                (in_array($req['estado'], ['rechazada', 'aprobada', 'completada'])) && 
                                (in_array($user['rol'], ['compras', 'admin', 'departamento']) || $req['usuario_id'] == $user['id']) &&
                                !in_array($user['rol'], ['gerencia', 'gerencia_general'])
                            ): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-secondary ocultar-requisicion-btn"
                                        data-req-id="<?php echo $req['id']; ?>"
                                        data-req-folio="<?php echo htmlspecialchars($req['folio']); ?>"
                                        title="Ocultar">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Admin estado change -->
                        <?php if ($user['rol'] === 'admin'): ?>
                            <form method="POST" style="display: flex; align-items: center; gap: 4px;">
                                <input type="hidden" name="requisicion_id" value="<?php echo $req['id']; ?>">
                                <select name="nuevo_estado" class="req-estado-select">
                                    <option value="pendiente" <?php echo ($req['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="en_compras" <?php echo ($req['estado'] == 'en_compras') ? 'selected' : ''; ?>>En Compras</option>
                                    <option value="en_gerencia" <?php echo ($req['estado'] == 'en_gerencia') ? 'selected' : ''; ?>>En Gerencia</option>
                                    <option value="aprobada" <?php echo ($req['estado'] == 'aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                                    <option value="rechazada" <?php echo ($req['estado'] == 'rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                                    <option value="completada" <?php echo ($req['estado'] == 'completada') ? 'selected' : ''; ?>>Completada</option>
                                </select>
                                <button type="submit" name="cambiar_estado" class="btn btn-sm btn-primary" style="padding: 4px 8px;">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- List View (hidden by default) -->
        <div id="vista-lista" style="display: none; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: var(--gray-50); border-bottom: 2px solid var(--gray-200);">
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">No. Requisicion</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Solicitante</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Departamento</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Fecha</th>
                            <th style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Monto</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Estado</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--gray-600); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requisiciones as $index => $req): ?>
                            <?php
                            $tbl_estado_label = '';
                            $tbl_estado_color = '';
                            $tbl_estado_bg = '';
                            $tbl_estado_icono = '';
                            switch($req['estado']) {
                                case 'pendiente': $tbl_estado_label = 'Pendiente'; $tbl_estado_color = '#d97706'; $tbl_estado_bg = '#fffbeb'; $tbl_estado_icono = 'fa-clock'; break;
                                case 'cotizada': $tbl_estado_label = 'Cotizada'; $tbl_estado_color = '#7c3aed'; $tbl_estado_bg = '#f5f3ff'; $tbl_estado_icono = 'fa-file-invoice-dollar'; break;
                                case 'en_compras': $tbl_estado_label = 'En Compras'; $tbl_estado_color = '#2563eb'; $tbl_estado_bg = '#eff6ff'; $tbl_estado_icono = 'fa-shopping-cart'; break;
                                case 'en_gerencia': $tbl_estado_label = 'En Gerencia'; $tbl_estado_color = '#0284c7'; $tbl_estado_bg = '#f0f9ff'; $tbl_estado_icono = 'fa-user-tie'; break;
                                case 'aprobada': $tbl_estado_label = 'Aprobada'; $tbl_estado_color = '#059669'; $tbl_estado_bg = '#ecfdf5'; $tbl_estado_icono = 'fa-check-circle'; break;
                                case 'rechazada': $tbl_estado_label = 'Rechazada'; $tbl_estado_color = '#dc2626'; $tbl_estado_bg = '#fef2f2'; $tbl_estado_icono = 'fa-times-circle'; break;
                                case 'completada': $tbl_estado_label = 'Completada'; $tbl_estado_color = '#64748b'; $tbl_estado_bg = '#f1f5f9'; $tbl_estado_icono = 'fa-check-double'; break;
                                default: $tbl_estado_label = ucfirst($req['estado']); $tbl_estado_color = '#94a3b8'; $tbl_estado_bg = '#f8fafc'; $tbl_estado_icono = 'fa-file-alt';
                            }
                            $tbl_monto = '-';
                            if ($req['monto_cotizado']) {
                                $tbl_iva = isset($req['porcentaje_iva']) && $req['porcentaje_iva'] > 0 ? floatval($req['porcentaje_iva']) : 16;
                                $tbl_monto = '$' . number_format($req['monto_cotizado'] * (1 + ($tbl_iva / 100)), 2);
                            }
                            $tbl_row_bg = ($index % 2 === 0) ? 'white' : 'var(--gray-50)';
                            ?>
                            <tr style="background: <?php echo $tbl_row_bg; ?>; border-bottom: 1px solid var(--gray-100); transition: background 0.15s ease;"
                                onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='<?php echo $tbl_row_bg; ?>'">
                                <td style="padding: 0.75rem 1rem; white-space: nowrap;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-file-invoice" style="color: var(--primary); font-size: 0.75rem;"></i>
                                        <span style="font-weight: 700; color: var(--gray-800);"><?php echo htmlspecialchars($req['folio']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 26px; height: 26px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-user" style="font-size: 0.5625rem; color: var(--primary);"></i>
                                        </div>
                                        <span style="font-weight: 600; color: var(--gray-700);"><?php echo htmlspecialchars($req['solicitante']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?php if (!empty($req['sub_almacen_nombre'])): ?>
                                        <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600; background: var(--primary-light); padding: 2px 8px; border-radius: var(--radius-full);">
                                            <?php echo htmlspecialchars($req['sub_almacen_nombre']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center; white-space: nowrap;">
                                    <span style="color: var(--gray-600); font-weight: 500;"><?php echo date('d/m/Y', strtotime($req['fecha_solicitud'])); ?></span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right; white-space: nowrap;">
                                    <span style="font-weight: 700; color: <?php echo $tbl_monto !== '-' ? 'var(--success-dark)' : 'var(--text-muted)'; ?>; font-size: 0.875rem;">
                                        <?php echo $tbl_monto; ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.6875rem; font-weight: 700; color: <?php echo $tbl_estado_color; ?>; background: <?php echo $tbl_estado_bg; ?>; border: 1px solid <?php echo $tbl_estado_color; ?>20; white-space: nowrap;">
                                        <i class="fas <?php echo $tbl_estado_icono; ?>" style="font-size: 0.5625rem;"></i>
                                        <?php echo $tbl_estado_label; ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <a href="ver-requisicion.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-primary" style="padding: 4px 12px; font-size: 0.6875rem; border-radius: var(--radius-md);">
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
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No hay requisiciones</h3>
            <p>
                No se encontraron requisiciones
                <?php if ($estado_filter || $mes_filter): ?>
                    con los filtros seleccionados
                <?php endif; ?>
            </p>
            <?php if ($estado_filter || $mes_filter): ?>
                <a href="requisiciones.php" class="btn btn-secondary" style="margin-top: 0.875rem;">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<script>
function cambiarVista(vista) {
    var cards = document.getElementById('vista-cards');
    var lista = document.getElementById('vista-lista');
    var btnCards = document.getElementById('btn-vista-cards');
    var btnLista = document.getElementById('btn-vista-lista');
    
    if (vista === 'lista') {
        if (cards) cards.style.display = 'none';
        if (lista) lista.style.display = 'block';
        btnCards.style.background = 'transparent';
        btnCards.style.color = 'var(--gray-500)';
        btnLista.style.background = 'var(--primary)';
        btnLista.style.color = 'white';
    } else {
        if (cards) cards.style.display = '';
        if (lista) lista.style.display = 'none';
        btnCards.style.background = 'var(--primary)';
        btnCards.style.color = 'white';
        btnLista.style.background = 'transparent';
        btnLista.style.color = 'var(--gray-500)';
    }
    try { localStorage.setItem('req_vista', vista); } catch(e) {}
}

document.addEventListener('DOMContentLoaded', function() {
    // Restore saved view preference
    try {
        var saved = localStorage.getItem('req_vista');
        if (saved === 'lista') cambiarVista('lista');
    } catch(e) {}
    
    const checkboxes = document.querySelectorAll('.req-checkbox');
    const btnImprimir = document.getElementById('btn-imprimir-seleccionadas');
    
    if (btnImprimir) {
        function actualizarBotonImprimir() {
            const seleccionadas = Array.from(checkboxes).filter(cb => cb.checked);
            const count = seleccionadas.length;
            btnImprimir.disabled = count === 0;
            btnImprimir.style.opacity = count === 0 ? '0.5' : '1';
            document.getElementById('print-count').textContent = count;
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarBotonImprimir);
        });
        
        btnImprimir.addEventListener('click', function() {
            const seleccionadas = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.dataset.reqId);
            
            if (seleccionadas.length > 0) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'imprimir-requisiciones.php';
                form.target = '_blank';
                
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
    }
    
    const botonesOcultar = document.querySelectorAll('.ocultar-requisicion-btn');
    
    botonesOcultar.forEach(boton => {
        boton.addEventListener('click', function() {
            const reqId = this.dataset.reqId;
            const reqFolio = this.dataset.reqFolio;
            
            CustomModal.showConfirm(
                'Ocultar requisicion ' + reqFolio + '?',
                'La requisicion se ocultara de su vista pero permanecera en el sistema.',
                () => {
                    fetch('controllers/ocultar-requisicion.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ requisicion_id: reqId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            CustomModal.showSuccess('Requisicion ocultada exitosamente.');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            alert('Error: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error al procesar la solicitud');
                    });
                }
            );
        });
    });
});
</script>

<?php require 'includes/footer.php'; ?>
