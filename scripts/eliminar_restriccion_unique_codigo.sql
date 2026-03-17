-- Eliminar la restricción UNIQUE del código para permitir mismo producto en diferentes almacenes
ALTER TABLE inventario DROP INDEX IF EXISTS codigo;

-- Crear índice compuesto único para código + sub_almacen_id
-- Esto permite el mismo código en diferentes almacenes, pero no duplicados en el mismo almacén
ALTER TABLE inventario ADD UNIQUE KEY unique_codigo_almacen (codigo, sub_almacen_id);
