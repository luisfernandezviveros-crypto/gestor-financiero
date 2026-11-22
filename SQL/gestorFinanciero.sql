CREATE DATABASE gestor_financiero;

USE gestor_financiero;

CREATE TABLE categorias (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  tipo ENUM('ingreso','gasto') NOT NULL,
  descripcion VARCHAR(255) NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE cuentas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  descripcion VARCHAR(255) NULL DEFAULT NULL,
  saldo DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  moneda VARCHAR(10) NOT NULL DEFAULT 'COP',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE transacciones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cuenta_id BIGINT UNSIGNED NOT NULL,
  categoria_id BIGINT UNSIGNED NOT NULL,
  tipo ENUM('ingreso','gasto') NOT NULL,
  monto DECIMAL(18,2) NOT NULL,
  fecha DATE NOT NULL,
  descripcion VARCHAR(255) NULL DEFAULT NULL,
  creado_por VARCHAR(150) NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY transacciones_cuenta_id_foreign (cuenta_id),
  KEY transacciones_categoria_id_foreign (categoria_id),
  CONSTRAINT transacciones_cuenta_id_foreign
    FOREIGN KEY (cuenta_id) REFERENCES cuentas (id)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT transacciones_categoria_id_foreign
    FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    ON DELETE RESTRICT ON UPDATE RESTRICT
);

INSERT INTO categorias (id, nombre, tipo, descripcion, created_at, updated_at) VALUES
(1, 'Salario',             'ingreso', 'Pago mensual por trabajo', '2026-11-01 08:05:00', '2026-11-01 08:05:00'),
(2, 'Trabajos freelance',  'ingreso', 'Ingresos extra',           '2026-11-01 08:05:00', '2026-11-01 08:05:00'),
(3, 'Alimentación',        'gasto',   'Mercado y restaurantes',   '2026-11-01 08:05:00', '2026-11-01 08:05:00'),
(4, 'Transporte',          'gasto',   'Bus, taxi y gasolina',     '2026-11-01 08:05:00', '2026-11-01 08:05:00'),
(5, 'Servicios públicos',  'gasto',   'Agua, luz, internet',      '2026-11-01 08:05:00', '2026-11-01 08:05:00');

INSERT INTO cuentas (id, nombre, descripcion, saldo, moneda, created_at, updated_at) VALUES
(1, 'Efectivo',                      'Dinero en mano',     330000.00, 'COP', '2026-11-01 08:10:00', '2026-11-05 10:00:00'),
(2, 'Cuenta de ahorros Bancolombia', 'Cuenta principal',   720000.00, 'COP', '2026-11-01 08:10:00', '2026-11-10 10:00:00'),
(3, 'Nequi',                         'Billetera digital',   55000.00, 'COP', '2026-11-01 08:10:00', '2026-11-12 10:00:00');

INSERT INTO transacciones (id, cuenta_id, categoria_id, tipo, monto, fecha, descripcion, creado_por, created_at, updated_at) VALUES
(1, 1, 1, 'ingreso', 150000.00, '2026-11-03', 'Pago quincena',         'Usuario Demo', '2026-11-03 09:00:00', '2026-11-03 09:00:00'),
(2, 1, 3, 'gasto',    20000.00, '2026-11-05', 'Mercado de la semana',  'Usuario Demo', '2026-11-05 10:00:00', '2026-11-05 10:00:00'),
(3, 2, 2, 'ingreso', 300000.00, '2026-11-08', 'Proyecto de página web','Usuario Demo', '2026-11-08 09:00:00', '2026-11-08 09:00:00'),
(4, 2, 4, 'gasto',    80000.00, '2026-11-10', 'Recarga de transporte', 'Usuario Demo', '2026-11-10 10:00:00', '2026-11-10 10:00:00'),
(5, 3, 5, 'gasto',    45000.00, '2026-11-12', 'Factura de internet',   'Usuario Demo', '2026-11-12 10:00:00', '2026-11-12 10:00:00');
