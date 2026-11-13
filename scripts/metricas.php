<?php
/**
 * FASE VERIFICAR: calcula las métricas de la Tabla 19 (y Tabla 13) y genera la
 * matriz de trazabilidad con su estado real.
 *
 *   php scripts/metricas.php
 *
 * Lee: reports/junit.xml, reports/clover.xml, calidad/requisitos.json,
 *      calidad/registro_iteracion.json y las etiquetas @requisito de las pruebas.
 * Escribe: reports/metricas.md, reports/metricas.json,
 *          reports/matriz_trazabilidad.md y agrega una fila a reports/historial_metricas.csv
 */
require __DIR__ . '/lib/calidad.php';

$junit = leer_junit(RAIZ . '/reports/junit.xml') ?: array();
$anot = leer_anotaciones();
$req = leer_json(RAIZ . '/calidad/requisitos.json', array('requisitos' => array()))['requisitos'];
$reg = leer_json(RAIZ . '/calidad/registro_iteracion.json', array());
$cob = cobertura(RAIZ . '/reports/clover.xml', array('Model_orders.php', 'Model_products.php'));
$manuales = isset($reg['casos_manuales']) ? $reg['casos_manuales'] : array();

$estadoPrueba = function ($clave) use ($junit) {
    return isset($junit[$clave]) ? $junit[$clave]['estado'] : 'sin-ejecutar';
};
$pct = function ($a, $b) {
    return $b ? round(100 * $a / $b, 1) : 0;
};

// ---------- Matriz de trazabilidad ----------
$filas = array();
foreach ($req as $r) {
    $pruebas = array();
    foreach ($anot as $clave => $a) {
        if (in_array($r['id'], $a['requisitos'], true)) {
            $pruebas[$clave] = $a + array('estado' => $estadoPrueba($clave));
        }
    }
    foreach ($manuales as $m) {
        if ($m['requisito'] === $r['id']) {
            $e = $m['estado'] === 'aprobado' ? 'ok' : ($m['estado'] === 'fallido' ? 'falla' : 'sin-ejecutar');
            $pruebas[$m['id']] = array('caso' => $m['id'], 'nivel' => $m['nivel'], 'tecnica' => $m['tecnica'], 'estado' => $e, 'manual' => true);
        }
    }
    $estados = array_column($pruebas, 'estado');
    if (!$pruebas) {
        $estado = 'SIN PRUEBA';
    } elseif (in_array('falla', $estados, true)) {
        $estado = 'Rechazado';
    } elseif (in_array('sin-ejecutar', $estados, true)) {
        $estado = 'Pendiente';
    } else {
        $estado = 'Aprobado';
    }
    $filas[] = array('req' => $r, 'pruebas' => $pruebas, 'estado' => $estado);
}

$icono = array('Aprobado' => '🟢 Aprobado', 'Pendiente' => '🟡 Pendiente', 'Rechazado' => '🔴 Rechazado', 'SIN PRUEBA' => '⚫ Sin prueba');
$md = "# Matriz de trazabilidad (generada " . date('Y-m-d H:i') . ")\n\n";
$md .= "| Requisito | Prioridad | Casos | Niveles | Pruebas (✔ pasa / ✘ falla / … pendiente) | Estado |\n|---|---|---|---|---|---|\n";
foreach ($filas as $f) {
    $casos = array_unique(array_filter(array_column($f['pruebas'], 'caso')));
    $niveles = array_unique(array_filter(array_column($f['pruebas'], 'nivel')));
    $lista = array();
    foreach ($f['pruebas'] as $clave => $p) {
        $marca = $p['estado'] === 'ok' ? '✔' : ($p['estado'] === 'falla' ? '✘' : '…');
        $lista[] = "$marca `" . (isset($p['manual']) ? $clave . ' (manual)' : $clave) . '`';
    }
    $md .= sprintf("| %s %s | %s | %s | %s | %s | %s |\n", $f['req']['id'], $f['req']['descripcion'], $f['req']['prioridad'],
        implode(', ', $casos), implode(', ', $niveles), implode('<br>', $lista), $icono[$f['estado']]);
}
escribir(RAIZ . '/reports/matriz_trazabilidad.md', $md);

// ---------- Métricas ----------
$disenados = count($anot) + count($manuales);
$ejecutados = count(array_filter($anot, function ($a, $k) use ($estadoPrueba) {
    return in_array($estadoPrueba($k), array('ok', 'falla'), true);
}, ARRAY_FILTER_USE_BOTH)) + count(array_filter($manuales, function ($m) {
    return in_array($m['estado'], array('aprobado', 'fallido'), true);
}));
$pasan = count(array_filter(array_keys($anot), function ($k) use ($estadoPrueba) {
    return $estadoPrueba($k) === 'ok';
}));

// Defectos: abierto si su prueba vinculada falla; sin prueba, se usa corregido_iteracion.
$defectos = isset($reg['defectos']) ? $reg['defectos'] : array();
$criticosAbiertos = array();
$tiempos = array();
foreach ($defectos as &$d) {
    $vinculadas = (array) $d['prueba'];
    if ($vinculadas && $vinculadas[0]) {
        $abierto = false;
        foreach ($vinculadas as $p) {
            $abierto = $abierto || $estadoPrueba($p) !== 'ok';
        }
    } else {
        $abierto = empty($d['corregido_iteracion']);
    }
    $d['abierto'] = $abierto;
    if ($abierto && $d['severidad'] === 'critico') {
        $criticosAbiertos[] = $d['id'];
    }
    if (!$abierto && $d['severidad'] === 'critico') {
        $cierre = $d['corregido_iteracion'] ?: $reg['iteracion'];
        $tiempos[] = $cierre - $d['detectado_iteracion'];
    }
}
unset($d);

