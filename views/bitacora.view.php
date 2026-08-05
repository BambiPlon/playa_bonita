<main class="main-content">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fas fa-clipboard-list" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Bitacora del Sistema
            </h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem; font-size: 0.875rem;">
                Registro de todas las actividades realizadas en el sistema
            </p>
        </div>
        <a href="bitacora.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-sync-alt"></i> Actualizar
        </a>
    </div>

        <!-- Estadísticas rápidas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: white; border-radius: var(--radius-lg); padding: 1.25rem; box-shadow: var(--shadow-sm); border-left: 4px solid #3b82f6;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: #eff6ff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="color: #3b82f6; font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Actividad Hoy</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $estadisticas['hoy']; ?></p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; border-radius: var(--radius-lg); padding: 1.25rem; box-shadow: var(--shadow-sm); border-left: 4px solid #10b981;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: #ecfdf5; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-sign-in-alt" style="color: #10b981; font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Inicios de Sesion</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $estadisticas['logins_hoy']; ?></p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; border-radius: var(--radius-lg); padding: 1.25rem; box-shadow: var(--shadow-sm); border-left: 4px solid #f59e0b;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: #fef3c7; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-alt" style="color: #f59e0b; font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Requisiciones Hoy</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $estadisticas['requisiciones_hoy']; ?></p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; border-radius: var(--radius-lg); padding: 1.25rem; box-shadow: var(--shadow-sm); border-left: 4px solid #8b5cf6;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: #f3e8ff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-warehouse" style="color: #8b5cf6; font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Salidas Almacen</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $estadisticas['salidas_hoy']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div style="background: white; border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-filter" style="color: var(--primary);"></i>
                <span style="font-weight: 600; color: var(--text-primary);">Filtros</span>
            </div>
            
            <form method="GET" action="bitacora.php" id="formFiltros">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <!-- Filtro por Acción -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Accion</label>
                        <select name="accion" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                            <option value="">Todas las acciones</option>
                            <?php foreach ($acciones as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($_GET['accion']) && $_GET['accion'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro por Módulo -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Modulo</label>
                        <select name="modulo" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                            <option value="">Todos los modulos</option>
                            <?php foreach ($modulos as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro por Usuario -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Usuario</label>
                        <select name="usuario" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios_bitacora as $usr): ?>
                                <option value="<?php echo $usr['usuario_id']; ?>" <?php echo (isset($_GET['usuario']) && $_GET['usuario'] == $usr['usuario_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($usr['usuario_nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Fecha Desde -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Fecha Desde</label>
                        <input type="date" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                    </div>
                    
                    <!-- Fecha Hasta -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                    </div>
                    
                    <!-- Límite -->
                    <div>
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Mostrar</label>
                        <select name="limite" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                            <option value="50" <?php echo (isset($_GET['limite']) && $_GET['limite'] == '50') ? 'selected' : ''; ?>>50 registros</option>
                            <option value="100" <?php echo (!isset($_GET['limite']) || $_GET['limite'] == '100') ? 'selected' : ''; ?>>100 registros</option>
                            <option value="200" <?php echo (isset($_GET['limite']) && $_GET['limite'] == '200') ? 'selected' : ''; ?>>200 registros</option>
                            <option value="500" <?php echo (isset($_GET['limite']) && $_GET['limite'] == '500') ? 'selected' : ''; ?>>500 registros</option>
                        </select>
                    </div>
                </div>
                
                <!-- Búsqueda y botón limpiar -->
                <div style="display: flex; gap: 1rem; margin-top: 1rem; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 4px; color: var(--text-muted); font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px;">Buscar en descripcion</label>
                        <input type="text" name="busqueda" value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>" placeholder="Buscar..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 0.8125rem; background: white; color: var(--text-primary); height: 38px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 38px;">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <?php if (!empty($_GET['accion']) || !empty($_GET['modulo']) || !empty($_GET['usuario']) || !empty($_GET['fecha_desde']) || !empty($_GET['fecha_hasta']) || !empty($_GET['busqueda'])): ?>
                        <a href="bitacora.php" class="btn btn-secondary" style="height: 38px; display: flex; align-items: center;">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabla de registros -->
        <div style="background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--text-primary);">
                    <i class="fas fa-list" style="color: var(--primary); margin-right: 0.5rem;"></i>
                    <?php echo count($registros); ?> registro(s) encontrado(s)
                </span>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--gray-50);">
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">Fecha/Hora</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">Usuario</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">Accion</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">Modulo</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">Descripcion</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--gray-200);">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registros)): ?>
                            <tr>
                                <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                    No se encontraron registros con los filtros seleccionados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registros as $registro): ?>
                                <?php
                                    // Determinar color según la acción
                                    $colorAccion = '#6b7280';
                                    $bgAccion = '#f3f4f6';
                                    $iconAccion = 'fas fa-circle';
                                    
                                    switch ($registro['accion']) {
                                        case 'login':
                                            $colorAccion = '#10b981';
                                            $bgAccion = '#ecfdf5';
                                            $iconAccion = 'fas fa-sign-in-alt';
                                            break;
                                        case 'logout':
                                            $colorAccion = '#6b7280';
                                            $bgAccion = '#f3f4f6';
                                            $iconAccion = 'fas fa-sign-out-alt';
                                            break;
                                        case 'crear':
                                            $colorAccion = '#3b82f6';
                                            $bgAccion = '#eff6ff';
                                            $iconAccion = 'fas fa-plus-circle';
                                            break;
                                        case 'editar':
                                            $colorAccion = '#f59e0b';
                                            $bgAccion = '#fef3c7';
                                            $iconAccion = 'fas fa-edit';
                                            break;
                                        case 'eliminar':
                                            $colorAccion = '#ef4444';
                                            $bgAccion = '#fee2e2';
                                            $iconAccion = 'fas fa-trash';
                                            break;
                                        case 'aprobar':
                                            $colorAccion = '#10b981';
                                            $bgAccion = '#ecfdf5';
                                            $iconAccion = 'fas fa-check-circle';
                                            break;
                                        case 'rechazar':
                                            $colorAccion = '#ef4444';
                                            $bgAccion = '#fee2e2';
                                            $iconAccion = 'fas fa-times-circle';
                                            break;
                                        case 'cotizar':
                                            $colorAccion = '#8b5cf6';
                                            $bgAccion = '#f3e8ff';
                                            $iconAccion = 'fas fa-dollar-sign';
                                            break;
                                        case 'salida_almacen':
                                            $colorAccion = '#ec4899';
                                            $bgAccion = '#fce7f3';
                                            $iconAccion = 'fas fa-arrow-right';
                                            break;
                                        case 'entrada_almacen':
                                            $colorAccion = '#06b6d4';
                                            $bgAccion = '#cffafe';
                                            $iconAccion = 'fas fa-arrow-left';
                                            break;
                                        case 'imprimir':
                                            $colorAccion = '#64748b';
                                            $bgAccion = '#f1f5f9';
                                            $iconAccion = 'fas fa-print';
                                            break;
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid var(--gray-100); transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                    <td style="padding: 0.875rem 1rem; font-size: 0.8125rem; white-space: nowrap;">
                                        <div style="font-weight: 500; color: var(--text-primary);"><?php echo date('d/m/Y', strtotime($registro['created_at'])); ?></div>
                                        <div style="color: var(--text-muted); font-size: 0.75rem;"><?php echo date('H:i:s', strtotime($registro['created_at'])); ?></div>
                                    </td>
                                    <td style="padding: 0.875rem 1rem; font-size: 0.8125rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 32px; height: 32px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user" style="color: var(--primary); font-size: 0.75rem;"></i>
                                            </div>
                                            <span style="font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($registro['usuario_nombre'] ?? 'Sistema'); ?></span>
                                        </div>
                                    </td>
                                    <td style="padding: 0.875rem 1rem; text-align: center;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; background: <?php echo $bgAccion; ?>; color: <?php echo $colorAccion; ?>;">
                                            <i class="<?php echo $iconAccion; ?>" style="font-size: 0.625rem;"></i>
                                            <?php echo htmlspecialchars($acciones[$registro['accion']] ?? $registro['accion']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 0.875rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; background: var(--gray-100); color: var(--text-secondary);">
                                            <?php echo htmlspecialchars($modulos[$registro['modulo']] ?? $registro['modulo']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--text-secondary); max-width: 300px;">
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($registro['descripcion'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($registro['descripcion'] ?? '-'); ?>
                                        </div>
                                    </td>
                                    <td style="padding: 0.875rem 1rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <?php echo htmlspecialchars($registro['ip_address'] ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
</main>
