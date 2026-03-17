-- Agregar campo para almacenar el porcentaje de IVA aplicado
ALTER TABLE requisiciones ADD COLUMN porcentaje_iva DECIMAL(5,2) DEFAULT 16.00 AFTER monto_cotizado;
