<main class="main-content">
    <!-- Header con gradiente azul marino -->
    <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-key" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700;">Permisos de Usuario</h1>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Configurar permisos para <?php echo htmlspecialchars($usuario['nombre_completo']); ?> (<?php echo htmlspecialchars($usuario['username']); ?>)</p>
            </div>
        </div>
    </div>

    <?php if ($usuario['rol'] === 'admin'): ?>
    <!-- Alerta con diseño minimalista -->
    <div style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.05) 0%, rgba(41, 98, 255, 0.1) 100%); border-left: 4px solid #2962FF; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(41, 98, 255, 0.2);">
        <i class="fas fa-info-circle" style="color: #2962FF;"></i>
        Los administradores tienen acceso completo a todos los módulos del sistema.
    </div>
    <?php else: ?>
    
    <?php if (isset($error)): ?>
    <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 4px solid #ef4444; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- Card con diseño limpio -->
    <div style="background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); border: 1px solid rgba(41, 98, 255, 0.08);">
        <form method="POST">
            <h3 style="margin-bottom: 1.5rem; color: #0a192f; font-size: 1.5rem;">Módulos del Sistema</h3>
            <p style="margin-bottom: 2rem; color: #6b7280;">
                Selecciona los módulos a los que este usuario tendrá acceso en el menú lateral.
            </p>

            <?php if (isset($modulos_disponibles['agregar_producto'])): ?>
            <div style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.05) 0%, rgba(41, 98, 255, 0.1) 100%); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(41, 98, 255, 0.2);">
                <i class="fas fa-info-circle" style="color: #2962FF;"></i>
                <?php if ($usuario['rol'] === 'compras'): ?>
                    Este usuario puede agregar productos al <strong>Almacén General</strong>.
                <?php else: ?>
                    Este usuario puede agregar productos solo a su <strong>Sub-Almacén: <?php echo htmlspecialchars($usuario['sub_almacen_nombre']); ?></strong>.
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Grid de permisos con diseño minimalista -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                <?php foreach($modulos_disponibles as $modulo => $info): ?>
                <div>
                    <label style="display: flex; align-items: center; padding: 1.25rem; background: #f9fafb; border-radius: 12px; cursor: pointer; transition: all 0.3s; border: 2px solid #e5e7eb;"
                           onmouseover="this.style.background='rgba(41, 98, 255, 0.05)'; this.style.borderColor='#2962FF'; this.style.transform='translateX(5px)'"
                           onmouseout="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.transform='translateX(0)'">
                        <input type="checkbox" 
                               name="modulos[]" 
                               value="<?php echo $modulo; ?>"
                               <?php echo in_array($modulo, $permisos_actuales) ? 'checked' : ''; ?>
                               style="width: 20px; height: 20px; margin-right: 1rem; cursor: pointer; accent-color: #2962FF;">
                        <i class="fas <?php echo $info['icono']; ?>" style="margin-right: 0.75rem; color: #2962FF; font-size: 1.125rem;"></i>
                        <span style="font-weight: 600; color: #0a192f;"><?php echo $info['nombre']; ?></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Botones con gradiente azul marino -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #f3f4f6;">
                <button type="submit" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); font-size: 1rem;">
                    <i class="fas fa-save"></i> Guardar Permisos
                </button>
                <button type="button" onclick="window.location.href='usuarios.php'" style="background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</main>
