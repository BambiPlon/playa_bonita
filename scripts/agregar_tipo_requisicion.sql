-- Agregar campo tipo_requisicion a la tabla requisiciones
ALTER TABLE requisiciones 
ADD COLUMN tipo_requisicion ENUM('producto', 'servicio') DEFAULT 'producto' AFTER folio;

-- Agregar índice para el tipo
ALTER TABLE requisiciones ADD INDEX idx_tipo_requisicion (tipo_requisicion);
