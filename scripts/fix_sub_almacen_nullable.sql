-- Script para permitir NULL en el campo sub_almacen_id de requisiciones
-- Esto permite que usuarios sin sub-almacén asignado puedan crear requisiciones

USE inventario_requisiciones;

-- Eliminar la foreign key constraint existente
ALTER TABLE requisiciones 
DROP FOREIGN KEY requisiciones_ibfk_1;

-- Modificar la columna para permitir NULL
ALTER TABLE requisiciones 
MODIFY COLUMN sub_almacen_id INT NULL;

-- Volver a crear la foreign key constraint permitiendo NULL
ALTER TABLE requisiciones 
ADD CONSTRAINT requisiciones_ibfk_1 
FOREIGN KEY (sub_almacen_id) 
REFERENCES sub_almacenes(id)
ON DELETE SET NULL
ON UPDATE CASCADE;
