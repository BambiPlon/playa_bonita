<div class="main-content">
    <div style="padding: 20px; width: 100%; margin: 0 auto;">
        <!-- Header con gradiente azul marino minimalista -->
        <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); border-radius: 16px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-truck" style="font-size: 2rem; color: white;"></i>
                </div>
                <div>
                    <h2 style="color: white; margin: 0; font-size: 2rem; font-weight: 700;">
                        <?php echo $modo_edicion ? 'Editar Proveedor' : 'Agregar Proveedor'; ?>
                    </h2>
                    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0;">
                        <?php echo $modo_edicion ? 'Modificar información del proveedor' : 'Registrar un nuevo proveedor en el sistema'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario con diseño minimalista -->
        <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); padding: 2.5rem; border: 1px solid rgba(41, 98, 255, 0.08);">
            <form method="POST" action="agregar-proveedor.php" id="formProveedor" onsubmit="return confirmarGuardarProveedor(event)">
                <?php if ($modo_edicion): ?>
                    <input type="hidden" name="id" value="<?php echo $proveedor['id']; ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-building" style="color: #2962FF;"></i> Nombre del Proveedor *
                        </label>
                        <input type="text" name="nombre" required
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['nombre']) : ''; ?>"
                               style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                               placeholder="Ej: Suministros SA de CV"
                               onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    </div>
                    
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-user" style="color: #2962FF;"></i> Persona de Contacto
                        </label>
                        <input type="text" name="contacto"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['contacto']) : ''; ?>"
                               style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                               placeholder="Ej: Juan Pérez"
                               onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    </div>
                    
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-phone" style="color: #2962FF;"></i> Teléfono
                        </label>
                        <input type="text" name="telefono"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['telefono']) : ''; ?>"
                               style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                               placeholder="Ej: 1234567890"
                               onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    </div>
                    
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-envelope" style="color: #2962FF;"></i> Email
                        </label>
                        <input type="email" name="email"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['email']) : ''; ?>"
                               style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                               placeholder="Ej: contacto@proveedor.com"
                               onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    </div>
                    
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-id-card" style="color: #2962FF;"></i> RFC
                        </label>
                        <input type="text" name="rfc"
                               value="<?php echo $modo_edicion ? htmlspecialchars($proveedor['rfc']) : ''; ?>"
                               style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f9fafb;"
                               placeholder="Ej: PEGJ850101ABC"
                               onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'">
                    </div>
                    
                    <div style="grid-column: 1 / -1;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #0a192f; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-map-marker-alt" style="color: #2962FF;"></i> Dirección
                        </label>
                        <textarea name="direccion" rows="3"
                                  style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; resize: vertical; transition: all 0.3s; background: #f9fafb;"
                                  placeholder="Dirección completa del proveedor"
                                  onfocus="this.style.borderColor='#2962FF'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(41, 98, 255, 0.08)'"
                                  onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'; this.style.boxShadow='none'"><?php echo $modo_edicion ? htmlspecialchars($proveedor['direccion']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Botones con nuevo diseño azul marino -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #f3f4f6;">
                    <button type="submit" style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 16px rgba(41, 98, 255, 0.3); transition: all 0.3s; font-size: 1rem;">
                        <i class="fas fa-save"></i> <?php echo $modo_edicion ? 'Actualizar Proveedor' : 'Guardar Proveedor'; ?>
                    </button>
                    <button type="button" onclick="location.href='proveedores.php'" style="background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmarGuardarProveedor(event) {
    event.preventDefault();
    
    const nombre = document.querySelector('input[name="nombre"]').value;
    const modoEdicion = <?php echo $modo_edicion ? 'true' : 'false'; ?>;
    const accion = modoEdicion ? 'actualizar' : 'guardar';
    const titulo = modoEdicion ? '¿Actualizar proveedor?' : '¿Guardar proveedor?';
    
    Swal.fire({
        title: titulo,
        html: `<p>Se ${accion}á el proveedor:</p>
               <p><strong>${nombre}</strong></p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formProveedor').submit();
        }
    });
    
    return false;
}
</script>
