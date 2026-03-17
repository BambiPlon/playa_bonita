-- Agregar columna 'oculta' a la tabla requisiciones si no existe
ALTER TABLE requisiciones 
ADD COLUMN IF NOT EXISTS oculta TINYINT(1) DEFAULT 0
AFTER agregado_a_inventario;

-- Crear índice para mejorar consultas con filtro de ocultas
CREATE INDEX IF NOT EXISTS idx_requisiciones_oculta ON requisiciones(oculta);

SELECT 'Columna oculta agregada exitosamente a la tabla requisiciones' as mensaje;
