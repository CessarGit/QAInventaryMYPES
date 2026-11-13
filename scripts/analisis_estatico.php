<?php
/**
 * TÉCNICA ESTÁTICA (Tabla 17): revisa el código SIN ejecutarlo.
 *
 *   php scripts/analisis_estatico.php
 *
 * Reglas adaptadas a este proyecto (CodeIgniter 3):
 *   SINT-01  Errores de sintaxis (php -l)                               -> crítico
 *   SEG-01   Acción de controlador sin control de permiso (in_array)    -> mayor
 *   SEG-02   HTML armado con datos sin html_escape() (posible XSS)      -> mayor
 *   SEG-03   CSRF desactivado en config.php                             -> mayor
 *   SEG-04   encryption_key escrita en el repositorio                   -> menor
 *   FIA-01   Modelo que escribe en varias tablas sin transacción        -> mayor
 *   MAN-01   CodeIgniter desactualizado frente a 3.1.13                 -> mayor
 *
 * Resultado: reports/estatico.json (lo lee el semáforo) y resumen en pantalla.
 */
require __DIR__ . '/lib/calidad.php';

$hallazgos = array();
$agregar = function ($regla, $severidad, $archivo, $linea, $mensaje) use (&$hallazgos) {
    $hallazgos[] = compact('regla', 'severidad', 'archivo', 'linea', 'mensaje');
};
$rel = function ($ruta) {
    return ltrim(str_replace(RAIZ, '', $ruta), '/');
};

// SINT-01: sintaxis de todo application/
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(RAIZ . '/application'));
foreach ($it as $f) {
    if (substr($f, -4) !== '.php') {
        continue;
    }
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($f) . ' 2>&1', $out, $code);
    if ($code !== 0) {
        $agregar('SINT-01', 'critico', $rel($f), 0, trim(implode(' ', $out)));
    }
    $out = array();
}

// SEG-01 y SEG-02: controladores
$sinPermiso = array('Auth.php' => true, 'Dashboard.php' => true); // públicas por diseño
foreach (glob(RAIZ . '/application/controllers/*.php') as $archivo) {
    $src = file_get_contents($archivo);
    $nombre = basename($archivo);
    preg_match_all('/public function (\w+)\s*\(/', $src, $m, PREG_OFFSET_CAPTURE);
    foreach ($m[1] as $i => $fn) {
        if ($fn[0] === '__construct' || isset($sinPermiso[$nombre])) {
            continue;
        }
        $fin = isset($m[1][$i + 1]) ? $m[1][$i + 1][1] : strlen($src);
        $cuerpo = substr($src, $fn[1], $fin - $fn[1]);
        if (!preg_match('/if\s*\(\s*!\s*in_array\s*\(/', $cuerpo) && !preg_match('/\$this->exigir_permiso\(/', $cuerpo)) {
            $linea = substr_count(substr($src, 0, $fn[1]), "\n") + 1;
            $agregar('SEG-01', 'mayor', $rel($archivo), $linea, "La acción {$fn[0]}() se puede abrir por URL sin revisar permisos");
        }
    }
    foreach (explode("\n", $src) as $n => $l) {
        if (preg_match('/<(td|b|small|h2)[^>]*>.*\'\s*\.\s*\$\w+\[\'\w+\'\]\s*\./', $l) && strpos($l, 'html_escape') === false) {
            $agregar('SEG-02', 'mayor', $rel($archivo), $n + 1, 'Dato impreso en HTML sin html_escape(): ' . trim($l));
        }
    }
}

// SEG-03 / SEG-04: configuración
$cfgRuta = RAIZ . '/application/config/config.php';
$cfg = file_get_contents($cfgRuta);
if (preg_match("/\\\$config\['csrf_protection'\]\s*=\s*FALSE/i", $cfg)) {
    $agregar('SEG-03', 'mayor', $rel($cfgRuta), 0, 'csrf_protection = FALSE: formularios expuestos a CSRF');
}
if (preg_match("/\\\$config\['encryption_key'\]\s*=\s*'[^']+'/", $cfg)) {
    $agregar('SEG-04', 'menor', $rel($cfgRuta), 0, 'encryption_key escrita en el código; usar variable de entorno');
}

// FIA-01: escrituras múltiples sin transacción
foreach (glob(RAIZ . '/application/models/*.php') as $archivo) {
    $src = file_get_contents($archivo);
    preg_match_all('/public function (\w+)\s*\(/', $src, $m, PREG_OFFSET_CAPTURE);
    foreach ($m[1] as $i => $fn) {
        $fin = isset($m[1][$i + 1]) ? $m[1][$i + 1][1] : strlen($src);
        $cuerpo = substr($src, $fn[1], $fin - $fn[1]);
        $escrituras = preg_match_all('/->(insert|update|delete)\s*\(/', $cuerpo);
        if ($escrituras >= 3 && strpos($cuerpo, 'trans_') === false) {
            $linea = substr_count(substr($src, 0, $fn[1]), "\n") + 1;
            $agregar('FIA-01', 'mayor', $rel($archivo), $linea, "{$fn[0]}() hace $escrituras escrituras sin transacción");
        }
    }
}

// MAN-01: versión del framework
if (preg_match("/CI_VERSION'?\s*(?:=|,)\s*'([\d.]+)'/", file_get_contents(RAIZ . '/system/core/CodeIgniter.php'), $v)
    && version_compare($v[1], '3.1.13', '<')) {
    $agregar('MAN-01', 'mayor', 'system/core/CodeIgniter.php', 58, "CodeIgniter {$v[1]} (última 3.x: 3.1.13)");
}

$resumen = array('critico' => 0, 'mayor' => 0, 'menor' => 0);
foreach ($hallazgos as $h) {
    $resumen[$h['severidad']]++;
}
escribir(RAIZ . '/reports/estatico.json', json_encode(array('resumen' => $resumen, 'hallazgos' => $hallazgos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "ANÁLISIS ESTÁTICO\n";
foreach ($hallazgos as $h) {
    printf("  [%-7s] %-7s %s:%d  %s\n", $h['severidad'], $h['regla'], $h['archivo'], $h['linea'], mb_strimwidth($h['mensaje'], 0, 110, '…'));
}
printf("Total: %d crítico(s), %d mayor(es), %d menor(es) -> reports/estatico.json\n", $resumen['critico'], $resumen['mayor'], $resumen['menor']);
exit($resumen['critico'] > 0 ? 1 : 0);
