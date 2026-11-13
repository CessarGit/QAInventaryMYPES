<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Sobrescribe SOLO algunas claves de config/config.php cuando CI_ENV=testing.
 * (CodeIgniter carga primero config/config.php y luego este archivo.)
 */
$config['base_url'] = getenv('APP_URL') ?: 'http://127.0.0.1:8089/';
$config['sess_save_path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ci_sessions_test';
if (!is_dir($config['sess_save_path'])) {
	@mkdir($config['sess_save_path'], 0700, TRUE);
}
$config['log_threshold'] = 1;
