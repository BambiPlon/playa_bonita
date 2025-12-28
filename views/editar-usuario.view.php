<main class="main-content">
    <!-- Rediseño header con tema azul marino minimalista -->
    <div class="page-header" style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-user-edit" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: white;">Editar Usuario</h1>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 1rem;">Modificar datos del usuario <?php echo htmlspecialchars($usuario['username']); ?></p>
            </div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <!-- Estilo de alerta actualizado -->
        <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 4px solid #ef4444; padding: 1.25rem; margin-bottom: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);">
            <div style="display: flex; align-items: start; gap: 0.75rem;">
                <i class="fas fa-exclamation-circle" style="color: #dc2626; margin-top: 2px; font-size: 1.25rem;"></i>
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; color: #991b1b; font-weight: 600;">Errores en el formulario:</h3>
                    <ul style="margin: 0; padding-left: 1.25rem; color: #7f1d1d;">
                        <?php foreach($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card rediseñada con estilo minimalista -->
    <div style="background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); border: 1px solid rgba(41, 98, 255, 0.08);">
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <!-- Username -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Nombre de Usuario <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           name="username" 
                           value="<?php echo htmlspecialchars($usuario['username']); ?>"
                           required
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <!-- Email -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Email
                    </label>
                    <input type="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>"
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <!-- Nombre Completo -->
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Nombre Completo <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           name="nombre_completo" 
                           value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>"
                           required
                           style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                           onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                </div>

                <!-- Rol -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Rol <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="rol" 
                            required
                            style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                            onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                        <option value="">Seleccionar rol...</option>
                        <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="gerencia_general" <?php echo $usuario['rol'] === 'gerencia_general' ? 'selected' : ''; ?>>Gerencia General</option>
                        <option value="gerencia" <?php echo $usuario['rol'] === 'gerencia' ? 'selected' : ''; ?>>Gerencia</option>
                        <option value="compras" <?php echo $usuario['rol'] === 'compras' ? 'selected' : ''; ?>>Compras</option>
                        <option value="departamento" <?php echo $usuario['rol'] === 'departamento' ? 'selected' : ''; ?>>Departamento</option>
                        <option value="solo_lectura" <?php echo $usuario['rol'] === 'solo_lectura' ? 'selected' : ''; ?>>Solo Lectura</option>
                    </select>
                </div>

                <!-- Sub-Almacén -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Sub-Almacén
                    </label>
                    <select name="sub_almacen_id" 
                            style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                            onfocus="this.style.borderColor='#2962FF'; this.style.outline='none'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                        <option value="">Ninguno</option>
                        <?php foreach($sub_almacenes as $almacen): ?>
                            <option value="<?php echo $almacen['id']; ?>" <?php echo $usuario['sub_almacen_id'] == $almacen['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($almacen['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cambiar Contraseña -->
                <div style="grid-column: 1 / -1; padding: 1.5rem; background: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: #374151;">
                        <i class="fas fa-key" style="color: #6b7280;"></i> Cambiar Contraseña
                    </h3>
                    <p style="margin: 0 0 1rem 0; color: #6b7280; font-size: 0.875rem;">
                        Deja estos campos vacíos si no deseas cambiar la contraseña
                    </p>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f;">
                                Nueva Contraseña
                            </label>
                            <input type="password" 
                                   name="password" 
                                   style="width: 100%; padding: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                                   onfocus="this.style.borderColor='#3b82f6'; this.style.outline='none'"
                                   onblur="this.style.borderColor='#d1d5db'">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #0a192f;">
                                Confirmar Contraseña
                            </label>
                            <input type="password" 
                                   name="password_confirm" 
                                   style="width: 100%; padding: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                                   onfocus="this.style.borderColor='#3b82f6'; this.style.outline='none'"
                                   onblur="this.style.borderColor='#d1d5db'">
                        </div>
                    </div>
                </div>

                <!-- Estado Activo -->
                <div style="grid-column: 1 / -1;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                        <input type="checkbox" 
                               name="activo" 
                               value="1"
                               <?php echo $usuario['activo'] ? 'checked' : ''; ?>
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="font-weight: 600; color: #374151;">Usuario Activo</span>
                    </label>
                </div>
            </div>

            <!-- Botones rediseñados con tema azul marino -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #f3f4f6;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); font-size: 1rem;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(41, 98, 255, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(41, 98, 255, 0.3)'">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" 
                        onclick="window.location.href='usuarios.php'"
                        style="background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem;"
                        onmouseover="this.style.background='#e5e7eb'; this.style.borderColor='#d1d5db'"
                        onmouseout="this.style.background='#f3f4f6'; this.style.borderColor='#e5e7eb'">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</main>
