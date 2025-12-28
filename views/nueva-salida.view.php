<?php require 'includes/header.php'; ?>
<?php // Eliminando require duplicado del sidebar ya que header.php ya lo incluye ?>

<main class="main-content" style="width: 100%; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); min-height: 100vh;">
    <!-- Aplicando diseño claro con header estilo dashboard -->
    <div style="background: white; color: #1f2937; padding: 25px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(16, 185, 129, 0.2);">
        <h1 style="margin: 0; font-size: 28px; display: flex; align-items: center; gap: 12px;">
            <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-box-open" style="color: #10b981; font-size: 24px;"></i>
            </div>
            Nueva Salida de Almacén
        </h1>
        <a href="salidas.php" class="btn btn-secondary" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 2px solid #10b981; padding: 10px 20px; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981;">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px; border: 1px solid rgba(16, 185, 129, 0.2);">
        <!-- Agregando confirmación al formulario -->
        <form method="POST" action="nueva-salida.php" id="formNuevaSalida" onsubmit="return confirmarSalida(event)">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <?php if ($user['rol'] === 'admin' || $user['rol'] === 'compras'): ?>
                    <div class="form-group">
                        <label for="sub_almacen_id" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                            <i class="fas fa-warehouse" style="color: #10b981;"></i> Sub-Almacén: *
                        </label>
                        <!-- Cambiando select oscuro a select claro -->
                        <select id="sub_almacen_id" name="sub_almacen_id" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                            <option value="" style="background: white;">Seleccionar...</option>
                            <?php foreach ($sub_almacenes as $almacen): ?>
                                <option value="<?php echo $almacen['id']; ?>" style="background: white;">
                                    <?php echo htmlspecialchars($almacen['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="producto_id" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-box" style="color: #10b981;"></i> Producto: *
                    </label>
                    <select id="producto_id" name="producto_id" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                        <option value="" style="background: white;">Seleccionar...</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" style="background: white;">
                                <?php echo htmlspecialchars($prod['nombre']); ?> 
                                (Stock: <?php echo $prod['cantidad']; ?> <?php echo $prod['unidad']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cantidad" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-hashtag" style="color: #10b981;"></i> Cantidad: *
                    </label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
                
                <div class="form-group">
                    <label for="fecha_salida" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                        <i class="fas fa-calendar" style="color: #10b981;"></i> Fecha de Salida: *
                    </label>
                    <input type="date" id="fecha_salida" name="fecha_salida" 
                           value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="destino" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #10b981;"></i> Destino:
                </label>
                <input type="text" id="destino" name="destino" 
                       placeholder="Ej: Oficina principal, Evento, etc." style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; color: #1f2937;">
            </div>
            
            <div class="form-group">
                <label for="motivo" style="display: flex; align-items: center; gap: 8px; color: #374151; font-weight: 600; margin-bottom: 8px;">
                    <i class="fas fa-comment-alt" style="color: #10b981;"></i> Motivo: *
                </label>
                <textarea id="motivo" name="motivo" rows="4" required 
                          placeholder="Describe el motivo de la salida..." style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical; background: white; color: #1f2937;"></textarea>
            </div>
            
            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="background: #10b981; color: white; border: none; padding: 12px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-save"></i> Registrar Salida
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Agregando script de confirmación -->
<script>
function confirmarSalida(event) {
    event.preventDefault();
    
    const producto = document.getElementById('producto_id').selectedOptions[0].text;
    const cantidad = document.getElementById('cantidad').value;
    
    Swal.fire({
        title: '¿Registrar salida?',
        html: `<p>Se registrará la salida de:</p>
               <p><strong>${cantidad}</strong> unidades de <strong>${producto}</strong></p>
               <p class="text-muted" style="font-size: 0.9em; color: #6b7280; margin-top: 10px;">Esta acción actualizará el inventario.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formNuevaSalida').submit();
        }
    });
    
    return false;
}
</script>

<?php require 'includes/footer.php'; ?>
