<?php require 'includes/header.php'; ?>

<main class="main-content" style="width: 100%; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); min-height: 100vh;">
    <!-- Header rediseñado con tema azul marino minimalista -->
    <div style="background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2962FF 100%); padding: 2.5rem; border-radius: 16px; margin-bottom: 2rem; box-shadow: 0 10px 40px rgba(41, 98, 255, 0.15);">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-bell" style="font-size: 2rem; color: white;"></i>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: white;">Mis Notificaciones</h1>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9; color: white;">Centro de notificaciones y actualizaciones</p>
            </div>
        </div>
    </div>

    <!-- Cards de notificaciones con diseño minimalista -->
    <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(10, 25, 47, 0.08); padding: 2rem; border: 1px solid rgba(41, 98, 255, 0.08);">
        <?php if (count($notificaciones) > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($notificaciones as $notif): ?>
                    <div style="background: <?php echo $notif['leida'] ? '#f9fafb' : 'linear-gradient(135deg, rgba(41, 98, 255, 0.03) 0%, rgba(41, 98, 255, 0.08) 100%)'; ?>; border-left: 4px solid <?php echo $notif['leida'] ? '#d1d5db' : '#2962FF'; ?>; border-radius: 12px; box-shadow: 0 2px 8px rgba(10, 25, 47, 0.05); transition: all 0.3s; border: 1px solid <?php echo $notif['leida'] ? '#e5e7eb' : 'rgba(41, 98, 255, 0.2)'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; padding: 1.5rem;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 0.75rem 0; color: #0a192f; display: flex; align-items: center; gap: 0.75rem; font-size: 1.125rem;">
                                    <i class="fas fa-<?php echo $notif['tipo'] === 'aprobacion' ? 'check-circle' : ($notif['tipo'] === 'rechazo' ? 'times-circle' : 'info-circle'); ?>" style="color: #2962FF;"></i>
                                    <?php echo htmlspecialchars($notif['titulo']); ?>
                                    <?php if (!$notif['leida']): ?>
                                        <span style="font-size: 0.75rem; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-weight: 600;">Nueva</span>
                                    <?php endif; ?>
                                </h3>
                                <p style="margin: 0 0 0.75rem 0; color: #6b7280; line-height: 1.6;">
                                    <?php echo htmlspecialchars($notif['mensaje']); ?>
                                </p>
                                <?php if ($notif['requisicion_folio']): ?>
                                    <p style="margin: 0; font-size: 0.875rem; color: #2962FF; font-weight: 600;">
                                        <i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($notif['requisicion_folio']); ?>
                                    </p>
                                <?php endif; ?>
                                <p style="margin: 0.75rem 0 0 0; font-size: 0.75rem; color: #9ca3af;">
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?>
                                </p>
                            </div>
                            <div style="display: flex; gap: 0.75rem; align-items: center;">
                                <?php if (!$notif['leida']): ?>
                                    <a href="notificaciones.php?marcar_leida=<?php echo $notif['id']; ?>" 
                                       style="background: linear-gradient(135deg, #2962FF 0%, #1d3557 100%); color: white; border: none; padding: 0.625rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 600; white-space: nowrap; box-shadow: 0 4px 12px rgba(41, 98, 255, 0.3); transition: all 0.3s;">
                                        <i class="fas fa-check"></i> Marcar leída
                                    </a>
                                <?php endif; ?>
                                <a href="eliminar-notificacion.php?id=<?php echo $notif['id']; ?>" 
                                   onclick="return confirm('¿Estás seguro de que deseas eliminar esta notificación?');"
                                   style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 0.625rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 600; white-space: nowrap; transition: all 0.3s;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Estado vacío mejorado -->
            <div style="text-align: center; padding: 5rem 1.25rem; color: #6b7280;">
                <div style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.05) 0%, rgba(41, 98, 255, 0.1) 100%); width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bell-slash" style="font-size: 4rem; color: #2962FF; opacity: 0.3;"></i>
                </div>
                <p style="font-size: 1.25rem; margin: 0 0 0.5rem 0; color: #0a192f; font-weight: 600;">No tienes notificaciones</p>
                <p style="font-size: 0.875rem; margin: 0; color: #9ca3af;">Aquí aparecerán las actualizaciones sobre tus requisiciones</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
