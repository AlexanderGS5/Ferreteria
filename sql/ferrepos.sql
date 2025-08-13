CREATE DATABASE IF NOT EXISTS ferrepos;
USE ferrepos;

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  precio DECIMAL(10,2),
  stock INT
);

CREATE TABLE IF NOT EXISTS ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS detalle_venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT,
  producto_id INT,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) UNIQUE,
  clave VARCHAR(50),
  rol ENUM('admin', 'cajero') DEFAULT 'cajero'
);

INSERT INTO usuarios (usuario, clave, rol) VALUES ('admin', 'admin123', 'admin');
