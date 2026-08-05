-- Script para implementar ocultamiento de requisiciones por usuario en lugar de global
-- Esto permite que compras oculte una requisición sin afectar al usuario creador

-- Crear tabla para tracking de requisiciones ocultas por usuario
CREATE TABLE IF NOT EXISTS requisiciones_ocultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requisicion_id INT NOT NULL,
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_req_user (requisicion_id, usuario_id),
    FOREIGN KEY (requisicion_id) REFERENCES requisiciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Migrar datos existentes de requisiciones ocultas (si hay alguna)
-- Las requisiciones marcadas como ocultas se mantendrán ocultas para todos los usuarios que las ocultaron
INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id)
SELECT r.id, r.usuario_id
FROM requisiciones r
WHERE r.oculta = 1;
