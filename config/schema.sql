-- ============================================================
-- Base de Datos — Sistema de Gestión de Biblioteca Fusalmo
-- DSS 404 G03T
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca_fusalmo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE biblioteca_fusalmo;

-- ── Tabla: usuarios ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    correo      VARCHAR(150)    NOT NULL UNIQUE,
    contrasena  VARCHAR(255)    NOT NULL,          -- bcrypt hash
    telefono    VARCHAR(20)     DEFAULT NULL,
    rol         ENUM('admin','bibliotecario','usuario') NOT NULL DEFAULT 'usuario',
    estado      ENUM('activo','inactivo')           NOT NULL DEFAULT 'activo',
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_correo (correo),
    INDEX idx_rol    (rol),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ── Tabla: categorias ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categorias (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)     NOT NULL UNIQUE,
    descripcion TEXT            DEFAULT NULL,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Tabla: libros ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS libros (
    id           INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    titulo       VARCHAR(200)   NOT NULL,
    autor        VARCHAR(150)   NOT NULL,
    isbn         VARCHAR(20)    DEFAULT NULL,
    categoria_id INT UNSIGNED   DEFAULT NULL,
    estado       ENUM('disponible','prestado','deteriorado','perdido') NOT NULL DEFAULT 'disponible',
    estado_fisico ENUM('bueno','regular','malo') NOT NULL DEFAULT 'bueno',
    codigo_unico VARCHAR(50)    NOT NULL UNIQUE,   -- código por copia
    created_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    INDEX idx_titulo   (titulo),
    INDEX idx_estado   (estado),
    INDEX idx_categoria(categoria_id)
) ENGINE=InnoDB;

-- ── Tabla: prestamos ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS prestamos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED NOT NULL,
    libro_id        INT UNSIGNED NOT NULL,
    fecha_prestamo  DATE         NOT NULL,
    fecha_devolucion DATE        NOT NULL,
    estado          ENUM('activo','devuelto','vencido') NOT NULL DEFAULT 'activo',
    observaciones   TEXT         DEFAULT NULL,
    registrado_por  INT UNSIGNED DEFAULT NULL,     -- FK usuario que registró
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id)     REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (libro_id)       REFERENCES libros(id)   ON DELETE RESTRICT,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL,

    INDEX idx_usuario (usuario_id),
    INDEX idx_libro   (libro_id),
    INDEX idx_estado  (estado),
    INDEX idx_fecha_dev(fecha_devolucion)
) ENGINE=InnoDB;

-- ── Tabla: devoluciones ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS devoluciones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prestamo_id     INT UNSIGNED NOT NULL,
    fecha_real      DATE         NOT NULL,
    estado_fisico   ENUM('bueno','regular','malo') NOT NULL DEFAULT 'bueno',
    observaciones   TEXT         DEFAULT NULL,
    registrado_por  INT UNSIGNED DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (prestamo_id)    REFERENCES prestamos(id) ON DELETE RESTRICT,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)  ON DELETE SET NULL,

    INDEX idx_prestamo(prestamo_id)
) ENGINE=InnoDB;

-- ── Tabla: calificaciones ────────────────────────────────────
CREATE TABLE IF NOT EXISTS calificaciones (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED     NOT NULL,
    libro_id    INT UNSIGNED     NOT NULL,
    estrellas   TINYINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_estrellas CHECK (estrellas BETWEEN 1 AND 5),
    UNIQUE KEY uq_usuario_libro (usuario_id, libro_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id)   REFERENCES libros(id)   ON DELETE CASCADE,
    INDEX idx_libro_cal (libro_id)
) ENGINE=InnoDB;

-- ── Tabla: reportes_mensuales ────────────────────────────────
CREATE TABLE IF NOT EXISTS reportes_mensuales (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    anio            SMALLINT UNSIGNED NOT NULL,
    mes             TINYINT UNSIGNED  NOT NULL,   -- 1-12
    datos           JSON              NOT NULL,   -- snapshot del reporte
    generado_por    INT UNSIGNED      DEFAULT NULL,
    created_at      TIMESTAMP         DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP         DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_anio_mes (anio, mes),
    FOREIGN KEY (generado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_anio_mes (anio, mes)
) ENGINE=InnoDB;

-- ── Datos iniciales ───────────────────────────────────────────
-- Usuario administrador por defecto
-- Contraseña: Admin@2026 (cambiar tras el primer acceso)
INSERT INTO usuarios (nombre, correo, contrasena, rol, estado) VALUES
('Administrador', 'admin@fusalmo.org',
 '$2y$12$placeholder_hash_change_this', 'admin', 'activo');

-- Categorías de ejemplo
INSERT INTO categorias (nombre, descripcion) VALUES
('Infantil',    'Libros para niños de 3 a 10 años'),
('Literatura',  'Novelas y cuentos para jóvenes y adultos'),
('Autoayuda',   'Desarrollo personal y bienestar'),
('Fantasía',    'Libros de fantasía y ciencia ficción'),
('Historia',    'Libros históricos y documentales'),
('Educativo',   'Material de apoyo escolar');
