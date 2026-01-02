<?php require 'includes/header.php'; ?>

<main class="main-content">
    <!-- Header moderno -->
    <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 8px 24px rgba(41, 98, 255, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 8px 0; color: white; font-size: 28px; font-weight: 700;">
                    <i class="fas fa-edit" style="margin-right: 12px;"></i>Editar Producto
                </h1>
                <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 14px;">
                    Modifica los datos del producto en el inventario
                </p>
            </div>
            <a href="index.php" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(10px); transition: all 0.2s;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje_error): ?>
        <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #991b1b; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                <strong><?php echo htmlspecialchars($mensaje_error); ?></strong>
            </p>
        </div>
    <?php endif; ?>

    <!-- Agregado mensaje de éxito -->
    <?php if ($mensaje_exito): ?>
        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 4px solid #10b981; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #065f46; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle"></i>
                <strong><?php echo htmlspecialchars($mensaje_exito); ?></strong>
            </p>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" action="" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <!-- Código (solo lectura) -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-barcode" style="color: #2962FF;"></i> Código
                </label>
                <input type="text" 
                       value="<?php echo htmlspecialchars($producto['codigo']); ?>" 
                       readonly
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: #f9fafb; color: #6b7280; cursor: not-allowed;">
            </div>

            <!-- Nombre -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-box" style="color: #2962FF;"></i> Nombre *
                </label>
                <input type="text" 
                       name="nombre" 
                       value="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                       required
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                       onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Cantidad -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-sort-numeric-up" style="color: #2962FF;"></i> Cantidad *
                </label>
                <input type="number" 
                       name="cantidad" 
                       value="<?php echo $producto['cantidad']; ?>" 
                       min="0" 
                       required
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                       onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Unidad -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-ruler" style="color: #2962FF;"></i> Unidad *
                </label>
                <input type="text" 
                       name="unidad" 
                       value="<?php echo htmlspecialchars($producto['unidad']); ?>" 
                       required
                       placeholder="ej: pieza, litro, kg"
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                       onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Precio Unitario -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-dollar-sign" style="color: #2962FF;"></i> Precio Unitario *
                </label>
                <input type="number" 
                       name="precio_unitario" 
                       value="<?php echo $producto['precio_unitario']; ?>" 
                       step="0.01" 
                       min="0" 
                       required
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                       onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Stock Mínimo -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-exclamation-triangle" style="color: #2962FF;"></i> Stock Mínimo
                </label>
                <input type="number" 
                       name="stock_minimo" 
                       value="<?php echo $producto['stock_minimo']; ?>" 
                       min="0"
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                       onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Sub-Almacén -->
            <div>
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-warehouse" style="color: #2962FF;"></i> Sub-Almacén *
                </label>
                <select name="sub_almacen_id" 
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    <?php foreach ($sub_almacenes as $almacen): ?>
                        <option value="<?php echo $almacen['id']; ?>" 
                                <?php echo ($producto['sub_almacen_id'] == $almacen['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($almacen['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Descripción -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">
                <i class="fas fa-align-left" style="color: #2962FF;"></i> Descripción
            </label>
            <textarea name="descripcion" 
                      rows="3"
                      placeholder="Descripción del producto (opcional)"
                      style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical; transition: all 0.2s; font-family: inherit;"
                      onfocus="this.style.borderColor='#2962FF'; this.style.boxShadow='0 0 0 3px rgba(41, 98, 255, 0.1)';"
                      onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>

        <!-- Botones -->
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="index.php" 
               style="background: #f3f4f6; color: #374151; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" 
                    style="background: linear-gradient(135deg, #2962FF 0%, #1d4ed8 100%); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(41, 98, 255, 0.3); transition: all 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(41, 98, 255, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.3)';">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</main>

<?php require 'includes/footer.php'; ?>
