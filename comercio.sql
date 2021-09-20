CREATE DATABASE Comercio;

USE Comercio;

CREATE TABLE TiposDeUsuario (
  id_tipo INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(15),
  PRIMARY KEY (id_tipo)
);

INSERT INTO TiposDeUsuario(nombre) VALUES ('Administrador'), ('Usuario');

CREATE TABLE Usuarios(
  id_usuario VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(30) NOT NULL UNIQUE,
  password_hash CHAR(60) NOT NULL,
  nombres VARCHAR(30) NOT NULL,
  apellidos VARCHAR(30) NOT NULL,
  cedula INT NOT NULL UNIQUE,
  tipo INT NOT NULL DEFAULT 2,
  PRIMARY KEY (id_usuario),
  FOREIGN KEY (tipo) REFERENCES TiposDeUsuario(id_tipo)
  ON UPDATE CASCADE ON DELETE CASCADE
);

-- La clave es hola1234
INSERT INTO Usuarios(id_usuario, email, password_hash, nombres, apellidos, cedula, tipo) VALUES
('admin-comercio', 'bv.vincenzo@gmail.com','$2y$10$uUgVeOQ3u8qrVhhxW39QmOoSfvyHX06J/MS.18MC8njGvlXXCrgle' , 'Vincenzo', 'Bonelli', 24287154, 1);

CREATE TABLE Productos (
  id_producto INT NOT NULL UNIQUE AUTO_INCREMENT,
  nombre VARCHAR(30) NOT NULL,
  precio DECIMAL(6,2) NOT NULL,
  cantidad INT(4) NOT NULL,
  descripcion TEXT,
  imagen CHAR(36) UNIQUE,
  PRIMARY KEY (id_producto)
);

INSERT INTO productos (nombre, precio, cantidad, descripcion, imagen) VALUES ('Flores', '1.00', '100', 'Flores para boda', '3dbf7650b93f6919e01bbbbf8be02301.png'), ('Torta', '15.00', '20', 'Torta decorada', '7d44b6f4ae7b25b7b33189160b2a0448.png'), ('Caja de regalo', '1.00', '200', 'Cajas perfectas para regalos', 'ec1b169f926b2f32196275402ad610fb.png'), ('Huevos de pascua', '3.00', '100', 'Huevos de pascua para regalar', 'eb789a888e206eb866644f3cb805e171.png'), ('Libro', '1.00', '10', 'Una lectura ligera', 'a531b30af284a16a18c9b5ff487a404a.png'), ('Helado', '0.50', '200', 'Helado con topping.', '2a35b8981bdbb4891159dede4e5394ca.png'), ('PimentÃ³n Amarillo', '0.07', '300', 'PimentÃ³n dulce y fresco', '870e39ed38c2532e18e60888e46a8114.png')


CREATE TABLE Carritos (
  id_carrito INT NOT NULL AUTO_INCREMENT,
  id_usuario VARCHAR(20) NOT NULL UNIQUE,
  fecha DATE NOT NULL DEFAULT CURRENT_DATE(),
  PRIMARY KEY (id_carrito),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
  ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE EnCarrito (
  ID INT NOT NULL AUTO_INCREMENT,
  id_carrito INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  PRIMARY KEY (ID),
  FOREIGN KEY (id_carrito) REFERENCES Carritos(id_carrito)
  ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (id_producto) REFERENCES Productos (id_producto)
  ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Compra (
  id_compra INT NOT NULL AUTO_INCREMENT,
  id_usuario VARCHAR(20) NOT NULL,
  fecha DATE NOT NULL DEFAULT CURRENT_DATE(),
  total DECIMAL(6,2) NOT NULL,
  PRIMARY KEY (id_compra),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
  ON UPDATE CASCADE ON DELETE RESTRICT
);