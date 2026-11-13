<?php
/**
 * Bootstrap de pruebas para CodeIgniter 3 (sin ci-phpunit-test).
 *
 * Qué hace:
 *  1. Define las constantes que CodeIgniter necesita (BASEPATH, APPPATH...).
 *  2. Carga solo las piezas del framework que usan los modelos (Common.php y Model.php).
 *  3. Crea un "contenedor" (TestCI) que hace el papel de get_instance():
 *     ahí colocamos db, input, session y load, reales o falsos según el nivel de prueba.
 *
 * Las pruebas de SISTEMA no usan este contenedor: levantan el sitio completo
 * con el servidor embebido de PHP (ver tests/Sistema/HttpTestCase.php).
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

define('ENVIRONMENT', 'testing');
define('ROOTPATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
define('FCPATH', ROOTPATH);
define('SELF', 'index.php');
define('BASEPATH', ROOTPATH . 'system' . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'application' . DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

// En consola (PHPUnit) no existe una petición web: si config.php arma base_url con
// $_SERVER['HTTP_HOST'], se simulan los datos mínimos de una petición.
$_SERVER += array(
    'HTTP_HOST'   => '127.0.0.1:8089',
    'SERVER_NAME' => '127.0.0.1',
    'SERVER_PORT' => '8089',
    'REQUEST_URI' => '/',
    'SCRIPT_NAME' => '/index.php',
);

if (file_exists(ROOTPATH . 'vendor/autoload.php')) {
    require_once ROOTPATH . 'vendor/autoload.php';
}

require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Model.php';

/** Contenedor que sustituye al superobjeto de CodeIgniter durante las pruebas. */
class TestCI
{
    /** @var TestCI|null */
    public static $instance;

    public $db;
    public $input;
    public $session;
    public $load;

    /** Propiedades dinámicas (modelos cargados) sin avisos en PHP 8.2+. */
    private $extra = array();

    public function __get($name)
    {
        return isset($this->extra[$name]) ? $this->extra[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->extra[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->extra[$name]);
    }

    public static function reset()
    {
        self::$instance = new self();
        self::$instance->input = new FakeInput();
        self::$instance->session = new FakeSession();
        self::$instance->load = new FakeLoader(self::$instance);
        return self::$instance;
    }
}

if (!function_exists('get_instance')) {
    function &get_instance()
    {
        return TestCI::$instance;
    }
}

/** Simula $this->input: devuelve lo que la prueba haya puesto en $post. */
class FakeInput
{
    public $post = array();

    public function post($key = null, $xss = false)
    {
        if ($key === null) {
            return $this->post;
        }
        return array_key_exists($key, $this->post) ? $this->post[$key] : null;
    }
}

/** Simula $this->session con un arreglo en memoria. */
class FakeSession
{
    public $data = array('id' => 1, 'logged_in' => true);

    public function userdata($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set_userdata($data, $value = null)
    {
        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        } else {
            $this->data[$data] = $value;
        }
    }
}

/**
 * Simula $this->load. Si la prueba ya inyectó un doble (mock) del modelo,
 * NO lo reemplaza: así las pruebas unitarias controlan las dependencias.
 */
class FakeLoader
{
    private $ci;

    public function __construct($ci)
    {
        $this->ci = $ci;
    }

    public function model($name)
    {
        $prop = strtolower($name);
        if (isset($this->ci->$prop)) {
            return;
        }
        $class = ucfirst($prop);
        require_once APPPATH . 'models/' . $class . '.php';
        $this->ci->$prop = new $class();
    }

    public function library($name)
    {
        $class = ucfirst($name);
        $prop = strtolower($name);
        if (isset($this->ci->$prop)) {
            return;
        }
        if (!class_exists($class, false)) {
            require_once APPPATH . 'libraries/' . $class . '.php';
        }
        $this->ci->$prop = new $class();
    }

    public function helper($name)
    {
    }
}

require_once __DIR__ . '/Support/DatabaseTestCase.php';
require_once __DIR__ . '/Sistema/HttpTestCase.php';