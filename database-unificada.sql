-- =============================================
-- BASE DE DATOS UNIFICADA
-- Sistema de Inventario y Requisiciones
-- Hotel Playa Bonita
-- =============================================

DROP DATABASE IF EXISTS inventario_requisiciones;
CREATE DATABASE inventario_requisiciones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_requisiciones;

-- =============================================
-- TABLAS PRINCIPALES
-- =============================================

-- Tabla de sub-almacenes (incluyendo almacén general)
CREATE TABLE sub_almacenes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200) NOT NULL,
    contacto VARCHAR(200),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    rfc VARCHAR(20),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios con roles completos y sistema de permisos
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    rol ENUM('admin', 'departamento', 'solo_lectura', 'compras', 'gerencia', 'gerencia_general') DEFAULT 'solo_lectura',
    sub_almacen_id INT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sub_almacen_id) REFERENCES sub_almacenes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos personalizados por usuario
CREATE TABLE permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY usuario_modulo (usuario_id, modulo),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos en inventario
CREATE TABLE inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    sub_almacen_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    unidad VARCHAR(50) NOT NULL,
    precio_unitario DECIMAL(10,2),
    stock_minimo INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sub_almacen_id) REFERENCES sub_almacenes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de requisiciones con flujo completo de aprobación
CREATE TABLE requisiciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(50) UNIQUE NOT NULL,
    sub_almacen_id INT NOT NULL,
    usuario_id INT NOT NULL,
    solicitante VARCHAR(200) NOT NULL,
    fecha_solicitud DATE NOT NULL,
    estado ENUM('pendiente', 'en_compras', 'en_gerencia', 'en_gerencia_general', 'aprobada', 'rechazada', 'completada') DEFAULT 'pendiente',
    observaciones TEXT,
    justificacion_rechazo TEXT NULL,
    monto_cotizado DECIMAL(10,2) NULL,
    fecha_cotizacion TIMESTAMP NULL,
    cotizado_por INT NULL,
    aprobado_por INT NULL,
    aprobado_por_general INT NULL,
    fecha_aprobacion TIMESTAMP NULL,
    fecha_aprobacion_general TIMESTAMP NULL,
    agregado_a_inventario TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sub_almacen_id) REFERENCES sub_almacenes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (cotizado_por) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por_general) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de requisiciones con cotización y aprobación por artículo
