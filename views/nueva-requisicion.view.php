<?php require 'includes/header.php'; ?>
<?php // Eliminando require duplicado del sidebar ya que header.php ya lo incluye ?>

<!-- Cambiando fondo oscuro a blanco/gris claro -->
<main class="main-content" style="background: #f9fafb; min-height: 100vh;">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding: 20px;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">
                <i class="fas fa-file-alt" style="color: #2962FF; margin-right: 0.75rem;"></i>
                Nueva Requisición de Compra
            </h1>
            <p style="color: #6b7280; font-size: 0.9375rem;">Complete la información para crear una nueva solicitud de compra</p>
        </div>
        <a href="requisiciones.php" class="btn btn-secondary" style="background: white; color: #6b7280; border: 2px solid #e5e7eb;">
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

    <form method="POST" action="nueva-requisicion.php" style="max-width: 100%; width: 100%; margin: 0; padding: 0 20px 20px;" id="formNuevaRequisicion" onsubmit="return confirmarCrearRequisicion(event)">
        <!-- Sección de información general con fondo blanco -->
        <div class="card" style="margin-bottom: 1.5rem; background: white; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div class="card-header" style="background: linear-gradient(135deg, #2962FF 0%, #1e40af 100%); color: white; padding: 1.25rem 1.5rem; border-bottom: none; border-radius: 1rem 1rem 0 0;">
                <h2 style="font-size: 1.125rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-info-circle"></i>
                    Información General
                </h2>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
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
                            <i class="fas fa-calendar-alt" style="color: #2962FF;"></i>
                            Fecha de Solicitud <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" id="fecha_solicitud" name="fecha_solicitud" 
                               value="<?php echo date('Y-m-d'); ?>" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label for="observaciones" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151;">
                        <i class="fas fa-comment-alt" style="color: #2962FF;"></i>
                        Observaciones Generales
                    </label>
                    <textarea id="observaciones" name="observaciones" rows="3" 
                              placeholder="Ingrese observaciones adicionales (opcional)..."
                              style="resize: vertical; background: white; border: 1px solid #d1d5db; color: #111827;"></textarea>
                </div>
            </div>
        </div>

        <!-- Sección de productos con fondo blanco -->
        <div class="card" style="background: white; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 1.5rem;">
            <div class="card-header" style="background: linear-gradient(135deg, #2962FF 0%, #1e40af 100%); color: white; padding: 1.25rem 1.5rem; border-bottom: none; border-radius: 1rem 1rem 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.125rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-boxes"></i>
                    Productos Solicitados
                </h2>
                <button type="button" onclick="agregarProducto()" class="btn" 
                        style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); color: white; padding: 0.625rem 1.25rem; font-size: 0.875rem;">
                    <i class="fas fa-plus-circle"></i> Agregar Producto
                </button>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <div id="productos-container" style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Card de producto individual con fondo blanco -->
                    <div class="producto-item" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;">
                        <button type="button" onclick="this.parentElement.remove()" 
                                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-box"></i>
                                    Producto <span style="color: #ef4444;">*</span>
                                </label>
                                <input list="productos-list-0" name="productos_search[]" id="productos_search_0" 
                                       placeholder="Escriba para buscar..." onchange="selectProducto(0)" 
                                       style="width: 100%;" required>
                                <datalist id="productos-list-0">
                                    <?php foreach ($datosFormulario['productos'] as $prod): ?>
                                        <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                                data-id="<?php echo $prod['id']; ?>"
                                                data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>">
                                            <?php echo htmlspecialchars($prod['nombre']); ?> - <?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="otro">Otro (no en inventario)</option>
                                </datalist>
                                <input type="hidden" name="productos[]" id="producto_id_0">
                                <input type="text" name="producto_nombre_0" id="producto_nombre_0" 
                                       placeholder="Especifique el nombre del producto" 
                                       style="margin-top: 0.75rem; display: none; width: 100%;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-hashtag"></i>
                                    Cantidad <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="number" name="cantidades[]" min="1" placeholder="0" 
                                       style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                    <i class="fas fa-ruler"></i>
                                    Unidad de Medida <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="unidades[]" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                                    <option value="">Seleccionar...</option>
                                    <option value="pieza">Pieza</option>
                                    <option value="caja">Caja</option>
                                    <option value="paquete">Paquete</option>
                                    <option value="litro">Litro</option>
                                    <option value="kilogramo">Kilogramo</option>
                                    <option value="metro">Metro</option>
                                    <option value="rollo">Rollo</option>
                                    <option value="bolsa">Bolsa</option>
                                    <option value="galón">Galón</option>
                                    <option value="unidad">Unidad</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed rgba(41, 98, 255, 0.2); text-align: center;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.375rem;"></i>
                        Puede agregar más productos utilizando el botón "Agregar Producto"
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="requisiciones.php" class="btn btn-secondary" style="padding: 0.875rem 2rem; background: white; color: #6b7280; border: 2px solid #e5e7eb;">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2.5rem; font-size: 1rem; background: #2962FF; border: none; box-shadow: 0 4px 12px rgba(41, 98, 255, 0.3);">
                <i class="fas fa-paper-plane"></i> Crear Requisición
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
    border-color: #2962FF;
    box-shadow: 0 4px 12px rgba(41, 98, 255, 0.15);
    transform: translateY(-2px);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let contadorProductos = 1;

