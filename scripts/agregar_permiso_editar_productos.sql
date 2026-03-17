-- Agregar campo de permiso para editar productos en la tabla de permisos
-- Este script agrega el permiso 'editar_productos' a usuarios específicos

USE inventario_requisiciones;

-- Agregar permisos de edición de productos a todos los roles excepto solo_lectura
-- Los usuarios con este permiso podrán editar productos desde el dashboard

-- Ejemplo: Dar permiso de editar productos al usuario de tecnología (id = 5)
-- INSERT INTO permisos (usuario_id, modulo) VALUES (5, 'editar_productos');

-- Para dar el permiso a todos los usuarios de departamento:
-- INSERT INTO permisos (usuario_id, modulo)
-- SELECT id, 'editar_productos'
-- FROM usuarios
-- WHERE rol = 'departamento';

SELECT 'Script ejecutado. Usa el panel de admin para asignar permisos de edición de productos' as mensaje;
