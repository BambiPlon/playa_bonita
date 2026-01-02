<?php require 'includes/header.php'; ?>

<main class="main-content" style="width: 100%; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); min-height: 100vh;">
    <div style="background: white; color: #1f2937; padding: 25px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(41, 98, 255, 0.2);">
        <h1 style="margin: 0; font-size: 28px; display: flex; align-items: center; gap: 12px;">
            <div style="background: rgba(41, 98, 255, 0.1); padding: 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-plus-circle" style="color: #2962FF; font-size: 24px;"></i>
            </div>
            Agregar Producto al Inventario
        </h1>
        <a href="index.php" class="btn btn-secondary" style="background: rgba(41, 98, 255, 0.1); color: #2962FF; border: 2px solid #2962FF; padding: 10px 20px; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; <?php echo $tipo_mensaje === 'success' ? 'background: rgba(41, 98, 255, 0.1); border: 1px solid #2962FF; color: #2962FF;' : 'background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444;'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px; border: 1px solid rgba(41, 98, 255, 0.2);">
        <form method="POST" action="agregar-producto.php">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <!-- Cambiando el campo de nombre para que sea autocomplete en lugar de código -->
                <div class="form-group">
                    <label for="nombre" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-box" style="color: #2962FF;"></i> Nombre del Producto: *
                    </label>
                    <div style="position: relative;">
                        <input type="text" id="nombre" name="nombre" required 
                               placeholder="Escribe el nombre del producto..." 
                               autocomplete="off"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                        <div id="nombre-autocomplete" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-top: none; border-radius: 0 0 8px 8px; max-height: 300px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="codigo" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-barcode" style="color: #2962FF;"></i> Código: *
                    </label>
                    <input type="text" id="codigo" name="codigo" required readonly
                           placeholder="Se generará automáticamente" 
                           style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #f3f4f6; color: #1f2937; cursor: not-allowed;">
                    <small style="color: #6b7280; display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> El código se auto-completa al seleccionar un producto existente
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="sub_almacen_id" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-warehouse" style="color: #2962FF;"></i> Sub-Almacén: *
                    </label>
                    <?php if ($usuarioData['rol'] === 'compras'): ?>
                        <!-- Compras: mostrar Almacén General -->
                        <input type="text" value="Almacén General" readonly 
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #f3f4f6; color: #1f2937; cursor: not-allowed;">
                        <input type="hidden" name="sub_almacen_id" value="100">
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Los productos se agregarán al Almacén General
                        </small>
                    <?php elseif ($puede_seleccionar): ?>
                        <!-- Admin/Gerencia: select para elegir sub-almacén -->
                        <select id="sub_almacen_id" name="sub_almacen_id" required 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($sub_almacenes as $almacen): ?>
                                <option value="<?php echo $almacen['id']; ?>">
                                    <?php echo htmlspecialchars($almacen['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Selecciona el sub-almacén donde se agregará el producto
                        </small>
                    <?php else: ?>
                        <!-- Usuario departamental: mostrar su sub-almacén asignado -->
                        <?php 
                        $texto_almacen = !empty($usuarioData['sub_almacen_nombre']) 
                            ? $usuarioData['sub_almacen_nombre'] 
                            : (!empty($usuarioData['sub_almacen_id']) 
                                ? 'Sub-almacén ID: ' . $usuarioData['sub_almacen_id'] 
                                : 'No asignado');
                        ?>
                        <input type="text" value="<?php echo htmlspecialchars($texto_almacen); ?>" readonly 
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #f3f4f6; color: #1f2937; cursor: not-allowed;">
                        <input type="hidden" name="sub_almacen_id" value="<?php echo $usuarioData['sub_almacen_id'] ?? ''; ?>">
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Los productos se agregarán a tu sub-almacén asignado
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="cantidad" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-hashtag" style="color: #2962FF;"></i> Cantidad Inicial: *
                    </label>
                    <input type="number" id="cantidad" name="cantidad" min="0" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
                
                <div class="form-group">
                    <label for="unidad" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-ruler" style="color: #2962FF;"></i> Unidad: *
                    </label>
                    <input type="text" id="unidad" name="unidad" required 
                           placeholder="Ej: pieza, litro, caja" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="precio_unitario" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-dollar-sign" style="color: #2962FF;"></i> Precio Unitario:
                    </label>
                    <input type="number" id="precio_unitario" name="precio_unitario" 
                           step="0.01" min="0" placeholder="0.00" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
                
                <div class="form-group">
                    <label for="stock_minimo" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-exclamation-triangle" style="color: #2962FF;"></i> Stock Mínimo:
                    </label>
                    <input type="number" id="stock_minimo" name="stock_minimo" 
                           min="0" value="10" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="descripcion" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                    <i class="fas fa-align-left" style="color: #2962FF;"></i> Descripción:
                </label>
                <textarea id="descripcion" name="descripcion" rows="3" 
                          placeholder="Descripción del producto..." style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical; background: white; color: #1f2937;"></textarea>
            </div>
            
            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="background: #2962FF; color: white; border: none; padding: 12px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 8px rgba(41, 98, 255, 0.3);">
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
    const descripcionInput = document.getElementById('descripcion');
    const unidadInput = document.getElementById('unidad');
    const precioInput = document.getElementById('precio_unitario');
    const stockMinimoInput = document.getElementById('stock_minimo');
    
    let timeoutId = null;
    let productoSeleccionado = null;
    
    nombreInput.addEventListener('input', function() {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        const nombre = this.value.trim();
        
        if (nombre.length === 0) {
            autocompleteDiv.style.display = 'none';
            autocompleteDiv.innerHTML = '';
            productoSeleccionado = null;
            codigoInput.value = '';
            codigoInput.readOnly = true;
            codigoInput.style.background = '#f3f4f6';
            codigoInput.placeholder = 'Se generará automáticamente';
            nombreInput.style.borderColor = '#d1d5db';
            nombreInput.style.borderWidth = '1px';
            return;
        }
        
        if (nombre.length < 2) {
            autocompleteDiv.style.display = 'none';
            autocompleteDiv.innerHTML = '';
            return;
        }
        
        autocompleteDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #6b7280;"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
        autocompleteDiv.style.display = 'block';
        
        timeoutId = setTimeout(() => {
            fetch(`api/buscar-producto-nombre.php?nombre=${encodeURIComponent(nombre)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.productos.length > 0) {
                        mostrarAutocomplete(data.productos);
                    } else {
                        mostrarCrearNuevo();
                    }
                })
                .catch(error => {
                    console.error('[v0] Error al buscar productos:', error);
                    autocompleteDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error al buscar</div>';
                });
        }, 300);
    });
    
    function mostrarAutocomplete(productos) {
        autocompleteDiv.innerHTML = '';
        
        productos.forEach(producto => {
            const item = document.createElement('div');
            item.style.cssText = 'padding: 12px; cursor: pointer; border-bottom: 1px solid #e5e7eb; transition: background 0.2s;';
            item.innerHTML = `
                <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">${producto.nombre}</div>
                <div style="font-size: 12px; color: #6b7280;">
                    <span style="background: rgba(41, 98, 255, 0.1); color: #2962FF; padding: 2px 8px; border-radius: 4px; margin-right: 8px;">${producto.codigo}</span>
                    <span>Stock total: ${producto.cantidad_total} ${producto.unidad}</span>
                    ${producto.almacenes_count > 1 ? ` <span style="color: #f59e0b;">• ${producto.almacenes_count} almacenes</span>` : ''}
                </div>
            `;
            
            item.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(41, 98, 255, 0.05)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.background = 'white';
            });
            
            item.addEventListener('click', function() {
                seleccionarProducto(producto);
            });
            
            autocompleteDiv.appendChild(item);
        });
        
        const nuevoItem = document.createElement('div');
        nuevoItem.style.cssText = 'padding: 12px; cursor: pointer; background: rgba(41, 98, 255, 0.05); color: #2962FF; font-weight: 600; border-top: 2px solid #e5e7eb;';
        nuevoItem.innerHTML = '<i class="fas fa-plus-circle"></i> Crear nuevo producto con este nombre';
        nuevoItem.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(41, 98, 255, 0.1)';
        });
        nuevoItem.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(41, 98, 255, 0.05)';
        });
        nuevoItem.addEventListener('click', function() {
            crearNuevoProducto();
        });
        autocompleteDiv.appendChild(nuevoItem);
        
        autocompleteDiv.style.display = 'block';
    }
    
    function mostrarCrearNuevo() {
        autocompleteDiv.innerHTML = '';
        
        const infoItem = document.createElement('div');
        infoItem.style.cssText = 'padding: 12px; color: #6b7280; text-align: center;';
        infoItem.innerHTML = '<i class="fas fa-info-circle"></i> No se encontraron productos con ese nombre';
        autocompleteDiv.appendChild(infoItem);
        
        const nuevoItem = document.createElement('div');
        nuevoItem.style.cssText = 'padding: 12px; cursor: pointer; background: rgba(41, 98, 255, 0.05); color: #2962FF; font-weight: 600; text-align: center;';
        nuevoItem.innerHTML = '<i class="fas fa-plus-circle"></i> Crear nuevo producto';
        nuevoItem.addEventListener('click', function() {
            crearNuevoProducto();
        });
        autocompleteDiv.appendChild(nuevoItem);
        
        autocompleteDiv.style.display = 'block';
    }
    
    function seleccionarProducto(producto) {
        productoSeleccionado = producto;
        nombreInput.value = producto.nombre;
        codigoInput.value = producto.codigo;
        codigoInput.readOnly = true;
        codigoInput.style.background = '#f3f4f6';
        codigoInput.style.cursor = 'not-allowed';
        descripcionInput.value = producto.descripcion || '';
        unidadInput.value = producto.unidad;
        precioInput.value = producto.precio_unitario || '';
        stockMinimoInput.value = producto.stock_minimo || '10';
        autocompleteDiv.style.display = 'none';
        
        nombreInput.style.borderColor = '#10b981';
        nombreInput.style.borderWidth = '2px';
        
        const tooltip = document.createElement('div');
        tooltip.style.cssText = 'position: absolute; top: -40px; left: 0; background: #10b981; color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; z-index: 1000;';
        tooltip.innerHTML = '<i class="fas fa-check-circle"></i> Producto existente seleccionado. Se usará el mismo código.';
        nombreInput.parentElement.style.position = 'relative';
        nombreInput.parentElement.appendChild(tooltip);
        
        setTimeout(() => {
            tooltip.remove();
        }, 3000);
    }
    
    function crearNuevoProducto() {
        productoSeleccionado = null;
        autocompleteDiv.style.display = 'none';
        codigoInput.value = '';
        codigoInput.readOnly = true;
        codigoInput.style.background = '#f3f4f6';
        codigoInput.placeholder = 'Se generará automáticamente';
        nombreInput.style.borderColor = '#2962FF';
        nombreInput.style.borderWidth = '2px';
        
        descripcionInput.value = '';
        unidadInput.value = '';
        precioInput.value = '';
        stockMinimoInput.value = '10';
    }
    
    document.addEventListener('click', function(e) {
        if (!nombreInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
            autocompleteDiv.style.display = 'none';
        }
    });
    
    document.querySelector('form').addEventListener('submit', function(e) {
        if (productoSeleccionado && !codigoInput.value) {
            e.preventDefault();
            alert('Error: El código del producto no se ha cargado correctamente. Por favor, selecciona el producto nuevamente.');
            return false;
        }
    });
});
</script>
