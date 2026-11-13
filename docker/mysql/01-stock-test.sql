-- Base de datos SOLO para pruebas automáticas (PHPUnit la vacía en cada ejecución).
-- Se ejecuta sola cuando el volumen de MySQL se crea por primera vez.
-- Si tu volumen ya existe, ejecútala a mano (ver la guía).
CREATE DATABASE IF NOT EXISTS stock_test CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER IF NOT EXISTS 'test'@'%' IDENTIFIED BY 'test';
GRANT ALL PRIVILEGES ON stock_test.* TO 'test'@'%';
FLUSH PRIVILEGES;
