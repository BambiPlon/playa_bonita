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
                <div class="form-group">
                    <label for="codigo" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-barcode" style="color: #2962FF;"></i> Código: *
                    </label>
                    <input type="text" id="codigo" name="codigo" required 
                           placeholder="Ej: TEC-001" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
                
                <div class="form-group">
                    <label for="nombre" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-box" style="color: #2962FF;"></i> Nombre: *
                    </label>
                    <input type="text" id="nombre" name="nombre" required 
                           placeholder="Nombre del producto" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
                
                <div class="form-group">
                    <label for="sub_almacen_id" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-warehouse" style="color: #2962FF;"></i> Sub-Almacén: *
                    </label>
                    <?php if ($usuarioData['rol'] === 'compras'): ?>
                        <!-- Compras: mostrar Almacén General -->
                        <input type="text" value="Almacén General" readonly 
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #f3f4f6; color: #1f2937; cursor: not-allowed;">
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
    const codigoInput = document.getElementById('codigo');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const unidadInput = document.getElementById('unidad');
    const precioInput = document.getElementById('precio_unitario');
    const stockMinimoInput = document.getElementById('stock_minimo');
    
    console.log('[v0] Autocompletado inicializado para usuario');
    
    if (!codigoInput) {
        console.error('[v0] Campo de código no encontrado');
        return;
    }
    
    let timeoutId = null;
    
    codigoInput.addEventListener('input', function() {
        // Limpiar timeout anterior
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        const codigo = this.value.trim();
        
        console.log('[v0] Código ingresado:', codigo);
        
        if (codigo.length < 2) {
            codigoInput.style.borderColor = '#d1d5db';
            codigoInput.style.borderWidth = '1px';
            ocultarMensajeInfo();
            return;
        }
        
        // Esperar 500ms después de que el usuario deje de escribir
        timeoutId = setTimeout(() => {
            console.log('[v0] Iniciando búsqueda para código:', codigo);
            
            fetch(`api/buscar-producto.php?codigo=${encodeURIComponent(codigo)}`)
                .then(response => {
                    console.log('[v0] Respuesta HTTP status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('[v0] Datos recibidos:', data);
                    
                    if (data.existe) {
                        // Llenar automáticamente los campos con los datos del producto existente
                        nombreInput.value = data.producto.nombre;
                        descripcionInput.value = data.producto.descripcion || '';
                        unidadInput.value = data.producto.unidad;
                        precioInput.value = data.producto.precio_unitario || '';
                        stockMinimoInput.value = data.producto.stock_minimo || '';
                        
                        // Cambiar el borde a azul marino para indicar que existe
                        codigoInput.style.borderColor = '#2962FF';
                        codigoInput.style.borderWidth = '2px';
                        
                        // Mostrar mensaje informativo
                        mostrarMensajeInfo(`Producto encontrado. Cantidad actual: ${data.producto.cantidad_actual} ${data.producto.unidad}`);
                        
                        console.log('[v0] Campos autocompletados correctamente');
                    } else {
                        // Producto no existe, limpiar campos y permitir ingreso nuevo
                        codigoInput.style.borderColor = '#d1d5db';
                        codigoInput.style.borderWidth = '1px';
                        ocultarMensajeInfo();
                        console.log('[v0] Producto no encontrado, listo para crear nuevo');
                    }
                })
                .catch(error => {
                    console.error('[v0] Error al buscar producto:', error);
                    codigoInput.style.borderColor = '#ef4444';
                    codigoInput.style.borderWidth = '2px';
                    mostrarMensajeInfo('Error al buscar producto. Verifica la conexión.');
                });
        }, 500);
    });
    
    function mostrarMensajeInfo(mensaje) {
        // Remover mensaje anterior si existe
        const mensajeAnterior = document.getElementById('mensaje-info-codigo');
        if (mensajeAnterior) {
            mensajeAnterior.remove();
        }
        
        // Crear nuevo mensaje
        const mensajeDiv = document.createElement('small');
        mensajeDiv.id = 'mensaje-info-codigo';
        mensajeDiv.style.color = '#2962FF';
        mensajeDiv.style.fontSize = '12px';
        mensajeDiv.style.marginTop = '4px';
        mensajeDiv.style.display = 'block';
        mensajeDiv.innerHTML = '<i class="fas fa-info-circle"></i> ' + mensaje;
        
        codigoInput.parentElement.appendChild(mensajeDiv);
    }
    
    function ocultarMensajeInfo() {
        const mensajeAnterior = document.getElementById('mensaje-info-codigo');
        if (mensajeAnterior) {
            mensajeAnterior.remove();
        }
    }
});
</script>