function agregarProducto() {
    contadorProductos++;
    const container = document.getElementById('productos-container');
    const nuevoProducto = document.createElement('div');
    nuevoProducto.className = 'producto-item';
    nuevoProducto.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; position: relative; transition: all 0.3s ease;';
    nuevoProducto.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
                style="position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
            <i class="fas fa-times"></i>
        </button>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 1.25rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-box"></i>
                    Producto <span style="color: #ef4444;">*</span>
                </label>
                <input list="productos-list-${contadorProductos}" name="productos_search[]" id="productos_search_${contadorProductos}" 
                       placeholder="Escriba para buscar..." onchange="selectProducto(${contadorProductos})" 
                       style="width: 100%;" required>
                <datalist id="productos-list-${contadorProductos}">
                    <?php foreach ($datosFormulario['productos'] as $prod): ?>
                        <option value="<?php echo htmlspecialchars($prod['nombre']); ?>" 
                                data-id="<?php echo $prod['id']; ?>"
                                data-sub="<?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>">
                            <?php echo htmlspecialchars($prod['nombre']); ?> - <?php echo htmlspecialchars($prod['sub_almacen_nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="otro">Otro (no en inventario)</option>
                </datalist>
                <input type="hidden" name="productos[]" id="producto_id_${contadorProductos}">
                <input type="text" name="producto_nombre_${contadorProductos}" id="producto_nombre_${contadorProductos}" 
                       placeholder="Especifique el nombre del producto" 
                       style="margin-top: 0.75rem; display: none; width: 100%;">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-hashtag"></i>
                    Cantidad <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" name="cantidades[]" min="1" placeholder="0" 
                       style="text-align: center; font-weight: 600; font-size: 1.125rem; background: white; border: 1px solid #d1d5db; color: #111827;" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    <i class="fas fa-ruler"></i>
                    Unidad de Medida <span style="color: #ef4444;">*</span>
                </label>
                <select name="unidades[]" required style="background: white; border: 1px solid #d1d5db; color: #111827;">
                    <option value="">Seleccionar...</option>
                    <option value="pieza">Pieza</option>
                    <option value="caja">Caja</option>
                    <option value="paquete">Paquete</option>
                    <option value="litro">Litro</option>
                    <option value="kilogramo">Kilogramo</option>
                    <option value="metro">Metro</option>
                    <option value="rollo">Rollo</option>
                    <option value="bolsa">Bolsa</option>
                    <option value="galón">Galón</option>
                    <option value="unidad">Unidad</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(nuevoProducto);
}

function selectProducto(index) {
    const searchInput = document.getElementById('productos_search_' + index);
    const hiddenInput = document.getElementById('producto_id_' + index);
    const nombreInput = document.getElementById('producto_nombre_' + index);
    
    if (searchInput.value === 'otro') {
        nombreInput.style.display = 'block';
        nombreInput.required = true;
        hiddenInput.value = 'otro';
    } else {
        nombreInput.style.display = 'none';
        nombreInput.required = false;
        
        const datalist = document.getElementById('productos-list-' + index);
        const options = datalist.querySelectorAll('option');
        let found = false;
        
        options.forEach(option => {
            if (option.value === searchInput.value) {
                hiddenInput.value = option.getAttribute('data-id');
                found = true;
            }
        });
        
        if (!found) {
            hiddenInput.value = '';
        }
    }
}

function confirmarCrearRequisicion(event) {
    event.preventDefault();
    
    // Contar productos agregados
    const productos = document.querySelectorAll('.producto-item');
    
    if (productos.length === 0) {
        alertaError('Debes agregar al menos un producto a la requisición');
        return false;
    }
    
    Swal.fire({
        title: '¿Crear requisición?',
        html: `<p>Se creará una requisición con <strong>${productos.length}</strong> producto(s)</p>
               <p class="text-muted" style="font-size: 0.9em; color: #6b7280; margin-top: 10px;">Se notificará al departamento de compras.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formNuevaRequisicion').submit();
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
</script>

<?php require 'includes/footer.php'; ?>
