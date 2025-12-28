<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_nombre'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/../controllers/NotificacionController.php';

$notificacionController = new NotificacionController();
$no_leidas = $notificacionController->contarNoLeidas($_SESSION['user_id']);
$notificaciones_header = $notificacionController->listar($_SESSION['user_id'], false);
$notificaciones_header = array_slice($notificaciones_header, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo $pageTitle ?? 'Sistema de Inventario'; ?></title>

    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ======== NOTIFICACIONES (MISMO DISEÑO) ======== */
        .notifications-dropdown { position: relative; margin-right: 15px; }

        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .notification-bell:hover { background-color: rgba(16, 185, 129, 0.1); }

        .notification-bell i { color: #6b7280; font-size: 20px; }

        .notification-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #ef4444;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .notifications-panel {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            width: 380px;
            max-height: 500px;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .notifications-panel.show { display: block; }

        .notifications-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h3 {
            margin: 0;
            font-size: 16px;
            color: #1f2937;
        }

        .mark-all-read {
            color: #10b981;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .mark-all-read:hover { text-decoration: underline; }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
        }

        .notification-item:hover { background-color: #f9fafb; }

        .notification-item.unread {
            background-color: rgba(16, 185, 129, 0.05);
            border-left: 3px solid #10b981;
        }

        .notification-item-content {
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 185, 129, 0.1);
            flex-shrink: 0;
        }

        .notification-icon i { color: #10b981; font-size: 16px; }

        .notification-text { flex: 1; }

        .notification-text h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
        }

        .notification-text p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .notification-delete {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #9ca3af;
            font-size: 14px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .notification-item:hover .notification-delete { opacity: 1; }

        .notification-delete:hover { color: #ef4444; }

        .notifications-empty {
            padding: 60px 20px;
            text-align: center;
            color: #9ca3af;
        }

        .notifications-empty i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .view-all-link {
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            display: block;
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .view-all-link:hover { background-color: #f9fafb; }
    </style>
</head>

<body>

<div class="header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo">
            <i class="fas fa-boxes"></i>
            Sistema de Inventario
        </div>
    </div>

    <div class="header-right">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Buscar productos, requisiciones...">
        </div>

        <div class="notifications-dropdown">
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <?php if ($no_leidas > 0): ?>
                    <span class="notification-badge"><?php echo $no_leidas; ?></span>
                <?php endif; ?>
            </div>

            <div class="notifications-panel" id="notificationsPanel">
                <div class="notifications-header">
                    <h3>Notificaciones</h3>
                    <?php if ($no_leidas > 0): ?>
                        <a href="marcar-todas-leidas.php" class="mark-all-read">Marcar todas como leídas</a>
                    <?php endif; ?>
                </div>

                <div class="notifications-list">
                    <?php if (count($notificaciones_header) > 0): ?>
                        <?php foreach ($notificaciones_header as $notif): ?>
                            <div class="notification-item <?php echo !$notif['leida'] ? 'unread' : ''; ?>"
                                 onclick="marcarLeidaYRedirigir(
                                    <?php echo (int)$notif['id']; ?>,
                                    '<?php echo $notif['requisicion_id'] ? 'ver-requisicion.php?id=' . (int)$notif['requisicion_id'] : 'notificaciones.php'; ?>'
                                 )">
                                <div class="notification-item-content">
                                    <div class="notification-icon">
                                        <i class="fas fa-<?php echo $notif['tipo'] === 'aprobacion' ? 'check-circle' : ($notif['tipo'] === 'rechazo' ? 'times-circle' : 'info-circle'); ?>"></i>
                                    </div>

                                    <div class="notification-text">
                                        <h4><?php echo htmlspecialchars($notif['titulo']); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($notif['mensaje'], 0, 60)) . (strlen($notif['mensaje']) > 60 ? '...' : ''); ?></p>

                                        <?php if (!empty($notif['requisicion_folio'])): ?>
                                            <span style="font-size: 12px; color: #10b981; font-weight: 600;">
                                                #<?php echo htmlspecialchars($notif['requisicion_folio']); ?>
                                            </span>
                                        <?php endif; ?>

                                        <div class="notification-time">
                                            <?php
                                            $time_diff = time() - strtotime($notif['created_at']);
                                            if ($time_diff < 60) echo 'Hace un momento';
                                            elseif ($time_diff < 3600) echo 'Hace ' . floor($time_diff / 60) . ' minutos';
                                            elseif ($time_diff < 86400) echo 'Hace ' . floor($time_diff / 3600) . ' horas';
                                            else echo date('d/m/Y', strtotime($notif['created_at']));
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <i class="fas fa-times notification-delete"
                                   onclick="event.stopPropagation(); eliminarNotificacion(<?php echo (int)$notif['id']; ?>);"></i>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notifications-empty">
                            <i class="fas fa-bell-slash"></i>
                            <p>No tienes notificaciones</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($notificaciones_header) > 0): ?>
                    <a href="notificaciones.php" class="view-all-link">Ver todas las notificaciones</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="user-info" onclick="toggleUserDropdown()">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario'); ?></span>
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 2)); ?>
            </div>

            <div class="user-dropdown" id="userDropdown">
                <a href="logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
