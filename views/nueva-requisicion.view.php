<?php require 'includes/header.php'; ?>
<?php 
// Obtener unidades de la base de datos
$conn = getConnection();
$unidades_lista = [];
$query_unidades = $conn->query("SELECT nombre FROM unidades WHERE activo = 1 ORDER BY nombre");
if ($query_unidades) {
    while ($row = $query_unidades->fetch_assoc()) {
        $unidades_lista[] = $row['nombre'];
    }
}
// Si no hay unidades en BD, usar lista predeterminada
if (empty($unidades_lista)) {
    $unidades_lista = ['pieza', 'unidad', 'caja', 'paquete', 'bolsa', 'rollo', 'metro', 'litro', 'galón', 'kilogramo', 'gramo', 'juego', 'par', 'docena', 'cubeta', 'bote', 'botella', 'garrafón', 'servicio'];
}
?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Nueva Requisicion</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Seleccione el tipo de requisicion que desea crear</p>
        </div>
        <a href="requisiciones.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Agregando validación para evitar warning de variable no definida -->
    <?php if (isset($mensaje) && !empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="animation: slideDown 0.3s ease-out; margin: 0 20px;">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>" style="margin-right: 0.5rem;"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Selector de tipo de requisición -->
    <div id="selectorTipoRequisicion" style="padding: 0 20px 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; max-width: 800px; margin: 0 auto;">
            <!-- Opcion Producto -->
            <div class="tipo-requisicion-card" onclick="seleccionarTipoRequisicion('producto')" 
                 style="background: white; border: 2px solid var(--gray-200); border-radius: 14px; padding: 2rem; cursor: pointer; transition: all 0.2s ease; text-align: center;"
                 onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(37, 99, 235, 0.12)'"
                 onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='var(--gray-200)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'}">
                <div style="width: 64px; height: 64px; background: var(--primary-light); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                    <i class="fas fa-boxes" style="font-size: 1.5rem; color: var(--primary);"></i>
                </div>
                <h3 style="color: var(--gray-900); font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Requisicion de Producto</h3>
                <p style="color: var(--text-muted); font-size: 0.8125rem; line-height: 1.5;">Materiales, suministros, equipos u otros productos fisicos para el inventario.</p>
            </div>
            
            <!-- Opcion Servicio -->
            <div class="tipo-requisicion-card" onclick="seleccionarTipoRequisicion('servicio')" 
                 style="background: white; border: 2px solid var(--gray-200); border-radius: 14px; padding: 2rem; cursor: pointer; transition: all 0.2s ease; text-align: center;"
                 onmouseover="this.style.borderColor='var(--success)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(16, 185, 129, 0.12)'"
                 onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='var(--gray-200)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'}">
                <div style="width: 64px; height: 64px; background: var(--success-light); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                    <i class="fas fa-tools" style="font-size: 1.5rem; color: var(--success);"></i>
                </div>
                <h3 style="color: var(--gray-900); font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Requisicion de Servicio</h3>
                <p style="color: var(--text-muted); font-size: 0.8125rem; line-height: 1.5;">Mantenimiento, reparaciones, consultorias u otros servicios externos.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="nueva-requisicion.php" style="max-width: 100%; width: 100%; margin: 0; padding: 0 20px 20px; display: none;" id="formNuevaRequisicion" onsubmit="return confirmarCrearRequisicion(event)">
        <input type="hidden" name="tipo_requisicion" id="tipoRequisicionInput" value="producto">
        <!-- Sección de información general con fondo blanco -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100);">
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                    Informacion General
                </h2>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="solicitante" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            Solicitante <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" id="solicitante" name="solicitante" 
                               value="<?php echo htmlspecialchars($user['nombre_completo'] ?? $_SESSION['user_nombre'] ?? ''); ?>" 
                               style="background: #f3f4f6; border: 1px solid #d1d5db; color: #111827; cursor: not-allowed;" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 5px; color: #4b5563; font-weight: 500;">Sub-Almacén</label>
                        <div style="position: relative;">
                            <?php 
                            $sub_almacen_display = $user['sub_almacen_nombre'] ?? $_SESSION['user_sub_almacen_nombre'] ?? null;
                            $user_rol = $user['rol'] ?? $_SESSION['user_rol'] ?? '';
                            
                            // Si no tiene sub-almacén pero es un rol especial, mostrar mensaje apropiado
                            if (empty($sub_almacen_display) && in_array($user_rol, ['admin', 'compras', 'gerencia', 'gerencia_general'])) {
                                $sub_almacen_display = 'Todos los sub-almacenes';
                            } elseif (empty($sub_almacen_display)) {
                                $sub_almacen_display = 'No asignado';
                            }
                            ?>
                            <input type="text" value="<?php echo htmlspecialchars($sub_almacen_display); ?>" 
                                   style="background: #f3f4f6; border: 1px solid #d1d5db; color: #111827; cursor: not-allowed;" readonly>
                            <input type="hidden" name="sub_almacen_id" value="<?php echo $user['sub_almacen_id'] ?? $_SESSION['user_sub_almacen_id'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_solicitud" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-calendar-alt" style="color: #2563eb;"></i>
                            Fecha de Solicitud <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" id="fecha_solicitud" name="fecha_solicitud" 
                               value="<?php echo date('Y-m-d'); ?>" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label for="justificacion" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151;">
                        <i class="fas fa-comment-alt" style="color: #2563eb;"></i>
                        Justificación
                    </label>
                    <textarea id="justificacion" name="justificacion" rows="3" 
                              placeholder="Ingrese la justificación de la requisición (opcional)..."
                              style="resize: vertical; background: white; border: 1px solid #d1d5db; color: #111827;"></textarea>
                </div>
            </div>
        </div>

        <!-- Indicador del tipo de requisición -->
        <div id="tipoRequisicionIndicador" style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
            <span id="indicadorTipo" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.875rem;"></span>
            <button type="button" onclick="cambiarTipoRequisicion()" style="background: none; border: none; color: #2563eb; cursor: pointer; font-size: 0.875rem; text-decoration: underline;">
                Cambiar tipo
            </button>
        </div>

        <!-- Sección de productos con fondo blanco -->
        <div id="seccionProductos" class="card" style="margin-top: 1.5rem;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-boxes" style="color: var(--primary);"></i>
                    Productos Solicitados
                </h2>
                <div style="display: flex; gap: 0.5rem;">
                    <?php if (!empty($plantillas_usuario)): ?>
                    <button type="button" onclick="abrirModalPlantilla()" class="btn btn-sm" style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 5px; font-size: 0.75rem; padding: 6px 12px; border-radius: var(--radius-md);">
                        <i class="fas fa-copy"></i> Cargar Plantilla
                    </button>
                    <?php endif; ?>
                    <button type="button" onclick="agregarProducto()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle"></i> Agregar Producto
                    </button>
                </div>
            </div>
            <div style="padding: 1.5rem;">
                <div id="productos-container" style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Card de producto individual -->
                    <div class="producto-item" data-index="0" data-modo="existente" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;">
                        <button type="button" onclick="this.parentElement.remove()" 
                                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                            <i class="fas fa-times"></i>
                        </button>

                        <!-- Toggle existente / nuevo -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                            <div id="toggle-wrap-0" style="display: inline-flex; background: var(--gray-100); border-radius: 8px; overflow: hidden; border: 1px solid var(--gray-200);">
                                <button type="button" id="btn-existente-0" onclick="toggleModoProducto(0, 'existente')"
                                        style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: var(--primary); color: white;">
                                    <i class="fas fa-search"></i> Del inventario
                                </button>
                                <button type="button" id="btn-nuevo-0" onclick="toggleModoProducto(0, 'nuevo')"
                                        style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--gray-500);">
                                    <i class="fas fa-plus"></i> Producto nuevo
                                </button>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-box"></i>
                                    Producto <span style="color: #ef4444;">*</span>
                                </label>
                                <!-- Modo existente: datalist -->
                                <div id="modo-existente-0">
                                    <input list="productos-list-0" id="productos_search_0" 
                                           placeholder="Escriba para buscar..." onchange="selectProducto(0)" 
                                           style="width: 100%;" required>
                                    <datalist id="productos-list-0">
                                        <?php foreach ($datosFormulario['productos'] as $prod): ?>
                                            <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                                    data-id="<?php echo $prod['id']; ?>"
                                                    data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>"
                                                    data-unidad="<?php echo htmlspecialchars($prod['unidad'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($prod['nombre']); ?> - <?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </datalist>
                                    <input type="hidden" id="producto_id_0">
                                </div>
                                <!-- Modo nuevo: texto libre -->
                                <div id="modo-nuevo-0" style="display: none;">
                                    <input type="text" id="producto_nombre_input_0" 
                                           placeholder="Nombre del producto nuevo" 
                                           style="width: 100%;">
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-hashtag"></i>
                                    Cantidad <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="number" class="campo-cantidad" min="1" placeholder="0" 
                                       style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-ruler"></i>
                                    Unidad de Medida <span style="color: #ef4444;">*</span>
                                </label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($unidades_lista as $unidad): ?>
                                            <option value="<?php echo htmlspecialchars($unidad); ?>"><?php echo ucfirst(htmlspecialchars($unidad)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" onclick="abrirModalUnidad()" 
                                            title="Agregar nueva unidad"
                                            style="background: #10b981; color: white; border: none; padding: 10px 14px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: all 0.2s ease; white-space: nowrap;"
                                            onmouseover="this.style.background='#059669'"
                                            onmouseout="this.style.background='#10b981'">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed rgba(37, 99, 235, 0.2); text-align: center;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.375rem;"></i>
                        Puede agregar más productos utilizando el botón "Agregar Producto"
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Sección de servicios (oculta por defecto) -->
        <div id="seccionServicios" class="card" style="margin-top: 1.5rem; display: none;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-tools" style="color: var(--success);"></i>
                    Servicios Solicitados
                </h2>
                <button type="button" onclick="agregarServicio()" class="btn btn-sm" style="background: var(--success); color: white; border: none;">
                    <i class="fas fa-plus-circle"></i> Agregar Servicio
                </button>
            </div>
            <div style="padding: 1.5rem;">
                <div id="servicios-container" style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Card de servicio individual -->
                    <div class="servicio-item" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;">
                        <button type="button" onclick="this.parentElement.remove()" 
                                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; align-items: start;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-concierge-bell"></i>
                                    Descripción del Servicio <span style="color: #ef4444;">*</span>
                                </label>
                                <textarea name="servicios_descripcion[]" rows="2" 
                                          placeholder="Describa el servicio requerido (ej: Mantenimiento de aire acondicionado, Fumigación, etc.)"
                                          style="width: 100%; resize: vertical; background: white; border: 1px solid #d1d5db; color: #111827;" required></textarea>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-tag"></i>
                                    Tipo de Servicio <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="servicios_tipo[]" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                                    <option value="">Seleccionar...</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="reparacion">Reparación</option>
                                    <option value="instalacion">Instalación</option>
                                    <option value="consultoria">Consultoría</option>
                                    <option value="limpieza">Limpieza</option>
                                    <option value="seguridad">Seguridad</option>
                                    <option value="transporte">Transporte</option>
                                    <option value="capacitacion">Capacitación</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-top: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Ubicación/Área
                                </label>
                                <input type="text" name="servicios_ubicacion[]" placeholder="Ej: Edificio principal, Área de cocina"
                                       style="background: white; border: 1px solid #d1d5db; color: #111827;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-calendar-check"></i>
                                    Fecha Requerida
                                </label>
                                <input type="date" name="servicios_fecha[]" 
                                       style="background: white; border: 1px solid #d1d5db; color: #111827;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Prioridad
                                </label>
                                <select name="servicios_prioridad[]" style="background: white; border: 1px solid #d1d5db; color: #111827;">
                                    <option value="normal">Normal</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed rgba(16, 185, 129, 0.2); text-align: center;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.375rem;"></i>
                        Puede agregar más servicios utilizando el botón "Agregar Servicio"
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
            <a href="requisiciones.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Crear Requisicion
            </button>
        </div>
    </form>
</main>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.producto-item:hover {
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    transform: translateY(-2px);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let contadorProductos = 1;

function agregarProducto() {
    contadorProductos++;
    const idx = contadorProductos;
    const container = document.getElementById('productos-container');
    const nuevoProducto = document.createElement('div');
    nuevoProducto.className = 'producto-item';
    nuevoProducto.setAttribute('data-index', idx);
    nuevoProducto.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;';
    nuevoProducto.setAttribute('data-modo', 'existente');
    nuevoProducto.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
            <i class="fas fa-times"></i>
        </button>

        <!-- Toggle existente / nuevo -->
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <div id="toggle-wrap-${idx}" style="display: inline-flex; background: var(--gray-100); border-radius: 8px; overflow: hidden; border: 1px solid var(--gray-200);">
                <button type="button" id="btn-existente-${idx}" onclick="toggleModoProducto(${idx}, 'existente')"
                        style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: var(--primary); color: white;">
                    <i class="fas fa-search"></i> Del inventario
                </button>
                <button type="button" id="btn-nuevo-${idx}" onclick="toggleModoProducto(${idx}, 'nuevo')"
                        style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--gray-500);">
                    <i class="fas fa-plus"></i> Producto nuevo
                </button>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-box"></i>
                    Producto <span style="color: #ef4444;">*</span>
                </label>
                <!-- Modo existente -->
                <div id="modo-existente-${idx}">
                    <input list="productos-list-${idx}" id="productos_search_${idx}" 
                           placeholder="Escriba para buscar..." onchange="selectProducto(${idx})" 
                           style="width: 100%;" required>
                    <datalist id="productos-list-${idx}">
                        <?php foreach ($datosFormulario['productos'] as $prod): ?>
                            <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                    data-id="<?php echo $prod['id']; ?>"
                                    data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>"
                                    data-unidad="<?php echo htmlspecialchars($prod['unidad'] ?? ''); ?>">
                                <?php echo htmlspecialchars($prod['nombre']); ?> - <?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                    <input type="hidden" id="producto_id_${idx}">
                </div>
                <!-- Modo nuevo -->
                <div id="modo-nuevo-${idx}" style="display: none;">
                    <input type="text" id="producto_nombre_input_${idx}" 
                           placeholder="Nombre del producto nuevo" 
                           style="width: 100%;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-hashtag"></i>
                    Cantidad <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" class="campo-cantidad" min="1" placeholder="0" 
                       style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-ruler"></i>
                    Unidad de Medida <span style="color: #ef4444;">*</span>
                </label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827;">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($unidades_lista as $unidad): ?>
                        <option value="<?php echo htmlspecialchars($unidad); ?>"><?php echo ucfirst(htmlspecialchars($unidad)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="abrirModalUnidad()" 
                            title="Agregar nueva unidad"
                            style="background: #10b981; color: white; border: none; padding: 10px 14px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: all 0.2s ease; white-space: nowrap;"
                            onmouseover="this.style.background='#059669'"
                            onmouseout="this.style.background='#10b981'">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(nuevoProducto);
}

function toggleModoProducto(index, modo) {
    const btnExistente = document.getElementById('btn-existente-' + index);
    const btnNuevo = document.getElementById('btn-nuevo-' + index);
    const modoExistente = document.getElementById('modo-existente-' + index);
    const modoNuevo = document.getElementById('modo-nuevo-' + index);
    const card = modoExistente.closest('.producto-item');
    
    if (modo === 'nuevo') {
        btnNuevo.style.background = 'var(--primary)';
        btnNuevo.style.color = 'white';
        btnExistente.style.background = 'transparent';
        btnExistente.style.color = 'var(--gray-500)';
        
        modoExistente.style.display = 'none';
        modoNuevo.style.display = 'block';
        card.setAttribute('data-modo', 'nuevo');
        
        var nombreInput = document.getElementById('producto_nombre_input_' + index);
        if (nombreInput) { nombreInput.required = true; nombreInput.focus(); }
        
        // Desactivar required del search
        var searchInput = modoExistente.querySelector('input[list]');
        if (searchInput) searchInput.required = false;
    } else {
        btnExistente.style.background = 'var(--primary)';
        btnExistente.style.color = 'white';
        btnNuevo.style.background = 'transparent';
        btnNuevo.style.color = 'var(--gray-500)';
        
        modoExistente.style.display = 'block';
        modoNuevo.style.display = 'none';
        card.setAttribute('data-modo', 'existente');
        
        var nombreInput = document.getElementById('producto_nombre_input_' + index);
        if (nombreInput) { nombreInput.required = false; nombreInput.value = ''; }
        
        // Activar required del search
        var searchInput = modoExistente.querySelector('input[list]');
        if (searchInput) searchInput.required = true;
    }
}

function selectProducto(index) {
    const searchInput = document.getElementById('productos_search_' + index);
    const hiddenInput = document.getElementById('producto_id_' + index);
    
    const datalist = document.getElementById('productos-list-' + index);
    const options = datalist.querySelectorAll('option');
    let found = false;
    
    options.forEach(option => {
        if (option.value === searchInput.value) {
            hiddenInput.value = option.getAttribute('data-id');
            found = true;
            
            // Auto-completar la unidad
            var unidadProd = option.getAttribute('data-unidad');
            if (unidadProd) {
                var card = searchInput.closest('.producto-item');
                if (card) {
                    var unidadSelect = card.querySelector('.campo-unidad');
                    if (unidadSelect) {
                        for (var i = 0; i < unidadSelect.options.length; i++) {
                            if (unidadSelect.options[i].value.toLowerCase() === unidadProd.toLowerCase()) {
                                unidadSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            }
        }
    });
    
    if (!found) {
        hiddenInput.value = '';
    }
}

// ========== FUNCIONES PARA TIPO DE REQUISICIÓN ==========
let tipoRequisicionActual = '';

function seleccionarTipoRequisicion(tipo) {
    tipoRequisicionActual = tipo;
    document.getElementById('tipoRequisicionInput').value = tipo;
    
    // Ocultar selector y mostrar formulario
    document.getElementById('selectorTipoRequisicion').style.display = 'none';
    document.getElementById('formNuevaRequisicion').style.display = 'block';
    
    // Actualizar indicador
    const indicador = document.getElementById('indicadorTipo');
    if (tipo === 'producto') {
        indicador.innerHTML = '<i class="fas fa-boxes"></i> Requisición de Producto';
        indicador.style.background = '#dbeafe';
        indicador.style.color = '#1e40af';
        document.getElementById('seccionProductos').style.display = 'block';
        document.getElementById('seccionServicios').style.display = 'none';
        
        // Habilitar campos de productos y deshabilitar campos de servicios
        document.querySelectorAll('#seccionProductos [required]').forEach(el => el.disabled = false);
        document.querySelectorAll('#seccionServicios [required]').forEach(el => el.disabled = true);
    } else {
        indicador.innerHTML = '<i class="fas fa-tools"></i> Requisición de Servicio';
        indicador.style.background = '#d1fae5';
        indicador.style.color = '#065f46';
        document.getElementById('seccionProductos').style.display = 'none';
        document.getElementById('seccionServicios').style.display = 'block';
        
        // Habilitar campos de servicios y deshabilitar campos de productos
        document.querySelectorAll('#seccionServicios [required]').forEach(el => el.disabled = false);
        document.querySelectorAll('#seccionProductos [required]').forEach(el => el.disabled = true);
    }
}

function cambiarTipoRequisicion() {
    Swal.fire({
        title: '¿Cambiar tipo de requisición?',
        text: 'Se perderán los datos ingresados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('selectorTipoRequisicion').style.display = 'block';
            document.getElementById('formNuevaRequisicion').style.display = 'none';
            tipoRequisicionActual = '';
        }
    });
}

let servicioCounter = 1;
function agregarServicio() {
    const container = document.getElementById('servicios-container');
    const nuevoServicio = document.createElement('div');
    nuevoServicio.className = 'servicio-item';
    nuevoServicio.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;';
    nuevoServicio.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
            <i class="fas fa-times"></i>
        </button>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; align-items: start;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-concierge-bell"></i>
                    Descripción del Servicio <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="servicios_descripcion[]" rows="2" 
                          placeholder="Describa el servicio requerido (ej: Mantenimiento de aire acondicionado, Fumigación, etc.)"
                          style="width: 100%; resize: vertical; background: white; border: 1px solid #d1d5db; color: #111827;" required></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-tag"></i>
                    Tipo de Servicio <span style="color: #ef4444;">*</span>
                </label>
                <select name="servicios_tipo[]" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                    <option value="">Seleccionar...</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="reparacion">Reparación</option>
                    <option value="instalacion">Instalación</option>
                    <option value="consultoria">Consultoría</option>
                    <option value="limpieza">Limpieza</option>
                    <option value="seguridad">Seguridad</option>
                    <option value="transporte">Transporte</option>
                    <option value="capacitacion">Capacitación</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-top: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-map-marker-alt"></i>
                    Ubicación/Área
                </label>
                <input type="text" name="servicios_ubicacion[]" placeholder="Ej: Edificio principal, Área de cocina"
                       style="background: white; border: 1px solid #d1d5db; color: #111827;">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar-check"></i>
                    Fecha Requerida
                </label>
                <input type="date" name="servicios_fecha[]" 
                       style="background: white; border: 1px solid #d1d5db; color: #111827;">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Prioridad
                </label>
                <select name="servicios_prioridad[]" style="background: white; border: 1px solid #d1d5db; color: #111827;">
                    <option value="normal">Normal</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(nuevoServicio);
    servicioCounter++;
}

function confirmarCrearRequisicion(event) {
    event.preventDefault();
    
    const tipo = document.getElementById('tipoRequisicionInput').value;
    let items, itemName;
    
    if (tipo === 'servicio') {
        items = document.querySelectorAll('.servicio-item');
        itemName = 'servicio(s)';
    } else {
        items = document.querySelectorAll('.producto-item');
        itemName = 'producto(s)';
    }
    
    if (items.length === 0) {
        alertaError('Debes agregar al menos un ' + (tipo === 'servicio' ? 'servicio' : 'producto') + ' a la requisicion');
        return false;
    }
    
    // Validar que todos los productos tengan unidad seleccionada
    if (tipo !== 'servicio') {
        var sinUnidad = false;
        items.forEach(function(card) {
            var unidadSel = card.querySelector('.campo-unidad');
            if (!unidadSel || !unidadSel.value) {
                sinUnidad = true;
            }
        });
        if (sinUnidad) {
            alertaError('Todos los productos deben tener una unidad seleccionada.');
            return false;
        }
    }
    
    Swal.fire({
        title: '¿Crear requisición?',
        html: `<p>Se creará una requisición de <strong>${tipo}</strong> con <strong>${items.length}</strong> ${itemName}</p>
               <p class="text-muted" style="font-size: 0.9em; color: #6b7280; margin-top: 10px;">Se notificará al departamento de compras.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
  if (result.isConfirmed) {
  // Construir datos de productos dinamicamente en orden DOM
  var form = document.getElementById('formNuevaRequisicion');
  
  // Eliminar hidden inputs previos generados
  form.querySelectorAll('.dynamic-product-data').forEach(function(el) { el.remove(); });
  
  // Iterar cards en orden DOM y crear inputs hidden con name arrays
  document.querySelectorAll('#productos-container .producto-item').forEach(function(card) {
      var idx = card.getAttribute('data-index');
      var modo = card.getAttribute('data-modo') || 'existente';
      
      var productoId, productoNombre;
      
      if (modo === 'nuevo') {
          productoId = 'otro';
          var nameInput = document.getElementById('producto_nombre_input_' + idx);
          productoNombre = nameInput ? nameInput.value : '';
      } else {
          var hiddenId = document.getElementById('producto_id_' + idx);
          productoId = hiddenId ? hiddenId.value : '';
          productoNombre = '';
      }
      
      var cantidad = card.querySelector('.campo-cantidad');
      var unidad = card.querySelector('.campo-unidad');
      
      // Crear hidden inputs dentro del form
      var h1 = document.createElement('input');
      h1.type = 'hidden'; h1.name = 'productos[]'; h1.value = productoId; h1.className = 'dynamic-product-data';
      form.appendChild(h1);
      
      var h2 = document.createElement('input');
      h2.type = 'hidden'; h2.name = 'productos_nombre_custom[]'; h2.value = productoNombre; h2.className = 'dynamic-product-data';
      form.appendChild(h2);
      
      var h3 = document.createElement('input');
      h3.type = 'hidden'; h3.name = 'cantidades[]'; h3.value = cantidad ? cantidad.value : '0'; h3.className = 'dynamic-product-data';
      form.appendChild(h3);
      
      var h4 = document.createElement('input');
      h4.type = 'hidden'; h4.name = 'unidades[]'; h4.value = unidad ? unidad.value : ''; h4.className = 'dynamic-product-data';
      form.appendChild(h4);
  });
  
  form.submit();
        }
    });
    
    return false;
}

function alertaError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: mensaje
    });
}

// ========== FUNCIONES PARA MODAL DE UNIDAD ==========
function abrirModalUnidad() {
    document.getElementById('modalUnidad').style.display = 'flex';
    document.getElementById('nombreUnidad').focus();
}

function cerrarModalUnidad() {
    document.getElementById('modalUnidad').style.display = 'none';
    document.getElementById('formNuevaUnidad').reset();
}

// ========== FUNCIONES PARA CARGAR PLANTILLA ==========
function abrirModalPlantilla() {
    var plantillasData = <?php echo json_encode($plantillas_usuario ?? []); ?>;
    
    if (plantillasData.length === 0) {
        Swal.fire('Sin plantillas', 'No tienes plantillas creadas. Ve a Plantillas para crear una.', 'info');
        return;
    }
    
    var optionsHtml = '<div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 350px; overflow-y: auto; text-align: left;">';
    plantillasData.forEach(function(p) {
        optionsHtml += '<label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.15s;" ' +
            'onmouseover="this.style.borderColor=\'#3b82f6\'; this.style.background=\'#f0f7ff\'" ' +
            'onmouseout="if(!this.querySelector(\'input\').checked){this.style.borderColor=\'#e5e7eb\'; this.style.background=\'white\'}">' +
            '<input type="radio" name="plantilla_sel" value="' + p.id + '" style="accent-color: #3b82f6;">' +
            '<div style="flex: 1; min-width: 0;">' +
            '<div style="font-weight: 600; color: #1f2937;">' + p.nombre + '</div>' +
            '<div style="font-size: 0.75rem; color: #6b7280;">' + p.total_productos + ' producto(s)' + (p.descripcion ? ' - ' + p.descripcion : '') + '</div>' +
            '</div></label>';
    });
    optionsHtml += '</div>';
    
    Swal.fire({
        title: 'Cargar Plantilla',
        html: '<p style="color: #6b7280; font-size: 0.8125rem; margin-bottom: 0.75rem;">Selecciona una plantilla para cargar sus productos:</p>' + optionsHtml,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-download"></i> Cargar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3b82f6',
        width: '480px',
        preConfirm: function() {
            var selected = document.querySelector('input[name="plantilla_sel"]:checked');
            if (!selected) {
                Swal.showValidationMessage('Selecciona una plantilla');
                return false;
            }
            return selected.value;
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            cargarProductosDePlantilla(result.value);
        }
    });
}

function cargarProductosDePlantilla(plantillaId) {
    Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
    
    fetch('api/obtener-plantilla.php?id=' + plantillaId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            Swal.close();
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
                return;
            }
            
            var productos = data.productos;
            if (!productos || productos.length === 0) {
                Swal.fire('Vacio', 'Esta plantilla no tiene productos.', 'info');
                return;
            }
            
            // Clear existing empty cards
            var container = document.getElementById('productos-container');
            container.innerHTML = '';
            contadorProductos = 0;
            
            productos.forEach(function(prod) {
                contadorProductos++;
                var idx = contadorProductos;
                var esNuevo = !prod.producto_id;
                var nuevo = document.createElement('div');
                nuevo.className = 'producto-item';
                nuevo.setAttribute('data-index', idx);
                nuevo.setAttribute('data-modo', esNuevo ? 'nuevo' : 'existente');
                nuevo.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;';
                
                var unidadOptions = '<option value="">Seleccionar...</option>';
                var unidadesLista = <?php echo json_encode($unidades_lista); ?>;
                unidadesLista.forEach(function(u) {
                    var selected = (u === prod.unidad) ? 'selected' : '';
                    unidadOptions += '<option value="' + u + '" ' + selected + '>' + u.charAt(0).toUpperCase() + u.slice(1) + '</option>';
                });
                
                var productosOptions = '';
                <?php foreach ($datosFormulario['productos'] as $p): ?>
                productosOptions += '<option value="<?php echo htmlspecialchars($p['nombre']); ?>" data-id="<?php echo $p['id']; ?>" data-sub="<?php echo htmlspecialchars($p['sub_almacen_nombre']); ?>" data-unidad="<?php echo htmlspecialchars($p['unidad'] ?? ''); ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>';
                <?php endforeach; ?>
                
                nuevo.innerHTML = 
                    '<button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer;" onmouseover="this.style.background=\'#dc2626\'" onmouseout="this.style.background=\'#ef4444\'"><i class="fas fa-times"></i></button>' +
                    '<div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">' +
                        '<div id="toggle-wrap-' + idx + '" style="display: inline-flex; background: var(--gray-100); border-radius: 8px; overflow: hidden; border: 1px solid var(--gray-200);">' +
                            '<button type="button" id="btn-existente-' + idx + '" onclick="toggleModoProducto(' + idx + ', \'existente\')" style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: ' + (esNuevo ? 'transparent' : 'var(--primary)') + '; color: ' + (esNuevo ? 'var(--gray-500)' : 'white') + ';"><i class="fas fa-search"></i> Del inventario</button>' +
                            '<button type="button" id="btn-nuevo-' + idx + '" onclick="toggleModoProducto(' + idx + ', \'nuevo\')" style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: ' + (esNuevo ? 'var(--primary)' : 'transparent') + '; color: ' + (esNuevo ? 'white' : 'var(--gray-500)') + ';"><i class="fas fa-plus"></i> Producto nuevo</button>' +
                        '</div>' +
                    '</div>' +
                    '<div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">' +
                        '<div class="form-group" style="margin-bottom: 0;">' +
                            '<label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;"><i class="fas fa-box"></i> Producto <span style="color: #ef4444;">*</span></label>' +
                            '<div id="modo-existente-' + idx + '" style="display: ' + (esNuevo ? 'none' : 'block') + ';">' +
                                '<input list="productos-list-' + idx + '" id="productos_search_' + idx + '" placeholder="Escriba para buscar..." onchange="selectProducto(' + idx + ')" style="width: 100%;" ' + (esNuevo ? '' : 'required') + ' value="' + (prod.nombre_producto || '') + '">' +
                                '<datalist id="productos-list-' + idx + '">' + productosOptions + '</datalist>' +
                                '<input type="hidden" id="producto_id_' + idx + '" value="' + (prod.producto_id || '') + '">' +
                            '</div>' +
                            '<div id="modo-nuevo-' + idx + '" style="display: ' + (esNuevo ? 'block' : 'none') + ';">' +
                                '<input type="text" id="producto_nombre_input_' + idx + '" placeholder="Nombre del producto nuevo" style="width: 100%;" value="' + (prod.nombre_custom || '') + '" ' + (esNuevo ? 'required' : '') + '>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group" style="margin-bottom: 0;">' +
                            '<label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;"><i class="fas fa-hashtag"></i> Cantidad <span style="color: #ef4444;">*</span></label>' +
                            '<input type="number" class="campo-cantidad" min="1" placeholder="0" value="' + prod.cantidad + '" style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>' +
                        '</div>' +
                        '<div class="form-group" style="margin-bottom: 0;">' +
                            '<label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;"><i class="fas fa-ruler"></i> Unidad <span style="color: #ef4444;">*</span></label>' +
                            '<div style="display: flex; gap: 8px; align-items: center;"><select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827;">' + unidadOptions + '</select>' +
                            '<button type="button" onclick="abrirModalUnidad()" title="Agregar nueva unidad" style="background: #10b981; color: white; border: none; padding: 10px 14px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: all 0.2s ease; white-space: nowrap;" onmouseover="this.style.background=\'#059669\'" onmouseout="this.style.background=\'#10b981\'"><i class="fas fa-plus"></i></button></div>' +
                        '</div>' +
                    '</div>';
                
                container.appendChild(nuevo);
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Plantilla cargada',
                text: productos.length + ' producto(s) agregados.',
                timer: 1500,
                showConfirmButton: false
            });
        })
        .catch(function(err) {
            Swal.close();
            Swal.fire('Error', 'No se pudo cargar la plantilla.', 'error');
        });
}

// Auto-cargar plantilla si viene por URL
<?php if ($plantilla_precarga && !empty($plantilla_precarga['productos'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        cargarProductosDePlantilla(<?php echo intval($plantilla_precarga['id']); ?>);
    }, 300);
});
<?php endif; ?>
</script>

<!-- Modal para agregar nueva unidad -->
<div id="modalUnidad" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-ruler" style="color: #10b981;"></i>
            Agregar Nueva Unidad
        </h3>
        <form id="formNuevaUnidad">
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #374151; font-weight: 600; margin-bottom: 8px;">
                    Nombre de la Unidad <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="nombreUnidad" required 
                       placeholder="Ej: kilogramo, metro, litro" 
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background: #10b981; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#059669'"
                        onmouseout="this.style.background='#10b981'">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <button type="button" onclick="cerrarModalUnidad()" 
                        style="flex: 1; background: #6b7280; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 14px; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#4b5563'"
                        onmouseout="this.style.background='#6b7280'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Inicializar eventos del modal de unidad después de que el DOM esté listo
document.getElementById('formNuevaUnidad').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const nombreUnidad = document.getElementById('nombreUnidad').value.trim();
    
    if (!nombreUnidad) {
        alertaError('Ingrese el nombre de la unidad');
        return;
    }
    
    // Verificar si ya existe en algún select
    const primerSelect = document.querySelector('.unidad-select');
    let existe = false;
    if (primerSelect) {
        for (let option of primerSelect.options) {
            if (option.value.toLowerCase() === nombreUnidad.toLowerCase()) {
                existe = true;
                break;
            }
        }
    }
    
    if (existe) {
        Swal.fire({
            icon: 'info',
            title: 'Unidad existente',
            text: 'Esta unidad ya existe en la lista'
        });
        cerrarModalUnidad();
        return;
    }
    
    // Guardar en la base de datos via AJAX
    fetch('agregar-unidad.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'nombre=' + encodeURIComponent(nombreUnidad)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Agregar a todos los selects de unidad
            document.querySelectorAll('.unidad-select').forEach(select => {
                const option = document.createElement('option');
                option.value = data.unidad_nombre;
                option.textContent = data.unidad_nombre.charAt(0).toUpperCase() + data.unidad_nombre.slice(1);
                option.selected = true;
                select.appendChild(option);
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Unidad agregada',
                text: 'La unidad "' + data.unidad_nombre + '" se guardó correctamente',
                timer: 2000,
                showConfirmButton: false
            });
            
            cerrarModalUnidad();
        } else {
            alertaError(data.message || 'Error al guardar la unidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertaError('Error de conexión al guardar la unidad');
    });
});

// Cerrar modal al hacer clic fuera
document.getElementById('modalUnidad').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalUnidad();
    }
});
</script>

<?php require 'includes/footer.php'; ?>
