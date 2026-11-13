<?php
/**
 * PLAN DE AUDITORÍA (Tabla 20): junta la EVIDENCIA objetiva para las tres auditorías.
 *
 *   php scripts/auditoria.php [proceso|producto|cumplimiento|todas]
 *
 * El script no reemplaza al auditor: marca cada punto como
 *   CONFORME / NO CONFORME / REVISAR (requiere juicio humano)
 * y deja la evidencia para que el auditor firme en reports/auditoria.md.
 * Requiere haber corrido antes: analisis_estatico.php, phpunit, semaforo.php y metricas.php.
 */
require __DIR__ . '/lib/calidad.php';

$tipo = isset($argv[1]) ? $argv[1] : 'todas';
$items = array();
$check = function ($auditoria, $id, $pregunta, $resultado, $evidencia) use (&$items) {
    $items[] = compact('auditoria', 'id', 'pregunta', 'resultado', 'evidencia');
};
$git = function ($cmd) {
    return trim((string) @shell_exec('cd ' . escapeshellarg(RAIZ) . " && git $cmd 2>/dev/null"));
};

$semaforo = leer_json(RAIZ . '/reports/semaforo.json', array());
$metricas = leer_json(RAIZ . '/reports/metricas.json', array('metricas' => array(), 'defectos' => array()));
$estatico = leer_json(RAIZ . '/reports/estatico.json', array('resumen' => array(), 'hallazgos' => array()));
$anot = leer_anotaciones();
$metrica = function ($nombre) use ($metricas) {
    foreach ($metricas['metricas'] as $m) {
        if ($m[1] === $nombre) {
            return $m;
        }
    }
    return null;
};

// ---------------- AUDITORÍA DE PROCESO (César revisa a Oscar y Miguel) ----------------
if (in_array($tipo, array('proceso', 'todas'), true)) {
    $esRepo = $git('rev-parse --is-inside-work-tree') === 'true';
    $check('Proceso', 'P-01', '¿El código está en un repositorio Git?', $esRepo ? 'CONFORME' : 'NO CONFORME', $esRepo ? 'git rev-parse OK; remoto: ' . ($git('remote get-url origin') ?: 'sin remoto') : 'No hay .git');

    $ramas = $esRepo ? array_filter(array_map('trim', explode("\n", $git('branch -a --format="%(refname:short)"')))) : array();
    $check('Proceso', 'P-02', '¿Se trabaja con ramas (no directo en main)?', count($ramas) > 1 ? 'CONFORME' : 'NO CONFORME', count($ramas) . ' rama(s): ' . implode(', ', array_slice($ramas, 0, 8)));

    $log = $esRepo ? array_filter(explode("\n", $git('log -20 --pretty=%s'))) : array();
    $conRef = count(array_filter($log, function ($l) {
        return preg_match('/(#\d+|D-\d+|RQ-\d+|CP-\d+)/', $l);
    }));
    $check('Proceso', 'P-03', '¿Los commits referencian tarea, requisito o defecto?', $log && $conRef / count($log) >= 0.8 ? 'CONFORME' : 'NO CONFORME', "$conRef de " . count($log) . ' últimos commits con referencia (#n, RQ-, CP-, D-)');

    $rev = $metrica('Cambios integrados con revisión');
    $check('Proceso', 'P-04', '¿Todo cambio integrado tuvo revisión por pares?', $rev && $rev[4] ? 'CONFORME' : 'REVISAR', $rev ? $rev[5] : 'sin datos');

    $ci = is_file(RAIZ . '/.github/workflows/calidad.yml');
    $check('Proceso', 'P-05', '¿Las pruebas se ejecutan en cada cambio (CI con semáforo)?', $ci ? 'CONFORME' : 'NO CONFORME', $ci ? '.github/workflows/calidad.yml' : 'Falta el workflow');

    $reg = is_file(RAIZ . '/calidad/registro_iteracion.json');
    $check('Proceso', 'P-06', '¿Existe registro de tareas, casos manuales y defectos de la iteración?', $reg ? 'CONFORME' : 'NO CONFORME', $reg ? 'calidad/registro_iteracion.json' : 'Falta registro');
}

// ---------------- AUDITORÍA DE PRODUCTO (Oscar y Miguel se revisan entre sí) ----------------
if (in_array($tipo, array('producto', 'todas'), true)) {
    $estado = isset($semaforo['estado']) ? $semaforo['estado'] : 'SIN DATOS';
    $check('Producto', 'PR-01', '¿El último semáforo está en verde?', $estado === 'VERDE' ? 'CONFORME' : ($estado === 'AMBAR' ? 'REVISAR' : 'NO CONFORME'), 'Semáforo: ' . color($estado) . (empty($semaforo['motivos']) ? '' : ' — ' . implode('; ', $semaforo['motivos'])));

    $req = $metrica('Requisitos prioritarios con prueba');
    $check('Producto', 'PR-02', '¿Cada requisito prioritario tiene al menos una prueba?', $req && $req[4] ? 'CONFORME' : 'NO CONFORME', $req ? $req[5] . ' (ver reports/matriz_trazabilidad.md)' : 'sin datos');

    $incompletas = array_keys(array_filter($anot, function ($a) {
        return !$a['caso'] || !$a['requisitos'] || !$a['nivel'] || !$a['tecnica'];
    }));
    $check('Producto', 'PR-03', '¿Toda prueba indica caso, requisito, nivel y técnica (plantilla)?', !$incompletas ? 'CONFORME' : 'NO CONFORME', $incompletas ? 'Sin etiquetas: ' . implode(', ', $incompletas) : count($anot) . ' pruebas con etiquetas completas');

    $may = isset($estatico['resumen']['mayor']) ? $estatico['resumen']['mayor'] : 0;
    $check('Producto', 'PR-04', '¿El análisis estático no reporta hallazgos mayores?', $may === 0 ? 'CONFORME' : 'NO CONFORME', "$may hallazgo(s) mayor(es) (reports/estatico.json)");

    $cob = $metrica('Cobertura unitaria Orders + Products');
    $check('Producto', 'PR-05', '¿La cobertura de Orders + Products es >= 60 %?', $cob && $cob[4] ? 'CONFORME' : 'NO CONFORME', $cob ? $cob[2] . ' % ' . $cob[5] : 'sin medir');

    // Muestra para revisión manual: los 3 archivos de application/ más modificados recientemente.
    $muestra = array_slice(array_values(array_unique(array_filter(explode("\n", $git('log -30 --name-only --pretty=format: -- application')), function ($f) {
        return substr($f, -4) === '.php' && strpos($f, 'config/') === false;
    }))), 0, 3);
    $check('Producto', 'PR-06', 'Revisión manual de una muestra con la lista de verificación de código', 'REVISAR', $muestra ? 'Muestra: ' . implode(', ', array_unique($muestra)) : 'Elegir 3 archivos cambiados en la iteración');
}

