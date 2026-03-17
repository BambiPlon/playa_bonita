<?php require 'includes/header.php'; ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Salidas de Almacen</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Historial y registro de movimientos de salida</p>
        </div>
        <a href="nueva-salida.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Salida</a>
    </div>

    <?php if ($mensaje): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; <?php echo $tipo_mensaje === 'success' ? 'background: var(--success-light); border: 1px solid #bbf7d0; color: #166534;' : 'background: var(--danger-light); border: 1px solid #fecaca; color: #991b1b;'; ?>">
            <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <span style="font-weight: 500; font-size: 0.875rem;"><?php echo htmlspecialchars($mensaje); ?></span>
        </div>
    <?php endif; ?>

    <div class="card" style="overflow: hidden;">
        <div style="padding: 1.125rem 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h2 style="margin: 0; font-size: 1.0625rem; color: var(--gray-900); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-history" style="color: var(--primary);"></i> Historial de Salidas
            </h2>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--gray-50);">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Folio</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Fecha</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Producto</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Cantidad</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Sub-Almacen</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Usuario</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Destino</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--gray-500); font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($salidas) > 0): ?>
                        <?php foreach ($salidas as $salida): ?>
                            <tr style="border-bottom: 1px solid var(--gray-100); transition: background 0.15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.75rem 1rem;"><span style="color: var(--primary); font-weight: 600; font-size: 0.8125rem;"><?php echo htmlspecialchars($salida['folio']); ?></span></td>
                                <td style="padding: 0.75rem 1rem; color: var(--gray-900); font-size: 0.8125rem;"><?php echo date('d/m/Y', strtotime($salida['fecha_salida'])); ?></td>
                                <td style="padding: 0.75rem 1rem;"><strong style="color: var(--gray-900); font-size: 0.8125rem;"><?php echo htmlspecialchars($salida['producto_nombre']); ?></strong></td>
                                <td style="padding: 0.75rem 1rem;"><span style="color: var(--primary); font-weight: 700; font-size: 0.9375rem;"><?php echo $salida['cantidad']; ?></span></td>
                                <td style="padding: 0.75rem 1rem;"><span class="badge badge-primary"><?php echo htmlspecialchars($salida['sub_almacen_nombre']); ?></span></td>
                                <td style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.8125rem;"><?php echo htmlspecialchars($salida['usuario_nombre']); ?></td>
                                <td style="padding: 0.75rem 1rem; color: var(--gray-900); font-size: 0.8125rem;"><?php echo htmlspecialchars($salida['destino']); ?></td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <a href="generar-pdf-salida.php?id=<?php echo $salida['id']; ?>" class="btn btn-sm btn-danger" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 4rem; color: var(--gray-400);">
                                <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                <span style="font-size: 0.9375rem; font-weight: 500;">No hay salidas registradas</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
