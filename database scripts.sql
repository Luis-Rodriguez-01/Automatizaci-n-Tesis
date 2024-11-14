use moduloslimdb;

CREATE TABLE blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT
);

CREATE TABLE variables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_id INT,
    name VARCHAR(255),
    value TEXT,
    FOREIGN KEY (block_id) REFERENCES blocks(id)
);

ALTER TABLE blocks ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE variables ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE blocks ADD COLUMN Activated BOOLEAN DEFAULT FALSE;
ALTER TABLE blocks DROP COLUMN Activated;
ALTER TABLE variables ADD COLUMN Informative BOOLEAN DEFAULT FALSE;
ALTER TABLE variables ADD COLUMN Optional BOOLEAN DEFAULT FALSE;
ALTER TABLE blocks ADD COLUMN Extra_Information BOOLEAN DEFAULT FALSE;

INSERT INTO blocks (name, description) VALUES
('Bloque de cabecera', 'Bloques para la cabecera del sitio'),
('Bloque de pie de página', 'Bloques para el pie de página del sitio'),
('Bloque lateral', 'Bloques para la barra lateral del sitio');

INSERT INTO variables (block_id, name, value) VALUES
(1, 'Título principal', 'Mi sitio web'),
(1, 'Subtítulo', 'Descripción de mi sitio'),
(2, 'Copyright', '© 2023'),
(3, 'Banner', 'Imagen del banner'),
(3, 'Texto del banner', '¡Suscríbete a nuestro boletín!');

SELECT * FROM blocks;
SELECT * FROM variables;

SELECT b.*, v.name AS variable_name, v.value
FROM blocks b
LEFT JOIN variables v ON b.id = v.block_id
WHERE b.id = 1;

INSERT INTO blocks (name, description) VALUES
('Bloque Almacen General', 'Aquí son almacenadas todas las variables que aun no han sido asignadas');