CREATE TABLE requisicion_detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    requisicion_id INT NOT NULL,
    producto_id INT NULL,
    producto_nombre VARCHAR(200) NOT NULL,
    cantidad INT NOT NULL,
    unidad VARCHAR(50) NOT NULL,
    justificacion TEXT,
    precio_cotizado DECIMAL(10,2) NULL,
    proveedor_id INT NULL,
    aprobado TINYINT(1) DEFAULT 1,
    justificacion_rechazo TEXT NULL,
    FOREIGN KEY (requisicion_id) REFERENCES requisiciones(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES inventario(id) ON DELETE SET NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    requisicion_id INT NULL,
    leida TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (requisicion_id) REFERENCES requisiciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de salidas de almacén
CREATE TABLE salidas_almacen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(50) UNIQUE NOT NULL,
    usuario_id INT NOT NULL,
    sub_almacen_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    motivo TEXT NOT NULL,
    destino VARCHAR(200),
    fecha_salida DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (sub_almacen_id) REFERENCES sub_almacenes(id),
    FOREIGN KEY (producto_id) REFERENCES inventario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DATOS DE EJEMPLO
-- =============================================

-- Insertar sub-almacenes (incluyendo almacén general)
INSERT INTO sub_almacenes (id, nombre, descripcion) VALUES
(100, 'Almacén General', 'Almacén central que contiene todos los productos del sistema'),
(1, 'Tecnología', 'Equipos de cómputo, software y accesorios tecnológicos'),
(2, 'Ama de Llaves', 'Artículos de limpieza y mantenimiento'),
(3, 'Administración', 'Papelería y suministros de oficina'),
(4, 'Marketing', 'Material publicitario y promocional'),
(5, 'Poobar', 'Suministros para bar y bebidas'),
(6, 'Tiendita', 'Productos de venta al público'),
(7, 'Seguridad', 'Equipos y suministros de seguridad'),
(8, 'Recepción', 'Suministros para área de recepción');

-- Insertar proveedores de ejemplo
INSERT INTO proveedores (nombre, contacto, telefono, email, rfc, activo) VALUES
('Proveedor General', 'Juan Pérez', '1234567890', 'contacto@proveedor1.com', 'PEGJ850101ABC', 1),
('Suministros Tecnológicos', 'María García', '0987654321', 'ventas@sumitech.com', 'SUTM900202DEF', 1),
('Limpieza Total', 'Carlos López', '5551234567', 'info@limpiezatotal.com', 'LITC950303GHI', 1);

-- Insertar usuarios (password: 123456 para todos)
INSERT INTO usuarios (username, password, nombre_completo, email, rol, sub_almacen_id) VALUES
('admin', '123456', 'Administrador General', 'admin@playabonita.com', 'admin', NULL),
('compras', '123456', 'Departamento de Compras', 'compras@playabonita.com', 'compras', NULL),
('gerencia', '123456', 'Gerencia', 'gerencia@playabonita.com', 'gerencia', NULL),
('gerencia_general', '123456', 'Gerencia General', 'gerencia.general@playabonita.com', 'gerencia_general', NULL),
('tecnologia', '123456', 'Jefe de Tecnología', 'tecnologia@playabonita.com', 'departamento', 1),
('ama_llaves', '123456', 'Ama de Llaves', 'llaves@playabonita.com', 'departamento', 2),
('administracion', '123456', 'Jefe de Administración', 'administracion@playabonita.com', 'departamento', 3),
('marketing', '123456', 'Jefe de Marketing', 'marketing@playabonita.com', 'departamento', 4),
('poobar', '123456', 'Encargado Poobar', 'poobar@playabonita.com', 'departamento', 5),
('tiendita', '123456', 'Encargado Tiendita', 'tiendita@playabonita.com', 'departamento', 6),
('seguridad', '123456', 'Jefe de Seguridad', 'seguridad@playabonita.com', 'departamento', 7),
('recepcion', '123456', 'Recepcionista', 'recepcion@playabonita.com', 'solo_lectura', 8);

-- Datos de ejemplo para inventario
INSERT INTO inventario (codigo, nombre, descripcion, sub_almacen_id, cantidad, unidad, precio_unitario, stock_minimo) VALUES
-- Tecnología
('TEC-001', 'Mouse inalámbrico', 'Mouse óptico inalámbrico', 1, 25, 'pieza', 150.00, 10),
('TEC-002', 'Teclado USB', 'Teclado estándar USB', 1, 15, 'pieza', 200.00, 5),
('TEC-003', 'Monitor 24 pulgadas', 'Monitor LED Full HD', 1, 8, 'pieza', 2500.00, 3),
-- Ama de Llaves
('AMA-001', 'Cloro 1L', 'Cloro desinfectante', 2, 50, 'litro', 25.00, 20),
('AMA-002', 'Trapeador', 'Trapeador de microfibra', 2, 10, 'pieza', 80.00, 5),
('AMA-003', 'Jabón líquido', 'Jabón antibacterial 5L', 2, 30, 'litro', 120.00, 10),
-- Administración
('ADM-001', 'Papel bond carta', 'Resma de papel bond', 3, 100, 'resma', 120.00, 30),
('ADM-002', 'Pluma azul', 'Pluma de tinta azul', 3, 200, 'pieza', 5.00, 50),
('ADM-003', 'Engrapadora', 'Engrapadora metálica', 3, 12, 'pieza', 85.00, 5),
-- Marketing
('MKT-001', 'Volantes', 'Volantes publicitarios', 4, 5000, 'pieza', 0.50, 1000),
('MKT-002', 'Banners', 'Banner impreso 1x2m', 4, 13, 'pieza', 350.00, 5),
-- Poobar
('POO-001', 'Cerveza lata', 'Cerveza en lata 355ml', 5, 300, 'pieza', 18.00, 100),
('POO-002', 'Hielo', 'Bolsa de hielo 5kg', 5, 40, 'bolsa', 25.00, 15),
-- Tiendita
('TIE-001', 'Refresco 600ml', 'Refresco embotellado', 6, 150, 'pieza', 15.00, 50),
('TIE-002', 'Galletas', 'Paquete de galletas', 6, 80, 'paquete', 12.00, 30),
-- Seguridad
('SEG-001', 'Linterna LED', 'Linterna recargable', 7, 8, 'pieza', 250.00, 5),
('SEG-002', 'Radio comunicador', 'Radio portátil', 7, 6, 'pieza', 1200.00, 3),
-- Recepción
('REC-001', 'Folder tamaño carta', 'Folder de cartón', 8, 80, 'pieza', 3.00, 30),
('REC-002', 'Clips', 'Caja de clips', 8, 25, 'caja', 15.00, 10);

-- Requisiciones de ejemplo
INSERT INTO requisiciones (folio, sub_almacen_id, usuario_id, solicitante, fecha_solicitud, estado) VALUES
('REQ-2025-001', 1, 5, 'Jefe de Tecnología', '2025-01-15', 'pendiente'),
('REQ-2025-002', 2, 6, 'Ama de Llaves', '2025-01-18', 'en_compras');

-- Detalles de requisiciones de ejemplo
INSERT INTO requisicion_detalles (requisicion_id, producto_id, producto_nombre, cantidad, unidad, justificacion) VALUES
(1, 1, 'Mouse inalámbrico', 5, 'pieza', 'Para nuevos equipos de oficina'),
(1, 2, 'Teclado USB', 5, 'pieza', 'Para nuevos equipos de oficina'),
(2, 4, 'Cloro 1L', 20, 'litro', 'Reabastecimiento mensual'),
(2, 6, 'Jabón líquido', 10, 'litro', 'Reabastecimiento para áreas comunes');

-- Notificaciones de ejemplo
INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, leida) VALUES
(2, 'nueva_requisicion', 'Nueva Requisición', 'Se ha creado la requisición REQ-2025-001', 1, 0),
(2, 'nueva_requisicion', 'Nueva Requisición', 'Se ha creado la requisición REQ-2025-002', 2, 0);

-- Salidas de almacén de ejemplo
INSERT INTO salidas_almacen (folio, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) VALUES
('SAL-2025-001', 5, 1, 1, 3, 'Entrega a departamento', 'Oficina principal', '2025-01-10'),
('SAL-2025-002', 6, 2, 4, 10, 'Uso en limpieza', 'Áreas comunes', '2025-01-12');

-- =============================================
-- FIN DE SCRIPT
-- =============================================

SELECT 'Base de datos unificada creada exitosamente' as mensaje;
