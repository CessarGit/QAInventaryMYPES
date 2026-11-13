<?php
/**
 * Funciones compartidas por semaforo.php, metricas.php y auditoria.php.
 * Solo lee archivos que genera PHPUnit (junit.xml, clover.xml) y el código de las pruebas.
 */

define('RAIZ', realpath(__DIR__ . '/../..'));

/** Lee las etiquetas (@caso, @requisito, @nivel, @tecnica, @group) de cada método test_*. */
function leer_anotaciones()
{
    $pruebas = array();
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(RAIZ . '/tests'));
    foreach ($it as $archivo) {
        if (substr($archivo->getFilename(), -8) !== 'Test.php') {
            continue;
        }
        $src = file_get_contents($archivo->getPathname());
        $clase = preg_match('/class\s+(\w+)/', $src, $m) ? $m[1] : '?';
        preg_match_all('#/\*\*(.*?)\*/\s*public function (test\w+)#s', $src, $bloques, PREG_SET_ORDER);
        foreach ($bloques as $b) {
            $tags = array();
            preg_match_all('/@(\w+)\s+([^\n]*)/', $b[1], $t, PREG_SET_ORDER);
            foreach ($t as $par) {
                $tags[$par[1]] = trim($par[2]);
            }
            $pruebas["$clase::{$b[2]}"] = array(
                'clase' => $clase, 'metodo' => $b[2],
                'caso' => isset($tags['caso']) ? $tags['caso'] : '',
                'requisitos' => isset($tags['requisito']) ? preg_split('/[\s,]+/', $tags['requisito']) : array(),
                'nivel' => isset($tags['nivel']) ? $tags['nivel'] : '',
                'tecnica' => isset($tags['tecnica']) ? $tags['tecnica'] : '',
                'critico' => isset($tags['group']) && strpos($tags['group'], 'critico') !== false,
            );
        }
    }
    return $pruebas;
}

/**
 * Resultados de junit.xml agrupados por "Clase::metodo".
 * Un método con dataProvider tiene varias ejecuciones: falla si cualquiera falla.
 */
function leer_junit($ruta)
{
    if (!is_file($ruta)) {
        return null;
    }
    $xml = simplexml_load_file($ruta);
    $res = array();
    foreach ($xml->xpath('//testcase') as $tc) {
        $clase = (string) $tc['class'];
        if ($clase === '') {
            $partes = explode('::', (string) $tc['file']);
            $clase = basename($partes[0], '.php');
        }
        $metodo = preg_replace('/ with data set.*$/', '', (string) $tc['name']);
        $clave = "$clase::$metodo";
        $estado = 'ok';
        if (isset($tc->failure) || isset($tc->error)) {
            $estado = 'falla';
        } elseif (isset($tc->skipped)) {
            $estado = 'omitida';
        }
        if (!isset($res[$clave]) || $estado === 'falla') {
            $res[$clave] = isset($res[$clave]) && $res[$clave]['estado'] === 'falla' ? $res[$clave] : array('estado' => $estado);
        }
        $res[$clave]['ejecuciones'] = (isset($res[$clave]['ejecuciones']) ? $res[$clave]['ejecuciones'] : 0) + 1;
    }
    return $res;
}

/** Cobertura de líneas (statements) de los archivos indicados, desde clover.xml. */
function cobertura($ruta, array $archivos)
{
    if (!is_file($ruta)) {
        return null;
    }
    $xml = simplexml_load_file($ruta);
    $total = 0;
    $cubiertas = 0;
    $detalle = array();
    foreach ($xml->xpath('//file') as $f) {
        $nombre = basename((string) $f['name']);
        if (!in_array($nombre, $archivos, true)) {
            continue;
        }
        $s = (int) $f->metrics['statements'];
        $c = (int) $f->metrics['coveredstatements'];
        $total += $s;
        $cubiertas += $c;
        $detalle[$nombre] = $s ? round(100 * $c / $s, 1) : 0;
    }
    return array('porcentaje' => $total ? round(100 * $cubiertas / $total, 1) : 0, 'detalle' => $detalle);
}

function leer_json($ruta, $defecto = null)
{
    return is_file($ruta) ? json_decode(file_get_contents($ruta), true) : $defecto;
}

function escribir($ruta, $contenido)
{
    if (!is_dir(dirname($ruta))) {
        mkdir(dirname($ruta), 0777, true);
    }
    file_put_contents($ruta, $contenido);
}

function color($estado)
{
    $mapa = array('VERDE' => '🟢 VERDE', 'AMBAR' => '🟡 ÁMBAR', 'ROJO' => '🔴 ROJO');
    return isset($mapa[$estado]) ? $mapa[$estado] : $estado;
}
