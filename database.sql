-- Base de datos W-Style
CREATE DATABASE IF NOT EXISTS wstyle_db;
USE wstyle_db;

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de portafolio
CREATE TABLE IF NOT EXISTS portafolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    categoria VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(100),
    descripcion TEXT,
    logo VARCHAR(255),
    testimonio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de miembros W Club
CREATE TABLE IF NOT EXISTS wclub_miembros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mensajes de contacto
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE
);

-- Insertar administrador por defecto (usuario: admin, contraseña: admin123)
-- La contraseña está hasheada con password_hash()
INSERT INTO administradores (username, password, email) VALUES 
('admin', '$2y$10$q/n.j5RJGEGFs2cXVGEtrOchjQm7NA8a8UGnYJbHdYOUkFfdEJbtm', 'admin@w-style.com');

-- Insertar datos de ejemplo para portafolio
INSERT INTO portafolio (titulo, descripcion, imagen, categoria) VALUES 
('Colección 2024', 'Moda contemporánea con diseños innovadores', 'portfolio1.jpg', 'Colección'),
('Editorial Vogue', 'Fotografía de moda para revista Vogue', 'portfolio2.jpg', 'Editorial'),
('Desfile Primavera', 'Runway show de la colección primavera', 'portfolio3.jpg', 'Desfile'),
('Lookbook Verano', 'Campaña seasonal verano 2024', 'portfolio4.jpg', 'Lookbook'),
('Estudio Creativo', 'Behind the scenes del proceso creativo', 'portfolio5.jpg', 'Behind the scenes'),
('Colaboración', 'Edición limitada en colaboración con artista', 'portfolio6.jpg', 'Colaboración');

-- Insertar datos de ejemplo para servicios
INSERT INTO servicios (titulo, descripcion, icono) VALUES 
('Consultoría de Imagen', 'Asesoría personalizada para definir tu estilo único y proyectar la mejor versión de ti mismo.', '👗'),
('Fotografía de Moda', 'Sesiones fotográficas profesionales para editoriales, lookbooks y campañas publicitarias.', '📸'),
('Styling Personal', 'Selección y coordinación de outfits para eventos especiales, sesiones de fotos y vida cotidiana.', '✨'),
('Diseño de Colecciones', 'Creación de colecciones de moda completas, desde el concepto hasta la producción final.', '🎨'),
('Personal Shopping', 'Acompañamiento en compras para construir un armario versátil y sofisticado.', '🛍️'),
('Producción de Eventos', 'Organización y producción de desfiles de moda, lanzamientos y eventos de marca.', '🎭');

-- Insertar datos de ejemplo para clientes
INSERT INTO clientes (nombre, marca, descripcion, testimonio) VALUES 
('María González', 'VOGUE', 'Editorial de moda', 'W-Style transformó completamente nuestra imagen de marca. Su visión creativa es incomparable.'),
('Carlos Rodríguez', 'ELLE', 'Revista de estilo', 'La atención al detalle y profesionalismo de Wang Style es excepcional. Altamente recomendados.'),
('Ana Martínez', 'ZARA', 'Retail fashion', 'Trabajar con W-Style fue una experiencia increíble. Entendieron perfectamente nuestra visión.'),
('Pedro López', 'H&M', 'Moda sostenible', 'La creatividad y profesionalismo del equipo superaron todas nuestras expectativas.'),
('Laura Sánchez', 'GUCCI', 'Lujo italiano', 'W-Style aporta una perspectiva única que eleva cualquier proyecto al siguiente nivel.'),
('Miguel Torres', 'PRADA', 'Alta costura', 'Colaboración excepcional. El equipo de W-Style entiende el lujo como nadie más.');
