<main class="main-content">
    <!-- Header rediseñado con gradiente azul marino minimalista -->
    <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-user-plus" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700;">Agregar Usuario</h1>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Crear un nuevo usuario en el sistema</p>
            </div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <!-- Alerta de errores con diseño minimalista -->
    <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 4px solid #ef4444; padding: 1.25rem; margin-bottom: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);">
        <ul style="margin: 0; padding-left: 1.25rem; color: #7f1d1d;">
            <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Card con diseño minimalista y bordes sutiles -->
    <div style="background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); border: 1px solid rgba(41, 98, 255, 0.08);">
        <form method="POST" id="formAgregarUsuario">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Nombre de Usuario <span style="color: #ef4444;">*</span>
                    </label>
                    <!-- Input con efectos de enfoque modernos -->
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="nombre_completo" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Nombre Completo <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           id="nombre_completo" 
                           name="nombre_completo" 
                           value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>"
                           required
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="rol" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Rol <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="rol" name="rol" required style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                            onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                        <option value="">Seleccionar rol...</option>
                        <option value="admin" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="compras" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'compras') ? 'selected' : ''; ?>>Compras</option>
                        <option value="gerencia" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'gerencia') ? 'selected' : ''; ?>>Gerencia</option>
                        <option value="departamento" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'departamento') ? 'selected' : ''; ?>>Departamento</option>
                        <option value="solo_lectura" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'solo_lectura') ? 'selected' : ''; ?>>Solo Lectura</option>
                    </select>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label for="sub_almacen_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Sub-Almacén
                    </label>
                    <select id="sub_almacen_id" name="sub_almacen_id" style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                            onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                        <option value="">Sin sub-almacén asignado</option>
                        <?php foreach($sub_almacenes as $almacen): ?>
                            <option value="<?php echo $almacen['id']; ?>"
                                    <?php echo (isset($_POST['sub_almacen_id']) && $_POST['sub_almacen_id'] == $almacen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($almacen['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem; display: block;">Opcional. Solo necesario para usuarios de departamento.</small>
                </div>

                <div>
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Contraseña <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="6"
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    <small style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem; display: block;">Mínimo 6 caracteres</small>
                </div>

                <div>
                    <label for="password_confirm" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Confirmar Contraseña <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           required
                           minlength="6"
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>
            </div>

            <!-- Botones con gradiente azul marino -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #f3f4f6;">
                <button type="submit" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); font-size: 1rem;">
                    <i class="fas fa-save"></i> Crear Usuario
                </button>
                <button type="button" onclick="window.location.href='usuarios.php'" style="background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</main>
