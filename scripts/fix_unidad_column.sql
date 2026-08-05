-- Verificar y agregar columna unidad a requisicion_detalles si no existe

-- Primero verificamos si la columna existe
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = DATABASE() 
               AND TABLE_NAME = 'requisicion_detalles' 
               AND COLUMN_NAME = 'unidad');

-- Si no existe, la agregamos
SET @sql := IF(@exist = 0, 
    'ALTER TABLE requisicion_detalles ADD COLUMN unidad VARCHAR(50) DEFAULT NULL AFTER cantidad',
    'SELECT "La columna unidad ya existe" as mensaje');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mostrar estructura actual de la tabla
DESCRIBE requisicion_detalles;
