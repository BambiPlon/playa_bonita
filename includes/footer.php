</main>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('collapsed');
    
    // En mobile, mostrar/ocultar overlay
    if (window.innerWidth <= 768) {
        overlay.classList.toggle('active');
        // Prevenir scroll del body cuando el sidebar está abierto
        document.body.style.overflow = sidebar.classList.contains('collapsed') ? 'auto' : 'hidden';
    }
}

// Cerrar sidebar (para mobile)
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('collapsed');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Toggle dropdown de usuario
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('active');
}

// Toggle dropdown de notificaciones
function toggleNotifications() {
    const panel = document.getElementById('notificationsPanel');
    const userDropdown = document.getElementById('userDropdown');
    
    // Cerrar el dropdown de usuario si está abierto
    userDropdown.classList.remove('active');
    
    panel.classList.toggle('show');
}

// Marcar notificación como leída y redirigir
function marcarLeidaYRedirigir(notificacionId, url) {
    // Marcar como leída
    fetch('marcar-notificacion-leida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notificacion_id=' + notificacionId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirigir a la URL
            window.location.href = url;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Redirigir de todos modos
        window.location.href = url;
    });
}

// Función para confirmar eliminación
function confirmarEliminacion(mensaje = '¿Estás seguro de eliminar este elemento?') {
    return Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
}

// Función para confirmar acción
function confirmarAccion(titulo, mensaje, icono = 'question') {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: icono,
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });
}

// Función para mostrar alerta de éxito
function alertaExito(mensaje, titulo = 'Éxito') {
    return Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje,
        confirmButtonColor: '#10b981',
        timer: 3000,
        timerProgressBar: true
    });
}

// Función para mostrar alerta de error
function alertaError(mensaje, titulo = 'Error') {
    return Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensaje,
        confirmButtonColor: '#ef4444'
    });
}

// Eliminar notificación con confirmación mejorada
function eliminarNotificacion(notificacionId) {
    Swal.fire({
        title: '¿Eliminar notificación?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'eliminar-notificacion.php?id=' + notificacionId;
        }
    });
}

// Cerrar dropdowns al hacer clic fuera
document.addEventListener('click', function(event) {
    const userInfo = document.querySelector('.user-info');
    const userDropdown = document.getElementById('userDropdown');
    const notificationBell = document.querySelector('.notification-bell');
    const notificationsPanel = document.getElementById('notificationsPanel');
    
    // Cerrar dropdown de usuario
    if (userInfo && userDropdown && !userInfo.contains(event.target)) {
        userDropdown.classList.remove('active');
    }
    
    // Cerrar panel de notificaciones
    if (notificationBell && notificationsPanel && 
        !notificationBell.contains(event.target) && 
        !notificationsPanel.contains(event.target)) {
        notificationsPanel.classList.remove('show');
    }
});

// Cerrar sidebar en mobile al cambiar de tamaño de ventana
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth > 768) {
        sidebar.classList.remove('collapsed');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    } else {
        sidebar.classList.add('collapsed');
        overlay.classList.remove('active');
    }
});

// Inicializar estado del sidebar según el tamaño de pantalla
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
    }
});

// Animación suave para las tablas
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.inventory-table tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

// Animación para las cards de estadísticas
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Confirmación para agregar productos al inventario desde requisición
document.addEventListener('DOMContentLoaded', function() {
    const botonesAgregarInventario = document.querySelectorAll('.agregar-inventario-btn');
    
    botonesAgregarInventario.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            const folio = this.dataset.reqFolio;
            
            Swal.fire({
                title: '¿Agregar al Almacén General?',
                html: `Se agregarán todos los productos aprobados de la requisición <strong>${folio}</strong> al Almacén General.<br><br>Los productos existentes se actualizarán y los nuevos se crearán automáticamente.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, agregar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    window.location.href = url;
                }
            });
        });
    });
});

// Mostrar mensajes de sesión si existen
<?php if (isset($_SESSION['mensaje'])): ?>
    Swal.fire({
        icon: '<?php echo ($_SESSION['tipo_mensaje'] === 'error' || $_SESSION['tipo_mensaje'] === 'danger') ? 'error' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'warning' : 'success'); ?>',
        title: '<?php echo ($_SESSION['tipo_mensaje'] === 'error' || $_SESSION['tipo_mensaje'] === 'danger') ? 'Error' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'Atención' : 'Éxito'); ?>',
        text: '<?php echo addslashes($_SESSION['mensaje']); ?>',
        confirmButtonColor: '<?php echo ($_SESSION['tipo_mensaje'] === 'error' || $_SESSION['tipo_mensaje'] === 'danger') ? '#ef4444' : ($_SESSION['tipo_mensaje'] === 'warning' ? '#f59e0b' : '#10b981'); ?>',
        timer: 4000,
        timerProgressBar: true
    });
    <?php 
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
    ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?php echo addslashes($_SESSION['success_message']); ?>',
        confirmButtonColor: '#10b981',
        timer: 4000,
        timerProgressBar: true
    });
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo addslashes($_SESSION['error_message']); ?>',
        confirmButtonColor: '#ef4444'
    });
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['warning_message'])): ?>
    Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: '<?php echo addslashes($_SESSION['warning_message']); ?>',
        confirmButtonColor: '#f59e0b'
    });
    <?php unset($_SESSION['warning_message']); ?>
<?php endif; ?>
</script>
</body>
</html>
