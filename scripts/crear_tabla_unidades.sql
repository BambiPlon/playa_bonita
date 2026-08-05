-- Crear tabla de unidades de medida
CREATE TABLE IF NOT EXISTS unidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    abreviatura VARCHAR(10),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar unidades predeterminadas
INSERT IGNORE INTO unidades (nombre, abreviatura) VALUES 
('pieza', 'pza'),
('unidad', 'und'),
('caja', 'cja'),
('paquete', 'paq'),
('bolsa', 'bls'),
('rollo', 'rll'),
('metro', 'm'),
('litro', 'lt'),
('galón', 'gal'),
('kilogramo', 'kg'),
('gramo', 'gr'),
('juego', 'jgo'),
('par', 'par'),
('docena', 'doc'),
('millar', 'mll'),
('tonelada', 'ton'),
('cubeta', 'cub'),
('bote', 'bte'),
('lata', 'lta'),
('botella', 'bot'),
('garrafón', 'grf'),
('servicio', 'srv');
