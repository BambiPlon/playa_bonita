<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar sesión antes de continuar
if (!isset($_SESSION['user_nombre'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/../controllers/NotificacionController.php';

// Obtener notificaciones
$notificacionController = new NotificacionController();
$no_leidas = $notificacionController->contarNoLeidas($_SESSION['user_id']);
$notificaciones_header = $notificacionController->listar($_SESSION['user_id'], false);
$notificaciones_header = array_slice($notificaciones_header, 0, 5);
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo $pageTitle ?? 'Sistema de Inventario'; ?></title>
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="public/icon-dark-32x32.png">
    <link rel="apple-touch-icon" href="public/apple-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Notificaciones dropdown */
        .notifications-dropdown {
            position: relative;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 10px;
            transition: background 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
        }
        
        .notification-bell:hover {
            background: var(--gray-100);
        }
        
        .notification-bell i {
            color: var(--gray-500);
            font-size: 1rem;
        }
        
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: var(--danger);
            color: white;
            border-radius: 9999px;
            padding: 1px 5px;
            font-size: 10px;
            font-weight: 700;
            min-width: 16px;
            text-align: center;
            line-height: 1.4;
        }
        
        .notifications-panel {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 380px;
            max-height: 480px;
            overflow-y: auto;
            background: white;
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
            z-index: 1000;
            border: 1px solid var(--gray-200);
        }
        
        .notifications-panel.show {
            display: block;
        }
        
        .notifications-header {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notifications-header h3 {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .mark-all-read {
            color: var(--primary);
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }
        
        .mark-all-read:hover {
            text-decoration: underline;
        }
        
        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            cursor: pointer;
            transition: background 0.15s ease;
            position: relative;
        }
        
        .notification-item:hover {
            background: var(--gray-50);
        }
        
        .notification-item.unread {
            background: var(--primary-50);
            border-left: 3px solid var(--primary);
        }
        
        .notification-item-content {
            display: flex;
            align-items: start;
            gap: 0.75rem;
        }
        
        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-100);
            flex-shrink: 0;
        }
        
        .notification-icon i {
            color: var(--primary);
            font-size: 0.8125rem;
        }
        
        .notification-text {
            flex: 1;
            min-width: 0;
        }
        
        .notification-text h4 {
            margin: 0 0 2px 0;
            font-size: 0.8125rem;
            color: var(--gray-900);
            font-weight: 600;
        }
        
        .notification-text p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--gray-500);
            line-height: 1.4;
        }
        
        .notification-time {
            font-size: 0.6875rem;
            color: var(--gray-400);
            margin-top: 4px;
        }
        
        .notification-delete {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            color: var(--gray-400);
            font-size: 0.75rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.15s;
        }
        
        .notification-item:hover .notification-delete {
            opacity: 1;
        }
        
        .notification-delete:hover {
            color: var(--danger);
        }
        
        .notifications-empty {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--gray-400);
        }
        
        .notifications-empty i {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            opacity: 0.4;
        }
        
        .notifications-empty p {
            font-size: 0.8125rem;
        }
        
        .view-all-link {
            padding: 0.75rem 1rem;
            text-align: center;
            border-top: 1px solid var(--gray-100);
            display: block;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8125rem;
        }
        
        .view-all-link:hover {
            background: var(--gray-50);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Alternar menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <i class="fas fa-cubes"></i>
                <span>Sistema de Inventario</span>
            </div>
        </div>
        
        <div class="header-right">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Buscar..." aria-label="Buscar">
            </div>
            
            <div class="notifications-dropdown">
                <div class="notification-bell" onclick="toggleNotifications()" aria-label="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <?php if ($no_leidas > 0): ?>
                        <span class="notification-badge"><?php echo $no_leidas; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="notifications-panel" id="notificationsPanel">
                    <div class="notifications-header">
                        <h3>Notificaciones</h3>
                        <?php if ($no_leidas > 0): ?>
                            <a href="marcar-todas-leidas.php" class="mark-all-read">Marcar todas leidas</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="notifications-list">
                        <?php if (count($notificaciones_header) > 0): ?>
                            <?php foreach ($notificaciones_header as $notif): ?>
                                <div class="notification-item <?php echo !$notif['leida'] ? 'unread' : ''; ?>" 
                                     onclick="marcarLeidaYRedirigir(<?php echo $notif['id']; ?>, '<?php echo $notif['requisicion_id'] ? 'ver-requisicion.php?id=' . $notif['requisicion_id'] : 'notificaciones.php'; ?>')">
                                    <div class="notification-item-content">
                                        <div class="notification-icon">
                                            <i class="fas fa-<?php echo $notif['tipo'] === 'aprobacion' ? 'check-circle' : ($notif['tipo'] === 'rechazo' ? 'times-circle' : 'info-circle'); ?>"></i>
                                        </div>
                                        <div class="notification-text">
                                            <h4><?php echo htmlspecialchars($notif['titulo']); ?></h4>
                                            <p><?php echo htmlspecialchars(substr($notif['mensaje'], 0, 60)) . (strlen($notif['mensaje']) > 60 ? '...' : ''); ?></p>
                                            <?php if ($notif['requisicion_folio']): ?>
                                                <span style="font-size: 11px; color: var(--primary); font-weight: 600;">
                                                    #<?php echo htmlspecialchars($notif['requisicion_folio']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <div class="notification-time">
                                                <?php 
                                                $time_diff = time() - strtotime($notif['created_at']);
                                                if ($time_diff < 60) {
                                                    echo 'Hace un momento';
                                                } elseif ($time_diff < 3600) {
                                                    echo 'Hace ' . floor($time_diff / 60) . ' min';
                                                } elseif ($time_diff < 86400) {
                                                    echo 'Hace ' . floor($time_diff / 3600) . 'h';
                                                } else {
                                                    echo date('d/m/Y', strtotime($notif['created_at']));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="fas fa-times notification-delete" 
                                       onclick="event.stopPropagation(); eliminarNotificacion(<?php echo $notif['id']; ?>);"></i>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notifications-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>Sin notificaciones</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($notificaciones_header) > 0): ?>
                        <a href="notificaciones.php" class="view-all-link">Ver todas</a>
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
                        Cerrar Sesion
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <?php require_once __DIR__ . '/sidebar.php'; ?>
