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
