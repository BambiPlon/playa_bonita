-- Script para ocultar automáticamente las requisiciones aprobadas o en_gerencia_general
-- para los usuarios de gerencia y gerencia_general
-- Ejecutar después de crear la tabla requisiciones_ocultas

-- Ocultar requisiciones aprobadas para todos los usuarios de gerencia_general
INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id)
SELECT r.id, u.id
FROM requisiciones r
CROSS JOIN usuarios u
WHERE r.estado IN ('aprobada', 'completada')
AND u.rol = 'gerencia_general'
AND u.activo = 1;

-- Ocultar requisiciones aprobadas y en_gerencia_general para todos los usuarios de gerencia
INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id)
SELECT r.id, u.id
FROM requisiciones r
CROSS JOIN usuarios u
WHERE r.estado IN ('aprobada', 'completada', 'en_gerencia_general')
AND u.rol = 'gerencia'
AND u.activo = 1;

-- Verificar resultados
SELECT 'Requisiciones ocultas para gerencia:' as info, COUNT(*) as total
FROM requisiciones_ocultas ro
JOIN usuarios u ON ro.usuario_id = u.id
WHERE u.rol = 'gerencia';

SELECT 'Requisiciones ocultas para gerencia_general:' as info, COUNT(*) as total
FROM requisiciones_ocultas ro
JOIN usuarios u ON ro.usuario_id = u.id
WHERE u.rol = 'gerencia_general';
