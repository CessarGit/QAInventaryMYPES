<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Configuración SOLO para ENVIRONMENT = 'testing' (CI_ENV=testing).
 * CodeIgniter la usa en lugar de config/database.php. Apunta a la base de pruebas.
 */
// PHP 8.1+ lanza excepciones en mysqli por defecto; CodeIgniter 3 espera FALSE (como en PHP 7.4).
mysqli_report(MYSQLI_REPORT_OFF);

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => getenv('DB_HOST') ?: '127.0.0.1',
	'port'     => getenv('DB_PORT') ?: 3306,
	'username' => getenv('DB_USER') ?: 'test',
	'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'test',
	'database' => getenv('DB_NAME') ?: 'stock_test',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
