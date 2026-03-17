<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Permisos de Usuario</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Configurar permisos para <?php echo htmlspecialchars($usuario['nombre_completo']); ?> (<?php echo htmlspecialchars($usuario['username']); ?>)</p>
        </div>
        <a href="usuarios.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if ($usuario['rol'] === 'admin'): ?>
    <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: var(--primary-light); border: 1px solid #bfdbfe; color: var(--primary-hover);">
        <i class="fas fa-info-circle" style="font-size: 1rem;"></i>
        <span style="font-weight: 500; font-size: 0.8125rem;">Los administradores tienen acceso completo a todos los modulos del sistema.</span>
    </div>
    <?php else: ?>
    
    <?php if (isset($error)): ?>
    <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: var(--danger-light); border: 1px solid #fecaca; color: #991b1b;">
        <i class="fas fa-exclamation-circle"></i>
        <span style="font-weight: 500; font-size: 0.8125rem;"><?php echo htmlspecialchars($error); ?></span>
    </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <h3 style="margin: 0 0 4px 0; font-size: 1rem; font-weight: 700; color: var(--gray-900);">Modulos del Sistema</h3>
            <p style="margin: 0 0 1.5rem 0; color: var(--text-muted); font-size: 0.8125rem;">Selecciona los modulos a los que este usuario tendra acceso en el menu lateral.</p>

            <?php if (isset($modulos_disponibles['agregar_producto'])): ?>
            <div style="display: flex; align-items: center; gap: 10px; padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; background: var(--primary-light); border: 1px solid #bfdbfe; color: var(--primary-hover); font-size: 0.8125rem;">
                <i class="fas fa-info-circle"></i>
                <?php if ($usuario['rol'] === 'compras'): ?>
                    Este usuario puede agregar productos al <strong>Almacen General</strong>.
                <?php else: ?>
                    Este usuario puede agregar productos solo a su <strong>Sub-Almacen: <?php echo htmlspecialchars($usuario['sub_almacen_nombre']); ?></strong>.
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
                <?php foreach($modulos_disponibles as $modulo => $info): ?>
                <label style="display: flex; align-items: center; padding: 1rem; background: var(--gray-50); border-radius: 10px; cursor: pointer; transition: all 0.15s; border: 1px solid var(--gray-200);"
                       onmouseover="this.style.background='var(--primary-light)'; this.style.borderColor='var(--primary)'"
                       onmouseout="this.style.background='var(--gray-50)'; this.style.borderColor='var(--gray-200)'">
                    <input type="checkbox" name="modulos[]" value="<?php echo $modulo; ?>" <?php echo in_array($modulo, $permisos_actuales) ? 'checked' : ''; ?> style="width: 18px; height: 18px; margin-right: 0.75rem; cursor: pointer; accent-color: var(--primary);">
                    <i class="fas <?php echo $info['icono']; ?>" style="margin-right: 0.5rem; color: var(--primary); font-size: 0.875rem;"></i>
                    <span style="font-weight: 600; color: var(--gray-900); font-size: 0.8125rem;"><?php echo $info['nombre']; ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="usuarios.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Permisos</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</main>
