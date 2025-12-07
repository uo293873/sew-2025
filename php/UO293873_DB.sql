CREATE DATABASE IF NOT EXISTS UO293873_DB;
USE UO293873_DB;

CREATE TABLE IF NOT EXISTS Genero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS PericiaInformatica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Dispositivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Usuario (
    codigo INT AUTO_INCREMENT PRIMARY KEY,
    profesion VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    id_genero INT NOT NULL,
    id_pericia INT NOT NULL,
    FOREIGN KEY (id_genero) REFERENCES Genero(id),
    FOREIGN KEY (id_pericia) REFERENCES PericiaInformatica(id)
);

CREATE TABLE IF NOT EXISTS Resultado(
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_usuario INT NOT NULL,
    id_dispositivo INT NOT NULL,
    tiempo_completado TIME NOT NULL,
    tarea_completada BOOLEAN NOT NULL,
    comentarios VARCHAR(255),
    propuestas_mejora VARCHAR(255),
    valoracion DECIMAL(3,1) NOT NULL CHECK (valoracion >= 0 AND valoracion <= 10),
    FOREIGN KEY (codigo_usuario) REFERENCES Usuario(codigo),
    FOREIGN KEY (id_dispositivo) REFERENCES Dispositivo(id)
);

CREATE TABLE IF NOT EXISTS Observaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_usuario INT NOT NULL,
    comentarios VARCHAR(255) NOT NULL,
    FOREIGN KEY (codigo_usuario) REFERENCES Usuario(codigo)
);

INSERT INTO Genero (nombre) VALUES 
    ('Masculino'),
    ('Femenino'),
    ('Otro');

INSERT INTO PericiaInformatica (nivel) VALUES 
    ('Básico'),
    ('Intermedio'),
    ('Avanzado'),
    ('Experto');

INSERT INTO Dispositivo (tipo) VALUES 
    ('Ordenador'),
    ('Tableta'),
    ('Teléfono');
