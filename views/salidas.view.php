<?php require 'includes/header.php'; ?>

<main class="main-content" style="width: 100%; padding: 20px; background: #f9fafb; min-height: 100vh;">
    <!-- Cambiando degradado verde a azul marino -->
    <div style="background: white; border: 1px solid rgba(41, 98, 255, 0.2); color: #1f2937; padding: 25px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="margin: 0 0 8px 0; font-size: 28px; display: flex; align-items: center; gap: 12px; color: #111827;">
                <div style="background: rgba(41, 98, 255, 0.1); padding: 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-box-open" style="color: #2962FF; font-size: 24px;"></i>
                </div>
                Salidas de Almacén
            </h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Historial y registro de movimientos de salida</p>
        </div>
        <a href="nueva-salida.php" class="btn btn-primary" style="background: #2962FF; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 12px rgba(41, 98, 255, 0.3);">
            <i class="fas fa-plus"></i> Nueva Salida
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>" style="margin-bottom: 20px; border-radius: 8px;">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Card con diseño minimalista moderno -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="padding: 24px 30px; border-bottom: 1px solid #e5e7eb;">
            <h2 style="margin: 0; font-size: 20px; color: #111827; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                <i class="fas fa-history" style="color: #2962FF;"></i> Historial de Salidas
            </h2>
        </div>
        <div style="padding: 0;">
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Folio</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Fecha</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Producto</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Cantidad</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Sub-Almacén</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Usuario</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Destino</th>
                            <th style="padding: 16px; text-align: center; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($salidas) > 0): ?>
                            <?php foreach ($salidas as $salida): ?>
                                <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 16px;">
                                        <span style="color: #2962FF; font-weight: 600; font-size: 14px;">
                                            <?php echo htmlspecialchars($salida['folio']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px; color: #111827; font-size: 14px;">
                                        <?php echo date('d/m/Y', strtotime($salida['fecha_salida'])); ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <strong style="color: #111827; font-size: 14px;">
                                            <?php echo htmlspecialchars($salida['producto_nombre']); ?>
                                        </strong>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span style="color: #2962FF; font-weight: 600; font-size: 16px;">
                                            <?php echo $salida['cantidad']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span style="display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: rgba(41, 98, 255, 0.1); color: #2962FF;">
                                            <?php echo htmlspecialchars($salida['sub_almacen_nombre']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px; color: #6b7280; font-size: 14px;">
                                        <?php echo htmlspecialchars($salida['usuario_nombre']); ?>
                                    </td>
                                    <td style="padding: 16px; color: #111827; font-size: 14px;">
                                        <?php echo htmlspecialchars($salida['destino']); ?>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <a href="generar-pdf-salida.php?id=<?php echo $salida['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           target="_blank"
                                           style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #ef4444; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s; border: none;"
                                           onmouseover="this.style.background='#dc2626'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)'"
                                           onmouseout="this.style.background='#ef4444'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 60px; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                                    <span style="font-size: 16px; color: #6b7280;">No hay salidas registradas</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
