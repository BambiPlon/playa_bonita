-- Agregando campo para ocultar requisiciones rechazadas
ALTER TABLE requisiciones ADD COLUMN IF NOT EXISTS oculta TINYINT(1) DEFAULT 0;
