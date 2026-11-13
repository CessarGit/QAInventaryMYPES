<?php

/**
 * Base para pruebas de SISTEMA (caja negra): el sistema completo responde a
 * peticiones HTTP reales, igual que en el navegador.
 *
 * - Si defines APP_URL (por ejemplo, la app corriendo en Docker con CI_ENV=testing),
 *   se usa esa URL.
 * - Si no, se levanta automáticamente `php -S 127.0.0.1:8089` con CI_ENV=testing.
 *
 * Ojo: CodeIgniter redirige con redirect('x', 'refresh'), que envía la cabecera
 * "Refresh: 0;url=..." (no "Location"). Por eso usamos destino().
 */
abstract class HttpTestCase extends DatabaseTestCase
{
    const CSRF_COOKIE = 'csrf_cookie_name';
    const CSRF_CAMPO = 'csrf_test_name';

    private static $proceso;
    protected static $baseUrl;

    public static function setUpBeforeClass(): void
    {
        self::iniciarServidor();
    }

    private static function iniciarServidor()
    {
        if (self::$baseUrl) {
            return;
        }
        if (getenv('APP_URL')) {
            self::$baseUrl = rtrim(getenv('APP_URL'), '/') . '/';
            return;
        }
        $puerto = getenv('TEST_PORT') ?: '8089';
        self::$baseUrl = "http://127.0.0.1:$puerto/";
        if (@fsockopen('127.0.0.1', (int) $puerto)) {
            throw new RuntimeException("El puerto $puerto ya está ocupado (¿otro servidor de pruebas?). Ciérralo o usa TEST_PORT=otro_puerto.");
        }
        $env = array_merge(getenv(), array('CI_ENV' => 'testing', 'APP_URL' => self::$baseUrl));
        // "exec" hace que el proceso sea el propio PHP, así proc_terminate() lo cierra de verdad.
        $prefijo = DIRECTORY_SEPARATOR === '/' ? 'exec ' : '';
        $cmd = $prefijo . escapeshellarg(PHP_BINARY) . " -S 127.0.0.1:$puerto " . escapeshellarg(ROOTPATH . 'tests/Sistema/router.php');
        $log = sys_get_temp_dir() . '/servidor_pruebas.log';
        self::$proceso = proc_open($cmd, array(array('pipe', 'r'), array('file', $log, 'a'), array('file', $log, 'a')), $pipes, ROOTPATH, $env);
        for ($i = 0; $i < 50; $i++) {
            if (@fsockopen('127.0.0.1', (int) $puerto)) {
                break;
            }
            usleep(100000);
        }
        register_shutdown_function(function () {
            if (is_resource(self::$proceso)) {
                proc_terminate(self::$proceso);
            }
        });
    }

    /** Comprueba que el servidor que responde es el de esta carpeta del proyecto. */
    protected function setUp(): void
    {
        parent::setUp();
        $r = $this->http('GET', 'auth/login');
        $raiz = isset($r['headers']['x-raiz-pruebas'][0]) ? $r['headers']['x-raiz-pruebas'][0] : null;
        if (!getenv('APP_URL') && $raiz !== md5(realpath(ROOTPATH))) {
            $this->fail('El servidor en ' . self::$baseUrl . ' no es el de este proyecto. Cierra otros servidores de prueba.');
        }
    }

    /**
     * Petición HTTP sin dependencias (no requiere la extensión curl).
     * @return array{status:int, headers:array, body:string}
     */
    protected function http($metodo, $ruta, array $datos = array(), array &$cookies = array(), $conCsrf = true)
    {
        // Igual que el navegador: todo POST lleva el token CSRF de la cookie (ver templates/header.php).
        if ($metodo === 'POST' && $conCsrf) {
            if (empty($cookies[self::CSRF_COOKIE])) {
                $this->http('GET', 'auth/login', array(), $cookies);
            }
            if (!empty($cookies[self::CSRF_COOKIE])) {
                $datos[self::CSRF_CAMPO] = urldecode($cookies[self::CSRF_COOKIE]);
            }
        }
        $cabeceras = array();
        if ($cookies) {
            $pares = array();
            foreach ($cookies as $k => $v) {
                $pares[] = "$k=$v";
            }
            $cabeceras[] = 'Cookie: ' . implode('; ', $pares);
        }
        $opciones = array('method' => $metodo, 'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 20);
        if ($metodo === 'POST') {
            $cabeceras[] = 'Content-Type: application/x-www-form-urlencoded';
            $opciones['content'] = http_build_query($datos);
        }
        $opciones['header'] = implode("\r\n", $cabeceras);
        $body = file_get_contents(self::$baseUrl . ltrim($ruta, '/'), false, stream_context_create(array('http' => $opciones)));
        $respuesta = isset($http_response_header) ? $http_response_header : array();

        $status = 0;
        $headers = array();
        foreach ($respuesta as $linea) {
            if (preg_match('#^HTTP/\S+\s+(\d+)#', $linea, $m)) {
                $status = (int) $m[1];
                continue;
            }
            $partes = explode(':', $linea, 2);
            if (count($partes) === 2) {
                $nombre = strtolower(trim($partes[0]));
                $valor = trim($partes[1]);
                $headers[$nombre][] = $valor;
                if ($nombre === 'set-cookie' && preg_match('/^([^=]+)=([^;]*)/', $valor, $c)) {
                    $cookies[$c[1]] = $c[2];
                }
            }
        }
        return array('status' => $status, 'headers' => $headers, 'body' => (string) $body);
    }

    /** URL a la que redirige la respuesta (Location o Refresh), o null. */
    protected function destino(array $r)
    {
        if (!empty($r['headers']['location'][0])) {
            return $r['headers']['location'][0];
        }
        if (!empty($r['headers']['refresh'][0]) && preg_match('/url=(.*)$/i', $r['headers']['refresh'][0], $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /** Inicia sesión y devuelve las cookies de la sesión. */
    protected function iniciarSesion($email, $clave = self::CLAVE)
    {
        $cookies = array();
        $r = $this->http('POST', 'auth/login', array('email' => $email, 'password' => $clave), $cookies);
        $this->assertStringContainsString('dashboard', (string) $this->destino($r), "No se pudo iniciar sesión como $email");
        return $cookies;
    }
}