// ---------------- AUDITORÍA DE CUMPLIMIENTO (César, antes del cierre) ----------------
if (in_array($tipo, array('cumplimiento', 'todas'), true)) {
    $hash = true;
    $junit = leer_junit(RAIZ . '/reports/junit.xml') ?: array();
    foreach ($anot as $k => $a) {
        if (in_array('RQ-06', $a['requisitos'], true)) {
            $hash = $hash && isset($junit[$k]) && $junit[$k]['estado'] === 'ok';
        }
    }
    $check('Cumplimiento', 'C-01', '¿Las contraseñas se almacenan con hash (protección de datos)?', $hash ? 'CONFORME' : 'NO CONFORME', 'Pruebas de RQ-06 ' . ($hash ? 'en verde' : 'con fallas'));

    $cfgTest = is_file(RAIZ . '/application/config/testing/database.php');
    $check('Cumplimiento', 'C-02', '¿Las pruebas usan una base separada y datos ficticios?', $cfgTest ? 'CONFORME' : 'NO CONFORME', 'config/testing/database.php -> stock_test; usuarios @mype.test; DatabaseTestCase bloquea DB_NAME=stock');

    $db = file_get_contents(RAIZ . '/application/config/database.php');
    $secretoDb = preg_match("/'password'\s*=>\s*'[^']+'/", $db);
    $secretoKey = false;
    foreach ($estatico['hallazgos'] as $h) {
        $secretoKey = $secretoKey || $h['regla'] === 'SEG-04';
    }
    $check('Cumplimiento', 'C-03', '¿No hay secretos (claves de BD o de cifrado) en el repositorio?', !$secretoDb && !$secretoKey ? 'CONFORME' : 'NO CONFORME', ($secretoDb ? 'Contraseña de BD en config/database.php. ' : '') . ($secretoKey ? 'encryption_key en config/config.php.' : ''));

    $csrf = false;
    foreach ($estatico['hallazgos'] as $h) {
        $csrf = $csrf || $h['regla'] === 'SEG-03';
    }
    $check('Cumplimiento', 'C-04', '¿Los formularios están protegidos contra CSRF?', $csrf ? 'NO CONFORME' : 'CONFORME', $csrf ? 'csrf_protection = FALSE' : 'csrf_protection = TRUE');

    $abiertos = array_filter($metricas['defectos'], function ($d) {
        return !empty($d['abierto']) && $d['severidad'] === 'critico';
    });
    $check('Cumplimiento', 'C-05', '¿Cero defectos críticos abiertos antes del cierre?', !$abiertos ? 'CONFORME' : 'NO CONFORME', $abiertos ? implode(', ', array_column($abiertos, 'id')) : 'ninguno');
}

// ---------------- Informe ----------------
$res = array_count_values(array_column($items, 'resultado'));
$md = "# Informe de auditoría interna — " . date('Y-m-d') . "\n\n";
$md .= "Tipo: **$tipo** · Conforme: " . (isset($res['CONFORME']) ? $res['CONFORME'] : 0)
    . ' · No conforme: ' . (isset($res['NO CONFORME']) ? $res['NO CONFORME'] : 0)
    . ' · Revisar: ' . (isset($res['REVISAR']) ? $res['REVISAR'] : 0) . "\n\n";
$md .= "| Auditoría | ID | Pregunta | Resultado | Evidencia |\n|---|---|---|---|---|\n";
foreach ($items as $i) {
    $ico = array('CONFORME' => '✅', 'NO CONFORME' => '❌', 'REVISAR' => '🔍');
    $md .= "| {$i['auditoria']} | {$i['id']} | {$i['pregunta']} | {$ico[$i['resultado']]} {$i['resultado']} | " . str_replace('|', '/', $i['evidencia']) . " |\n";
}
$md .= "\n## Acciones correctivas (completar)\n\n| ID hallazgo | Acción | Responsable | Iteración límite | Verificación |\n|---|---|---|---|---|\n";
foreach ($items as $i) {
    if ($i['resultado'] === 'NO CONFORME') {
        $md .= "| {$i['id']} |  |  |  |  |\n";
    }
}
$md .= "\nAuditor: ____________________   Auditado: ____________________   Firma SQA: ____________________\n";
escribir(RAIZ . '/reports/auditoria.md', $md);
echo $md;
