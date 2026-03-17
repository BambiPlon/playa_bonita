-- Agregar campo activo a la tabla de inventario si no existe

USE inventario_requisiciones;

-- Verificar y agregar el campo activo
ALTER TABLE inventario 
ADD COLUMN IF NOT EXISTS activo TINYINT(1) DEFAULT 1 AFTER stock_minimo;

SELECT 'Campo activo agregado a la tabla inventario' as mensaje;
