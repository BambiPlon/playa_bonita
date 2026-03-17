<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">
                <?php echo $modoEdicion ? 'Editar Plantilla' : 'Nueva Plantilla'; ?>
            </h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">
                <?php echo $modoEdicion ? 'Modifica los productos de tu plantilla' : 'Define un conjunto de productos reutilizable para tus requisiciones'; ?>
            </p>
        </div>
        <a href="plantillas.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="animation: slideDown 0.3s ease-out; margin-bottom: 1rem;">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>" style="margin-right: 0.5rem;"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="formPlantilla">
        <!-- Datos de la plantilla -->
        <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1.25rem;">
            <div style="padding: 1rem 1.5rem; background: var(--gray-50); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt" style="color: var(--primary);"></i>
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--gray-900);">Datos de la Plantilla</h2>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.25rem;">
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                            <i class="fas fa-tag" style="color: var(--primary); font-size: 0.75rem;"></i>
                            Nombre <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nombre_plantilla" required placeholder="Ej: Limpieza mensual, Papeleria..." 
                               value="<?php echo $modoEdicion ? htmlspecialchars($plantilla['nombre']) : ''; ?>"
                               style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                            <i class="fas fa-align-left" style="color: var(--primary); font-size: 0.75rem;"></i>
                            Descripcion
                        </label>
                        <input type="text" name="descripcion_plantilla" placeholder="Descripcion breve (opcional)" 
                               value="<?php echo $modoEdicion ? htmlspecialchars($plantilla['descripcion'] ?? '') : ''; ?>"
                               style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1.25rem;">
            <div style="padding: 1rem 1.5rem; background: var(--gray-50); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-boxes" style="color: var(--primary);"></i>
                    Productos de la Plantilla
                </h2>
                <button type="button" onclick="agregarProductoPlantilla()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Agregar Producto
                </button>
            </div>
            <div style="padding: 1.5rem;">
                <div id="productos-container" style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if ($modoEdicion && !empty($plantilla_productos)): ?>
                        <?php foreach ($plantilla_productos as $idx => $pp): ?>
                            <div class="producto-item" data-index="<?php echo $idx; ?>" data-modo="<?php echo $pp['producto_id'] ? 'existente' : 'nuevo'; ?>" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;">
                                <button type="button" onclick="this.parentElement.remove()" 
                                        style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer;"
                                        onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                    <div id="toggle-wrap-<?php echo $idx; ?>" style="display: inline-flex; background: var(--gray-100); border-radius: 8px; overflow: hidden; border: 1px solid var(--gray-200);">
                                        <button type="button" id="btn-existente-<?php echo $idx; ?>" onclick="toggleModoProducto(<?php echo $idx; ?>, 'existente')"
                                                style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: <?php echo $pp['producto_id'] ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo $pp['producto_id'] ? 'white' : 'var(--gray-500)'; ?>;">
                                            <i class="fas fa-search"></i> Del inventario
                                        </button>
                                        <button type="button" id="btn-nuevo-<?php echo $idx; ?>" onclick="toggleModoProducto(<?php echo $idx; ?>, 'nuevo')"
                                                style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; background: <?php echo !$pp['producto_id'] ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo !$pp['producto_id'] ? 'white' : 'var(--gray-500)'; ?>;">
                                            <i class="fas fa-plus"></i> Producto nuevo
                                        </button>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                            <i class="fas fa-box"></i> Producto <span style="color: #ef4444;">*</span>
                                        </label>
                                        <div id="modo-existente-<?php echo $idx; ?>" style="display: <?php echo $pp['producto_id'] ? 'block' : 'none'; ?>;">
                                            <input list="productos-list-<?php echo $idx; ?>" id="productos_search_<?php echo $idx; ?>" 
                                                   placeholder="Escriba para buscar..." onchange="selectProducto(<?php echo $idx; ?>)" 
                                                   style="width: 100%;" value="<?php echo $pp['producto_id'] ? htmlspecialchars($pp['nombre_producto']) : ''; ?>" <?php echo $pp['producto_id'] ? 'required' : ''; ?>>
                                            <datalist id="productos-list-<?php echo $idx; ?>">
                                                <?php foreach ($productos_inventario as $prod): ?>
                                                    <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                                            data-id="<?php echo $prod['id']; ?>"
                                                            data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre'] ?? ''); ?>">
                                                        <?php echo htmlspecialchars($prod['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </datalist>
                                            <input type="hidden" id="producto_id_<?php echo $idx; ?>" value="<?php echo $pp['producto_id'] ?? ''; ?>">
                                        </div>
                                        <div id="modo-nuevo-<?php echo $idx; ?>" style="display: <?php echo !$pp['producto_id'] ? 'block' : 'none'; ?>;">
                                            <input type="text" id="producto_nombre_input_<?php echo $idx; ?>" 
                                                   placeholder="Nombre del producto nuevo" 
                                                   value="<?php echo !$pp['producto_id'] ? htmlspecialchars($pp['nombre_custom'] ?? '') : ''; ?>"
                                                   style="width: 100%;" <?php echo !$pp['producto_id'] ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                            <i class="fas fa-hashtag"></i> Cantidad <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="number" class="campo-cantidad" min="1" placeholder="0" value="<?php echo intval($pp['cantidad']); ?>"
                                               style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                            <i class="fas fa-ruler"></i> Unidad <span style="color: #ef4444;">*</span>
                                        </label>
                                        <select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827; width: 100%;">
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($unidades_lista as $unidad): ?>
                                                <option value="<?php echo htmlspecialchars($unidad); ?>" <?php echo ($pp['unidad'] === $unidad) ? 'selected' : ''; ?>>
                                                    <?php echo ucfirst(htmlspecialchars($unidad)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Card de producto vacio por defecto -->
                        <div class="producto-item" data-index="0" data-modo="existente" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;">
                            <button type="button" onclick="this.parentElement.remove()" 
                                    style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer;"
                                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                <i class="fas fa-times"></i>
                            </button>

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
                                        <i class="fas fa-box"></i> Producto <span style="color: #ef4444;">*</span>
                                    </label>
                                    <div id="modo-existente-0">
                                        <input list="productos-list-0" id="productos_search_0" 
                                               placeholder="Escriba para buscar..." onchange="selectProducto(0)" 
                                               style="width: 100%;" required>
                                        <datalist id="productos-list-0">
                                            <?php foreach ($productos_inventario as $prod): ?>
                                                <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                                        data-id="<?php echo $prod['id']; ?>"
                                                        data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($prod['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </datalist>
                                        <input type="hidden" id="producto_id_0">
                                    </div>
                                    <div id="modo-nuevo-0" style="display: none;">
                                        <input type="text" id="producto_nombre_input_0" 
                                               placeholder="Nombre del producto nuevo" 
                                               style="width: 100%;">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                        <i class="fas fa-hashtag"></i> Cantidad <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="number" class="campo-cantidad" min="1" placeholder="0" 
                                           style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                        <i class="fas fa-ruler"></i> Unidad <span style="color: #ef4444;">*</span>
                                    </label>
                                    <select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827; width: 100%;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($unidades_lista as $unidad): ?>
                                            <option value="<?php echo htmlspecialchars($unidad); ?>"><?php echo ucfirst(htmlspecialchars($unidad)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed rgba(37, 99, 235, 0.2); text-align: center;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.375rem;"></i>
                        Agrega los productos que usas frecuentemente en tus requisiciones
                    </p>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="plantillas.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="button" onclick="guardarPlantilla()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-save"></i> <?php echo $modoEdicion ? 'Guardar Cambios' : 'Crear Plantilla'; ?>
            </button>
        </div>
    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let contadorProductos = <?php echo $modoEdicion ? count($plantilla_productos) : 1; ?>;

function agregarProductoPlantilla() {
    contadorProductos++;
    var idx = contadorProductos;
    var container = document.getElementById('productos-container');
    var nuevo = document.createElement('div');
    nuevo.className = 'producto-item';
    nuevo.setAttribute('data-index', idx);
    nuevo.setAttribute('data-modo', 'existente');
    nuevo.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;';
    nuevo.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer;"
                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
            <i class="fas fa-times"></i>
        </button>
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
                    <i class="fas fa-box"></i> Producto <span style="color: #ef4444;">*</span>
                </label>
                <div id="modo-existente-${idx}">
                    <input list="productos-list-${idx}" id="productos_search_${idx}" 
                           placeholder="Escriba para buscar..." onchange="selectProducto(${idx})" 
                           style="width: 100%;" required>
                    <datalist id="productos-list-${idx}">
                        <?php foreach ($productos_inventario as $prod): ?>
                            <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                    data-id="<?php echo $prod['id']; ?>"
                                    data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre'] ?? ''); ?>">
                                <?php echo htmlspecialchars($prod['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                    <input type="hidden" id="producto_id_${idx}">
                </div>
                <div id="modo-nuevo-${idx}" style="display: none;">
                    <input type="text" id="producto_nombre_input_${idx}" 
                           placeholder="Nombre del producto nuevo" 
                           style="width: 100%;">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-hashtag"></i> Cantidad <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" class="campo-cantidad" min="1" placeholder="0" 
                       style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-ruler"></i> Unidad <span style="color: #ef4444;">*</span>
                </label>
                <select class="unidad-select campo-unidad" required style="flex: 1; background: white; border: 1px solid #d1d5db; color: #111827; width: 100%;">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($unidades_lista as $unidad): ?>
                        <option value="<?php echo htmlspecialchars($unidad); ?>"><?php echo ucfirst(htmlspecialchars($unidad)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    `;
    container.appendChild(nuevo);
}

function selectProducto(index) {
    var searchInput = document.getElementById('productos_search_' + index);
    var hiddenInput = document.getElementById('producto_id_' + index);
    var datalist = document.getElementById('productos-list-' + index);
    
    if (searchInput && datalist) {
        var options = datalist.querySelectorAll('option');
        var found = false;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === searchInput.value) {
                hiddenInput.value = options[i].getAttribute('data-id');
                found = true;
                break;
            }
        }
        if (!found) {
            hiddenInput.value = '';
        }
    }
}

function toggleModoProducto(index, modo) {
    var btnExistente = document.getElementById('btn-existente-' + index);
    var btnNuevo = document.getElementById('btn-nuevo-' + index);
    var modoExistente = document.getElementById('modo-existente-' + index);
    var modoNuevo = document.getElementById('modo-nuevo-' + index);
    var card = modoExistente.closest('.producto-item');
    
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
        var searchInput = modoExistente.querySelector('input[list]');
        if (searchInput) searchInput.required = true;
    }
}

function guardarPlantilla() {
    var cards = document.querySelectorAll('#productos-container .producto-item');
    if (cards.length === 0) {
        Swal.fire('Error', 'Agrega al menos un producto a la plantilla.', 'error');
        return;
    }
    
    var form = document.getElementById('formPlantilla');
    
    // Remove previous dynamic inputs
    form.querySelectorAll('.dynamic-plantilla-data').forEach(function(el) { el.remove(); });
    
    var valid = true;
    cards.forEach(function(card) {
        var idx = card.getAttribute('data-index');
        var modo = card.getAttribute('data-modo') || 'existente';
        var productoId, productoNombre;
        
        if (modo === 'nuevo') {
            productoId = 'otro';
            var nameInput = document.getElementById('producto_nombre_input_' + idx);
            productoNombre = nameInput ? nameInput.value : '';
            if (!productoNombre.trim()) valid = false;
        } else {
            var hiddenId = document.getElementById('producto_id_' + idx);
            productoId = hiddenId ? hiddenId.value : '';
            productoNombre = '';
            if (!productoId) valid = false;
        }
        
        var cantidad = card.querySelector('.campo-cantidad');
        var unidad = card.querySelector('.campo-unidad');
        
        if (!cantidad || !cantidad.value || parseInt(cantidad.value) < 1) valid = false;
        if (!unidad || !unidad.value) valid = false;
        
        // Create hidden inputs
        var h1 = document.createElement('input');
        h1.type = 'hidden'; h1.name = 'productos[]'; h1.value = productoId; h1.className = 'dynamic-plantilla-data';
        form.appendChild(h1);
        
        var h2 = document.createElement('input');
        h2.type = 'hidden'; h2.name = 'productos_nombre_custom[]'; h2.value = productoNombre; h2.className = 'dynamic-plantilla-data';
        form.appendChild(h2);
        
        var h3 = document.createElement('input');
        h3.type = 'hidden'; h3.name = 'cantidades[]'; h3.value = cantidad ? cantidad.value : '0'; h3.className = 'dynamic-plantilla-data';
        form.appendChild(h3);
        
        var h4 = document.createElement('input');
        h4.type = 'hidden'; h4.name = 'unidades[]'; h4.value = unidad ? unidad.value : ''; h4.className = 'dynamic-plantilla-data';
        form.appendChild(h4);
    });
    
    if (!valid) {
        Swal.fire('Campos incompletos', 'Verifica que todos los productos tengan nombre, cantidad y unidad.', 'warning');
        return;
    }
    
    form.submit();
}
</script>
