-- Script de creación de la base de datos Terranova (MariaDB)
-- ADVERTENCIA: Este script elimina y recrea la base de datos

DROP DATABASE IF EXISTS terranova;
CREATE DATABASE terranova CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE terranova;

-- Tabla: Perfil
CREATE TABLE Perfil (
  idPerfil INT NOT NULL,
  nombrePerfil VARCHAR(50) NOT NULL,
  CONSTRAINT PK_Perfil PRIMARY KEY (idPerfil),
  CONSTRAINT UQ_Perfil_nombre UNIQUE (nombrePerfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Usuario
CREATE TABLE Usuario (
  rut CHAR(8) NOT NULL,
  nombre1 VARCHAR(50) NOT NULL,
  nombre2 VARCHAR(50) NULL,
  apellido1 VARCHAR(50) NOT NULL,
  apellido2 VARCHAR(50) NULL,
  idPerfil INT NOT NULL,
  contraseña VARCHAR(100) NOT NULL,
  CONSTRAINT PK_Usuario PRIMARY KEY (rut),
  CONSTRAINT FK_Usuario_Perfil FOREIGN KEY (idPerfil)
    REFERENCES Perfil (idPerfil)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX IX_Usuario_idPerfil (idPerfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: TipoLectura
CREATE TABLE TipoLectura (
  idTipoLectura INT NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  CONSTRAINT PK_TipoLectura PRIMARY KEY (idTipoLectura),
  CONSTRAINT UQ_TipoLectura_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Temporal (última medición)
-- Nota: el sistema realiza TRUNCATE + INSERT para mantener una única fila
CREATE TABLE Temporal (
  temperatura DECIMAL(5,2) NOT NULL,
  humedadAire DECIMAL(5,2) NOT NULL,
  humedadSuelo DECIMAL(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Lectura (histórico)
CREATE TABLE Lectura (
  idLectura BIGINT NOT NULL AUTO_INCREMENT,
  idTipoLectura INT NOT NULL,
  fechaLectura DATE NOT NULL,
  horaLectura TIME NOT NULL,
  lectura DECIMAL(6,2) NOT NULL,
  CONSTRAINT PK_Lectura PRIMARY KEY (idLectura),
  CONSTRAINT FK_Lectura_TipoLectura FOREIGN KEY (idTipoLectura)
    REFERENCES TipoLectura (idTipoLectura)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX IX_Lectura_Tipo_Fecha (idTipoLectura, fechaLectura),
  INDEX IX_Lectura_Tipo_Fecha_Hora (idTipoLectura, fechaLectura, horaLectura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: TicketSoporte
CREATE TABLE TicketSoporte (
  id INT NOT NULL AUTO_INCREMENT,
  fechaCreacion DATE NOT NULL,
  problema VARCHAR(500) NOT NULL,
  respuesta VARCHAR(500) NULL,
  fechaRespuesta DATE NULL,
  creador CHAR(8) NOT NULL,
  solucionador CHAR(8) NULL,
  CONSTRAINT PK_TicketSoporte PRIMARY KEY (id),
  CONSTRAINT FK_TicketSoporte_Creador FOREIGN KEY (creador)
    REFERENCES Usuario (rut)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT FK_TicketSoporte_Solucionador FOREIGN KEY (solucionador)
    REFERENCES Usuario (rut)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX IX_Ticket_Creador (creador),
  INDEX IX_Ticket_Solucionador (solucionador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos semilla
INSERT INTO Perfil (idPerfil, nombrePerfil) VALUES
  (1, 'Administrador'),
  (2, 'Trabajador')
ON DUPLICATE KEY UPDATE nombrePerfil = VALUES(nombrePerfil);

INSERT INTO TipoLectura (idTipoLectura, nombre) VALUES
  (1, 'Temperatura'),
  (2, 'Humedad Aire'),
  (3, 'Humedad Suelo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);


