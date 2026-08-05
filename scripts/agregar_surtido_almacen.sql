-- Agregar columna para marcar productos surtidos desde almacén
ALTER TABLE requisicion_detalles 
ADD COLUMN IF NOT EXISTS surtido_almacen TINYINT(1) DEFAULT 0 COMMENT 'Indica si el producto fue surtido desde el almacén';

-- Agregar índice para búsquedas
CREATE INDEX IF NOT EXISTS idx_surtido_almacen ON requisicion_detalles(surtido_almacen);
