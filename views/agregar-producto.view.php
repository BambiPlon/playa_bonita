<?php require 'includes/header.php'; ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Agregar Producto</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Registra un nuevo producto al inventario</p>
        </div>
        <a href="index.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; <?php echo $tipo_mensaje === 'success' ? 'background: var(--success-light); border: 1px solid #bbf7d0; color: #166534;' : 'background: var(--danger-light); border: 1px solid #fecaca; color: #991b1b;'; ?>">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>" style="font-size: 1.125rem;"></i>
            <span style="font-weight: 500; font-size: 0.875rem;"><?php echo htmlspecialchars($mensaje); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="agregar-producto.php">
            <input type="hidden" name="sub_almacen_id" value="100">
            <input type="hidden" name="cantidad" value="0">
            <input type="hidden" name="stock_minimo" value="0">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div style="grid-column: span 2;">
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-box" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre del Producto <span style="color: var(--danger);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="text" id="nombre" name="nombre" required 
                               placeholder="Escribe el nombre del producto..." 
                               autocomplete="off"
                               class="form-input">
                        <div id="nombre-autocomplete" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid var(--gray-200); border-top: none; border-radius: 0 0 10px 10px; max-height: 280px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 8px 20px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>
                
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-barcode" style="color: var(--primary); font-size: 0.75rem;"></i> Codigo <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" id="codigo" name="codigo" required readonly
                           placeholder="Se genera automaticamente" 
                           class="form-input" style="background: var(--gray-50); cursor: not-allowed;">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.6875rem;">
                        <i class="fas fa-info-circle"></i> Se genera automaticamente
                    </small>
                </div>
                
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-ruler" style="color: var(--primary); font-size: 0.75rem;"></i> Unidad <span style="color: var(--danger);">*</span>
                    </label>
                    <?php
                    $conn_u = getConnection();
                    $unidades_agregar = [];
                    $q_u = $conn_u->query("SELECT nombre FROM unidades WHERE activo = 1 ORDER BY nombre");
                    if ($q_u) { while ($r_u = $q_u->fetch_assoc()) { $unidades_agregar[] = $r_u['nombre']; } }
                    if (empty($unidades_agregar)) { $unidades_agregar = ['pieza', 'unidad', 'caja', 'paquete', 'bolsa', 'rollo', 'metro', 'litro', 'kilogramo', 'gramo', 'juego', 'par', 'docena', 'cubeta', 'bote', 'botella', 'servicio']; }
                    ?>
                    <select id="unidad" name="unidad" required class="form-input">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($unidades_agregar as $u_item): ?>
                            <option value="<?php echo htmlspecialchars($u_item); ?>"><?php echo ucfirst(htmlspecialchars($u_item)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-dollar-sign" style="color: var(--primary); font-size: 0.75rem;"></i> Precio Unitario
                    </label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" placeholder="0.00" class="form-input">
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 8px; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Agregar Producto
                </button>
            </div>
        </form>
    </div>
</main>

<?php require 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    const codigoInput = document.getElementById('codigo');
    const autocompleteDiv = document.getElementById('nombre-autocomplete');
    const unidadInput = document.getElementById('unidad');
    const precioInput = document.getElementById('precio_unitario');
    
    function generarCodigoAutomatico() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var result = 'ALM-100-';
        for (var i = 0; i < 6; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }
    
    let timeoutId = null;
    let productoSeleccionado = null;
    
    nombreInput.addEventListener('input', function() {
        if (timeoutId) clearTimeout(timeoutId);
        const nombre = this.value.trim();
        
        if (nombre.length === 0) {
            autocompleteDiv.style.display = 'none';
            autocompleteDiv.innerHTML = '';
            productoSeleccionado = null;
            codigoInput.value = '';
            codigoInput.readOnly = true;
            codigoInput.style.background = 'var(--gray-50)';
            nombreInput.style.borderColor = 'var(--gray-200)';
            return;
        }
        if (nombre.length < 2) { autocompleteDiv.style.display = 'none'; return; }
        
        autocompleteDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
        autocompleteDiv.style.display = 'block';
        
        timeoutId = setTimeout(() => {
            fetch(`api/buscar-producto-nombre.php?nombre=${encodeURIComponent(nombre)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.productos.length > 0) mostrarAutocomplete(data.productos);
                    else mostrarCrearNuevo();
                })
                .catch(() => {
                    autocompleteDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Error al buscar</div>';
                });
        }, 300);
    });
    
    function mostrarAutocomplete(productos) {
        autocompleteDiv.innerHTML = '';
        productos.forEach(p => {
            const item = document.createElement('div');
            item.style.cssText = 'padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--gray-100); transition: background 0.15s;';
            item.innerHTML = `<div style="font-weight: 600; color: var(--gray-900); font-size: 0.8125rem;">${p.nombre} <span style="background: #e0f2fe; color: #0369a1; padding: 1px 6px; border-radius: 4px; font-size: 0.6875rem; font-weight: 700; margin-left: 4px;">${p.unidad || 'sin unidad'}</span></div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">
                    <span style="background: var(--primary-light); color: var(--primary); padding: 2px 6px; border-radius: 4px; margin-right: 6px; font-weight: 600;">${p.codigo}</span>
                    Stock: ${p.cantidad_total} ${p.unidad} ${p.almacenes_count > 1 ? `<span style="color: var(--warning);">| ${p.almacenes_count} almacenes</span>` : ''}
                </div>`;
            item.addEventListener('mouseenter', () => item.style.background = 'var(--gray-50)');
            item.addEventListener('mouseleave', () => item.style.background = 'white');
            item.addEventListener('click', () => seleccionarProducto(p));
            autocompleteDiv.appendChild(item);
        });
        const nuevoItem = document.createElement('div');
        nuevoItem.style.cssText = 'padding: 10px 14px; cursor: pointer; background: var(--primary-light); color: var(--primary); font-weight: 600; font-size: 0.8125rem; border-top: 2px solid var(--gray-100);';
        nuevoItem.innerHTML = '<i class="fas fa-plus-circle"></i> Crear nuevo producto con este nombre';
        nuevoItem.addEventListener('click', crearNuevoProducto);
        autocompleteDiv.appendChild(nuevoItem);
        autocompleteDiv.style.display = 'block';
    }
    
    function mostrarCrearNuevo() {
        autocompleteDiv.innerHTML = '<div style="padding: 10px 14px; color: var(--text-muted); text-align: center; font-size: 0.8125rem;"><i class="fas fa-info-circle"></i> No se encontraron productos</div>';
        const nuevoItem = document.createElement('div');
        nuevoItem.style.cssText = 'padding: 10px 14px; cursor: pointer; background: var(--primary-light); color: var(--primary); font-weight: 600; text-align: center; font-size: 0.8125rem;';
        nuevoItem.innerHTML = '<i class="fas fa-plus-circle"></i> Crear nuevo producto';
        nuevoItem.addEventListener('click', crearNuevoProducto);
        autocompleteDiv.appendChild(nuevoItem);
        autocompleteDiv.style.display = 'block';
    }
    
    function seleccionarProducto(p) {
        productoSeleccionado = p;
        nombreInput.value = p.nombre;
        codigoInput.value = p.codigo;
        codigoInput.readOnly = true;
        codigoInput.style.background = 'var(--gray-50)';
        // Establecer unidad en el select
        if (p.unidad) {
            var unidadEncontrada = false;
            for (var i = 0; i < unidadInput.options.length; i++) {
                if (unidadInput.options[i].value.toLowerCase() === p.unidad.toLowerCase()) {
                    unidadInput.selectedIndex = i;
                    unidadEncontrada = true;
                    break;
                }
            }
        }
        precioInput.value = p.precio_unitario || '';
        autocompleteDiv.style.display = 'none';
        nombreInput.style.borderColor = 'var(--success)';
        
        // Quitar aviso previo si habia
        var avisoAnterior = document.getElementById('aviso-unidad-diferente');
        if (avisoAnterior) avisoAnterior.remove();
    }
    
    // Detectar cambio de unidad tras seleccionar un producto existente
    unidadInput.addEventListener('change', function() {
        var avisoAnterior = document.getElementById('aviso-unidad-diferente');
        if (avisoAnterior) avisoAnterior.remove();
        
        if (productoSeleccionado && productoSeleccionado.unidad) {
            var unidadOriginal = productoSeleccionado.unidad.toLowerCase();
            var unidadNueva = this.value.toLowerCase();
            
            if (unidadNueva && unidadNueva !== unidadOriginal) {
                // Generar nuevo codigo porque sera un producto separado
                codigoInput.value = generarCodigoAutomatico();
                
                var aviso = document.createElement('div');
                aviso.id = 'aviso-unidad-diferente';
                aviso.style.cssText = 'display: flex; align-items: center; gap: 8px; padding: 0.625rem 1rem; border-radius: 8px; margin-bottom: 1rem; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; font-size: 0.75rem; font-weight: 500;';
                aviso.innerHTML = '<i class="fas fa-info-circle"></i> Se creara un nuevo registro: <strong>"' + productoSeleccionado.nombre + ' (' + this.value + ')"</strong> separado de <strong>"' + productoSeleccionado.nombre + ' (' + productoSeleccionado.unidad + ')"</strong>';
                document.querySelector('.card form').insertBefore(aviso, document.querySelector('.card form').firstChild);
            } else if (unidadNueva === unidadOriginal) {
                // Restaurar codigo original
                codigoInput.value = productoSeleccionado.codigo;
            }
        }
    });
    
    function crearNuevoProducto() {
        productoSeleccionado = null;
        autocompleteDiv.style.display = 'none';
        codigoInput.value = generarCodigoAutomatico();
        codigoInput.readOnly = true;
        codigoInput.style.background = 'var(--gray-50)';
        nombreInput.style.borderColor = 'var(--primary)';
        unidadInput.selectedIndex = 0;
        precioInput.value = '';
    }
    
    document.addEventListener('click', function(e) {
        if (!nombreInput.contains(e.target) && !autocompleteDiv.contains(e.target)) autocompleteDiv.style.display = 'none';
    });
    
    document.querySelector('form').addEventListener('submit', function(e) {
        // Si no hay codigo pero si hay nombre, generar codigo automaticamente
        if (!codigoInput.value && nombreInput.value.trim() !== '') {
            codigoInput.value = generarCodigoAutomatico();
        }
        if (!codigoInput.value) {
            e.preventDefault();
            alert('Error: Escribe un nombre de producto.');
        }
    });
});
</script>
