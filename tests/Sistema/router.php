<?php
// Router para el servidor embebido de PHP: sirve archivos estáticos y envía el resto a index.php
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$raiz = realpath(__DIR__ . '/../..');
if ($ruta !== '/' && is_file($raiz . $ruta)) {
    return false;
}
header('X-Raiz-Pruebas: ' . md5($raiz)); // lo verifica HttpTestCase
$_SERVER['CI_ENV'] = 'testing';
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir($raiz);
require $raiz . '/index.php';
