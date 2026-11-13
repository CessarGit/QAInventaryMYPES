<?php
/**
 * SEMÁFORO DE CALIDAD (Tabla 18 del documento).
 *
 *   php scripts/semaforo.php
 *
 * Necesita haber ejecutado antes:
 *   php scripts/analisis_estatico.php            -> reports/estatico.json
 *   vendor/bin/phpunit                           -> reports/junit.xml y reports/clover.xml
 *
 * Reglas:
 *   🔴 ROJO  : falla al menos una prueba, o hay un error de sintaxis.        -> se BLOQUEA (exit 1)
 *   🟡 ÁMBAR : todo pasa, pero hay advertencias estáticas o la cobertura de
 *              Orders + Products está bajo el umbral.                         -> se integra solo con
 *                                                                               aprobación y riesgo registrado
 *   🟢 VERDE : todo pasa, sin advertencias y cobertura >= umbral.            -> se integra
 */
require __DIR__ . '/lib/calidad.php';

$UMBRAL_COBERTURA = (float) (getenv('UMBRAL_COBERTURA') ?: 60);

$junit = leer_junit(RAIZ . '/reports/junit.xml');
if ($junit === null) {
    fwrite(STDERR, "No existe reports/junit.xml: ejecuta primero vendor/bin/phpunit\n");
    exit(1);
}
$anot = leer_anotaciones();
$estatico = leer_json(RAIZ . '/reports/estatico.json', array('resumen' => array('critico' => 0, 'mayor' => 0, 'menor' => 0)));
$cob = cobertura(RAIZ . '/reports/clover.xml', array('Model_orders.php', 'Model_products.php'));

$fallidas = array();
$criticas = array();
foreach ($junit as $clave => $r) {
    if ($r['estado'] === 'falla') {
        $fallidas[] = $clave;
        if (!empty($anot[$clave]['critico'])) {
            $criticas[] = $clave;
        }
    }
}

$motivos = array();
if ($fallidas) {
    $estado = 'ROJO';
    $motivos[] = count($fallidas) . ' prueba(s) fallan (' . count($criticas) . ' marcadas como defecto crítico)';
} elseif ($estatico['resumen']['critico'] > 0) {
    $estado = 'ROJO';
    $motivos[] = 'errores de sintaxis en el código';
} else {
    $estado = 'VERDE';
    $advertencias = $estatico['resumen']['mayor'] + $estatico['resumen']['menor'];
    if ($advertencias > 0) {
        $estado = 'AMBAR';
        $motivos[] = "$advertencias advertencia(s) del análisis estático";
    }
    if ($cob === null) {
        $estado = 'AMBAR';
        $motivos[] = 'no se midió la cobertura (instala pcov o xdebug)';
    } elseif ($cob['porcentaje'] < $UMBRAL_COBERTURA) {
        $estado = 'AMBAR';
        $motivos[] = "cobertura Orders+Products {$cob['porcentaje']} % < {$UMBRAL_COBERTURA} %";
    }
}

$acciones = array(
    'VERDE' => 'El cambio se puede integrar.',
    'AMBAR' => 'Integrar solo con aprobación del responsable del módulo y registrar el riesgo en el PR.',
    'ROJO'  => 'Integración BLOQUEADA hasta corregir.',
);

$salida = "SEMÁFORO DE CALIDAD: " . color($estado) . "\n";
$salida .= '  Pruebas: ' . count($junit) . ' casos, ' . count($fallidas) . " con fallas\n";
foreach ($fallidas as $f) {
    $salida .= '    - ' . (!empty($anot[$f]['critico']) ? '[CRÍTICO] ' : '') . $f . (isset($anot[$f]['caso']) ? " ({$anot[$f]['caso']})" : '') . "\n";
}
$salida .= '  Cobertura Orders+Products: ' . ($cob ? $cob['porcentaje'] . ' %' : 'sin medir') . " (umbral $UMBRAL_COBERTURA %)\n";
$salida .= sprintf("  Estático: %d crítico, %d mayor, %d menor\n", $estatico['resumen']['critico'], $estatico['resumen']['mayor'], $estatico['resumen']['menor']);
if ($motivos) {
    $salida .= '  Motivo: ' . implode('; ', $motivos) . "\n";
}
$salida .= '  Acción: ' . $acciones[$estado] . "\n";
echo $salida;

escribir(RAIZ . '/reports/semaforo.json', json_encode(array(
    'estado' => $estado, 'motivos' => $motivos, 'fallidas' => $fallidas, 'criticas' => $criticas,
    'cobertura' => $cob, 'estatico' => $estatico['resumen'], 'fecha' => date('c'),
), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// En GitHub Actions: resumen visible en la pestaña del job y anotación.
if (getenv('GITHUB_STEP_SUMMARY')) {
    file_put_contents(getenv('GITHUB_STEP_SUMMARY'), "## Semáforo de calidad: " . color($estado) . "\n\n```\n$salida```\n", FILE_APPEND);
    if ($estado === 'AMBAR') {
        echo '::warning title=Semáforo ÁMBAR::' . implode('; ', $motivos) . "\n";
    }
    if ($estado === 'ROJO') {
        echo '::error title=Semáforo ROJO::' . implode('; ', $motivos) . "\n";
    }
}

exit($estado === 'ROJO' ? 1 : 0);
