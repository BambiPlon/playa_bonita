<?php require 'includes/header.php'; ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Editar Producto</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Modifica los datos del producto en el inventario</p>
        </div>
        <a href="index.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if ($mensaje_error): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: var(--danger-light); border: 1px solid #fecaca; color: #991b1b;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.125rem;"></i>
            <span style="font-weight: 500; font-size: 0.875rem;"><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: var(--success-light); border: 1px solid #bbf7d0; color: #166534;">
            <i class="fas fa-check-circle" style="font-size: 1.125rem;"></i>
            <span style="font-weight: 500; font-size: 0.875rem;"><?php echo htmlspecialchars($mensaje_exito); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-barcode" style="color: var(--primary); font-size: 0.75rem;"></i> Codigo
                    </label>
                    <input type="text" value="<?php echo htmlspecialchars($producto['codigo']); ?>" readonly class="form-input" style="background: var(--gray-50); cursor: not-allowed; color: var(--text-muted);">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-box" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required class="form-input">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-sort-numeric-up" style="color: var(--primary); font-size: 0.75rem;"></i> Cantidad <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="cantidad" value="<?php echo intval($producto['cantidad']); ?>" min="0" step="1" required class="form-input">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-ruler" style="color: var(--primary); font-size: 0.75rem;"></i> Unidad <span style="color: var(--danger);">*</span>
                    </label>
                    <select name="unidad" required class="form-input">
                        <option value="">Seleccionar unidad...</option>
                        <?php 
                        $unidades = ['pieza','unidad','caja','paquete','bolsa','rollo','metro','litro','galon','kilogramo','gramo','juego','par','docena','millar','tonelada','cubeta','bote','lata','botella','garrafon','servicio'];
                        foreach ($unidades as $u): ?>
                            <option value="<?php echo $u; ?>" <?php echo ($producto['unidad'] == $u) ? 'selected' : ''; ?>><?php echo ucfirst($u); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-dollar-sign" style="color: var(--primary); font-size: 0.75rem;"></i> Precio Unitario <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="precio_unitario" value="<?php echo $producto['precio_unitario']; ?>" step="0.01" min="0" required class="form-input">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-exclamation-triangle" style="color: var(--primary); font-size: 0.75rem;"></i> Stock Minimo
                    </label>
                    <input type="number" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>" min="0" step="1" class="form-input">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-warehouse" style="color: var(--primary); font-size: 0.75rem;"></i> Departamento <span style="color: var(--danger);">*</span>
                    </label>
                    <select name="sub_almacen_id" required class="form-input">
                        <?php foreach ($sub_almacenes as $almacen): ?>
                            <option value="<?php echo $almacen['id']; ?>" <?php echo ($producto['sub_almacen_id'] == $almacen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($almacen['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                    <i class="fas fa-align-left" style="color: var(--primary); font-size: 0.75rem;"></i> Descripcion
                </label>
                <textarea name="descripcion" rows="3" placeholder="Descripcion del producto (opcional)" class="form-input" style="resize: vertical;"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