$altas = array_filter($filas, function ($f) {
    return $f['req']['prioridad'] === 'alta';
});
$altasConPrueba = count(array_filter($altas, function ($f) {
    return $f['estado'] !== 'SIN PRUEBA';
}));

// Revisión de cambios: GitHub (gh) si está disponible; si no, el registro manual.
$integrados = isset($reg['cambios']['integrados']) ? $reg['cambios']['integrados'] : 0;
$revisados = isset($reg['cambios']['con_revision']) ? $reg['cambios']['con_revision'] : 0;
$fuenteRevision = 'registro manual';
$gh = @shell_exec('gh pr list --state merged --limit 100 --json number,reviews 2>/dev/null');
if ($gh && ($prs = json_decode($gh, true)) !== null && $prs) {
    $integrados = count($prs);
    $revisados = count(array_filter($prs, function ($p) {
        return !empty($p['reviews']);
    }));
    $fuenteRevision = 'GitHub (gh)';
}

$tareas = isset($reg['tareas']) ? $reg['tareas'] : array('planificadas' => 0, 'terminadas' => 0);
$m = array(
    array('Proyecto', 'Tareas terminadas / planificadas', $pct($tareas['terminadas'], $tareas['planificadas']), '>= 80 %', $pct($tareas['terminadas'], $tareas['planificadas']) >= 80, "{$tareas['terminadas']}/{$tareas['planificadas']}"),
    array('Proyecto', 'Casos de prueba ejecutados / diseñados', $pct($ejecutados, $disenados), '>= 90 %', $pct($ejecutados, $disenados) >= 90, "$ejecutados/$disenados (incluye manuales)"),
    array('Proceso', 'Cambios integrados con revisión', $pct($revisados, $integrados), '100 %', $integrados > 0 && $revisados === $integrados, "$revisados/$integrados ($fuenteRevision)"),
    array('Proceso', 'Iteraciones para corregir un defecto crítico (máx.)', $tiempos ? max($tiempos) : '-', '<= 1', !$tiempos || max($tiempos) <= 1, $tiempos ? count($tiempos) . ' corregidos' : 'aún sin corregidos'),
    array('Producto', 'Defectos críticos abiertos', count($criticosAbiertos), '0', !$criticosAbiertos, $criticosAbiertos ? implode(', ', $criticosAbiertos) : 'ninguno'),
    array('Producto', 'Cobertura unitaria Orders + Products', $cob ? $cob['porcentaje'] : '-', '>= 60 %', $cob && $cob['porcentaje'] >= 60, $cob ? json_encode($cob['detalle']) : 'sin medir'),
    array('Producto', 'Requisitos prioritarios con prueba', $pct($altasConPrueba, count($altas)), '100 %', $altasConPrueba === count($altas), "$altasConPrueba/" . count($altas)),
    array('Info', 'Pruebas automáticas que pasan', $pct($pasan, count($anot)), '-', null, "$pasan/" . count($anot)),
);

$out = "# Métricas de calidad — iteración " . (isset($reg['iteracion']) ? $reg['iteracion'] : '?') . ' (' . date('Y-m-d H:i') . ")\n\n";
$out .= "| Nivel | Métrica | Valor | Umbral | Estado | Detalle |\n|---|---|---|---|---|---|\n";
foreach ($m as $fila) {
    $estado = $fila[4] === null ? '—' : ($fila[4] ? '✅ Cumple' : '❌ No cumple');
    $valor = is_numeric($fila[2]) && (strpos($fila[3], '%') !== false || $fila[0] === 'Info') ? $fila[2] . ' %' : $fila[2];
    $out .= "| {$fila[0]} | {$fila[1]} | $valor | {$fila[3]} | $estado | {$fila[5]} |\n";
}
$out .= "\n## Defectos\n\n| ID | Severidad | Descripción | Estado |\n|---|---|---|---|\n";
foreach ($defectos as $d) {
    $out .= "| {$d['id']} | {$d['severidad']} | {$d['descripcion']} | " . ($d['abierto'] ? '🔴 Abierto' : '🟢 Cerrado') . " |\n";
}
escribir(RAIZ . '/reports/metricas.md', $out);
escribir(RAIZ . '/reports/metricas.json', json_encode(array('metricas' => $m, 'defectos' => $defectos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Historial para comparar antes / después (Fase 4)
$hist = RAIZ . '/reports/historial_metricas.csv';
if (!is_file($hist)) {
    escribir($hist, "fecha,etiqueta,pruebas_pasan,pruebas_total,criticos_abiertos,cobertura,req_alta_con_prueba,ejecutados_vs_disenados\n");
}
$etiqueta = isset($argv[1]) ? $argv[1] : 'ejecucion';
file_put_contents($hist, sprintf("%s,%s,%d,%d,%d,%s,%s,%s\n", date('c'), $etiqueta, $pasan, count($anot), count($criticosAbiertos),
    $cob ? $cob['porcentaje'] : '', $pct($altasConPrueba, count($altas)), $pct($ejecutados, $disenados)), FILE_APPEND);

echo $out . "\nArchivos: reports/metricas.md, reports/matriz_trazabilidad.md, reports/historial_metricas.csv\n";
