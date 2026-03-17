<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Agregar Usuario</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Crear un nuevo usuario en el sistema</p>
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
        <form method="POST" id="formAgregarUsuario">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-user" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre de Usuario <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required class="form-input">
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-id-card" style="color: var(--primary); font-size: 0.75rem;"></i> Nombre Completo <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>" required class="form-input">
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-envelope" style="color: var(--primary); font-size: 0.75rem;"></i> Email
                    </label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" class="form-input">
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-shield-alt" style="color: var(--primary); font-size: 0.75rem;"></i> Rol <span style="color: var(--danger);">*</span>
                    </label>
                    <select id="rol" name="rol" required class="form-input">
                        <option value="">Seleccionar rol...</option>
                        <option value="admin" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="compras" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'compras') ? 'selected' : ''; ?>>Compras</option>
                        <option value="gerencia" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'gerencia') ? 'selected' : ''; ?>>Gerencia</option>
                        <option value="departamento" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'departamento') ? 'selected' : ''; ?>>Departamento</option>
                        <option value="solo_lectura" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'solo_lectura') ? 'selected' : ''; ?>>Solo Lectura</option>
                    </select>
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-warehouse" style="color: var(--primary); font-size: 0.75rem;"></i> Sub-Almacen
                    </label>
                    <select id="sub_almacen_id" name="sub_almacen_id" class="form-input">
                        <option value="">Sin sub-almacen asignado</option>
                        <?php foreach($sub_almacenes as $almacen): ?>
                            <option value="<?php echo $almacen['id']; ?>" <?php echo (isset($_POST['sub_almacen_id']) && $_POST['sub_almacen_id'] == $almacen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($almacen['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--text-muted); font-size: 0.6875rem; display: block; margin-top: 4px;">Opcional. Solo necesario para usuarios de departamento.</small>
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-lock" style="color: var(--primary); font-size: 0.75rem;"></i> Contrasena <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="password" id="password" name="password" required minlength="6" class="form-input">
                    <small style="color: var(--text-muted); font-size: 0.6875rem; display: block; margin-top: 4px;">Minimo 6 caracteres</small>
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-lock" style="color: var(--primary); font-size: 0.75rem;"></i> Confirmar Contrasena <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6" class="form-input">
                </div>
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="usuarios.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crear Usuario</button>
            </div>
        </form>
    </div>
</main>
