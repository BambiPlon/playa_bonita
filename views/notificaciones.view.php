<?php require 'includes/header.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Mis Notificaciones</h1>
        <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Centro de notificaciones y actualizaciones</p>
    </div>

    <div class="card">
        <?php if (count($notificaciones) > 0): ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($notificaciones as $notif): ?>
                    <div style="display: flex; justify-content: space-between; align-items: start; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); background: <?php echo $notif['leida'] ? 'transparent' : 'var(--primary-light)'; ?>; transition: background 0.15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='<?php echo $notif['leida'] ? 'transparent' : 'var(--primary-light)'; ?>'">
                        <div style="flex: 1; display: flex; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: <?php 
                                if ($notif['tipo'] === 'aprobacion') echo 'var(--success-light)';
                                elseif ($notif['tipo'] === 'rechazo') echo 'var(--danger-light)';
                                else echo 'var(--primary-light)';
                            ?>;">
                                <i class="fas fa-<?php echo $notif['tipo'] === 'aprobacion' ? 'check-circle' : ($notif['tipo'] === 'rechazo' ? 'times-circle' : 'info-circle'); ?>" style="color: <?php 
                                    if ($notif['tipo'] === 'aprobacion') echo 'var(--success)';
                                    elseif ($notif['tipo'] === 'rechazo') echo 'var(--danger)';
                                    else echo 'var(--primary)';
                                ?>; font-size: 0.875rem;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0 0 4px 0; color: var(--gray-900); font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <?php echo htmlspecialchars($notif['titulo']); ?>
                                    <?php if (!$notif['leida']): ?>
                                        <span class="badge badge-warning" style="font-size: 0.625rem;">Nueva</span>
                                    <?php endif; ?>
                                </h3>
                                <p style="margin: 0 0 4px 0; color: var(--text-muted); font-size: 0.8125rem; line-height: 1.5;"><?php echo htmlspecialchars($notif['mensaje']); ?></p>
                                <?php if ($notif['requisicion_folio']): ?>
                                    <span style="color: var(--primary); font-size: 0.75rem; font-weight: 600;"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($notif['requisicion_folio']); ?></span>
                                <?php endif; ?>
                                <p style="margin: 4px 0 0 0; font-size: 0.6875rem; color: var(--gray-400);"><i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 4px; flex-shrink: 0;">
                            <?php if (!$notif['leida']): ?>
                                <a href="notificaciones.php?marcar_leida=<?php echo $notif['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <a href="eliminar-notificacion.php?id=<?php echo $notif['id']; ?>" onclick="return confirm('Eliminar esta notificacion?');" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem; color: var(--gray-400);">
                <i class="fas fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                <p style="margin: 0 0 4px 0; font-size: 0.9375rem; font-weight: 500; color: var(--gray-900);">No tienes notificaciones</p>
                <p style="margin: 0; font-size: 0.8125rem; color: var(--text-muted);">Aqui apareceran las actualizaciones sobre tus requisiciones</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
