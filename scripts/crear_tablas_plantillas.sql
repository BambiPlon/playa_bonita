-- Crear tabla de plantillas de requisicion
CREATE TABLE IF NOT EXISTS plantillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Crear tabla de productos de plantilla
-- Nota: No se usa FK en producto_id para evitar conflictos de tipo con la tabla productos
CREATE TABLE IF NOT EXISTS plantilla_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plantilla_id INT NOT NULL,
    producto_id INT NULL,
    nombre_custom VARCHAR(255) NULL,
    cantidad INT NOT NULL DEFAULT 1,
    unidad VARCHAR(50) NOT NULL,
    FOREIGN KEY (plantilla_id) REFERENCES plantillas(id) ON DELETE CASCADE,
    INDEX idx_plantilla (plantilla_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
