<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Editar Usuario</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Modificar datos de <?php echo htmlspecialchars($usuario['username']); ?></p>
        </div>
        <a href="usuarios.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div style="display: flex; align-items: start; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: var(--danger-light); border: 1px solid #fecaca; color: #991b1b;">
        <i class="fas fa-exclamation-circle" style="font-size: 1rem; margin-top: 2px;"></i>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8125rem;">
            <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-user" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre de Usuario <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($usuario['username']); ?>" required class="form-input">
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-envelope" style="color: var(--primary); font-size: 0.75rem;"></i> Email
                    </label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" class="form-input">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-id-card" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre Completo <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required class="form-input">
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-shield-alt" style="color: var(--primary); font-size: 0.75rem;"></i> Rol <span style="color: var(--danger);">*</span>
                    </label>
                    <select name="rol" required class="form-input">
                        <option value="">Seleccionar rol...</option>
                        <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="gerencia_general" <?php echo $usuario['rol'] === 'gerencia_general' ? 'selected' : ''; ?>>Gerencia General</option>
                        <option value="gerencia" <?php echo $usuario['rol'] === 'gerencia' ? 'selected' : ''; ?>>Gerencia</option>
                        <option value="compras" <?php echo $usuario['rol'] === 'compras' ? 'selected' : ''; ?>>Compras</option>
                        <option value="departamento" <?php echo $usuario['rol'] === 'departamento' ? 'selected' : ''; ?>>Departamento</option>
                        <option value="solo_lectura" <?php echo $usuario['rol'] === 'solo_lectura' ? 'selected' : ''; ?>>Solo Lectura</option>
                    </select>
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-warehouse" style="color: var(--primary); font-size: 0.75rem;"></i> Sub-Almacen
                    </label>
                    <select name="sub_almacen_id" class="form-input">
                        <option value="">Ninguno</option>
                        <?php foreach($sub_almacenes as $almacen): ?>
                            <option value="<?php echo $almacen['id']; ?>" <?php echo $usuario['sub_almacen_id'] == $almacen['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($almacen['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="padding: 1.25rem; background: var(--gray-50); border-radius: 10px; margin-bottom: 1.25rem; border: 1px dashed var(--gray-300);">
                <h3 style="margin: 0 0 0.75rem 0; font-size: 0.875rem; font-weight: 700; color: var(--gray-700); display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-key" style="color: var(--text-muted);"></i> Cambiar Contrasena
                </h3>
                <p style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.75rem;">Deja estos campos vacios si no deseas cambiar la contrasena</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: var(--gray-700); font-size: 0.8125rem;">Nueva Contrasena</label>
                        <input type="password" name="password" class="form-input">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: var(--gray-700); font-size: 0.8125rem;">Confirmar Contrasena</label>
                        <input type="password" name="password_confirm" class="form-input">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="activo" value="1" <?php echo $usuario['activo'] ? 'checked' : ''; ?> style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                    <span style="font-weight: 600; color: var(--gray-700); font-size: 0.8125rem;">Usuario Activo</span>
                </label>
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="usuarios.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</main>
