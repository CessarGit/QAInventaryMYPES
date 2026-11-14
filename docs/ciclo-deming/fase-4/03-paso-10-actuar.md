# Fase 4 · Paso 10: actuar

| Subpaso | Fecha de desarrollo | Estado |
|---|---|---|
| 10.1 Retrospectiva | 14/11/2026 | Redactada a partir de los datos |
| 10.2 Registro de acciones | 14/11/2026 | AC-01 a AC-12 definidas |
| 10.3 Estandarización | 14/11/2026 | E-1 a E-8 incorporadas como reglas 18 a 25 de `CLAUDE.md` y a la plantilla de PR (sin commit) |
| 10.4 Reevaluación de madurez | 14/11/2026 | Evaluada |

## 10.1 Retrospectiva (Tabla 23 de PHVA)

### Qué funcionó

| Práctica | Dato que lo respalda |
|---|---|
| Revisar antes de probar | 8 de 10 defectos ya figuraban como hallazgos de revisión. Los 2 que escaparon (#37 y #38) están en módulos no revisados |
| Confirmar el Top-Down en el código | A-01, encontrado al confirmar H-01 a H-06, resultó ser el único defecto crítico (#34) |
| Fijar las decisiones antes de ejecutar | D-01 a D-07 evitaron ajustar los resultados esperados a lo obtenido. La única decisión posterior (D-08) es la que generó dudas |
| Las pruebas no se modifican para que pasen | Las 6 correcciones se verificaron con la misma prueba que detectó el defecto; 0 regresiones entre rondas |
| Integración continua obligatoria | Ningún cambio de la Fase 2 entró sin pasar el semáforo |
| Respaldo con testigos | El simulacro demostró de dónde venían los datos y qué se pierde después de la última copia |

### Qué no funcionó

| Problema | Consecuencia | Causa |
|---|---|---|
| Inicio tardío | +33 días en la Fase 1 y +30 en las pruebas, frente al diseño | El trabajo empezó tarde; la duración real fue igual o menor que la planificada |
| RV-05 omitida y Reports sin revisar | #37 y #38 llegaron a las pruebas; cobertura de revisión 75 % | Decisión de alcance, sin aplicar la regla «no omitir revisiones de riesgo alto» |
| Regla «solo se corrigen críticos y altos confirmados por prueba» | H-01 sin aplicar; 4 hallazgos altos sin confirmar; cierre de hallazgos mayores 21,62 % | La regla limitó las correcciones, pero no obligó a escribir la prueba que confirmara cada hallazgo alto |
| Respaldos manuales | El RPO de la política no se cumple | Se probó la recuperación, no la política de copias |
| Hora del desastre sin anotar | RPO solo como cota | Faltó un paso explícito en el procedimiento del simulacro |
| Casos de prueba solo desde los requisitos | Cobertura de 49,06 % y 38,07 % | Ninguna regla pedía cubrir las líneas del código crítico |
| Sin revisor distinto del autor | La métrica de revisión por pares no se alcanza | Un solo integrante |

### Lecciones

1. Una revisión omitida sale cara: los únicos defectos no anticipados están en lo que no se revisó.
2. Todo hallazgo alto debe tener una prueba que lo confirme o lo descarte antes de cerrar la fase de pruebas.
3. Las decisiones que cambian un resultado esperado se toman antes de ejecutar, o se declaran como desviación.
4. Un procedimiento de recuperación debe exigir anotar T0.
5. La meta de cobertura necesita casos derivados del código (caja blanca), además de los derivados de los requisitos.

## 10.2 Registro de acciones (Tabla 21 de PHVA)

Cada acción sigue los cinco pasos del informe. La columna «WP» remite al paquete técnico de `PLAN-TECNICO.md`.

| ID | Brechas | Diagnóstico | Objetivo SMART | Acciones | Métrica de verificación | Estandarización | WP | Cuándo |
|---|---|---|---|---|---|---|---|---|
| AC-01 | B-01 | Cobertura de 49,06 % y 38,07 % | Llegar a ≥ 60 % en Orders y Products antes del cierre de la iteración 2 | Pruebas ligadas a casos para `printDiv`, `update`, `fetchProductData` y lo no cubierto | Cobertura de pcov | La integración continua bloquea si la cobertura baja del 60 % (verde de la Tabla 18) | WP-7, WP-8 | Iteración 2 |
| AC-02 | B-03 | RV-05 omitida; Reports sin revisar | Revisar Products (RV-05) y Reports antes de las pruebas de la iteración 2 | Doble pasada con L1: autor y Claude Code | Cobertura de revisión 4/4 = 100 % | Ninguna revisión de riesgo alto se omite sin excepción registrada | WP-5 | Iteración 2, Fase 2 |
| AC-03 | B-05, B-04 | 4 hallazgos altos sin confirmar | Confirmar o descartar #6, #16, #17 y #18 con un caso cada uno, y corregir los confirmados | CP-21 a CP-24 automatizados; corrección en `fix/` | 0 defectos críticos o altos abiertos, sin salvedades | Todo hallazgo alto tiene su caso antes de cerrar las pruebas | WP-2 | Iteración 2 |
| AC-04 | B-06 | #38, #42, #43 y #44 abiertos | Cerrar los 4 defectos con su prueba | Una rama `fix/` por defecto, con la prueba primero | 20/20 casos pasan; 0 defectos abiertos | Los defectos medios y bajos tienen plan con fecha | WP-3 | Iteración 2 |
| AC-05 | B-04 | Cierre de hallazgos mayores 21,62 % | 100 % de hallazgos mayores cerrados y verificados, o diferidos con issue, antes de aceptar el producto | Clasificar cada uno: prueba y corrección, decisión, no reproducible o siguiente iteración. Decidir #14 y #15 | Indicador de cierre (Tabla 19 de revisiones) | Ningún hallazgo se cierra sin una prueba o una decisión enlazada | WP-4 | Iteración 2 |
| AC-06 | B-02 | Sin revisión de un par | 100 % de los PR con revisión en pareja completa antes de fusionar | Check obligatorio `revision-pareja`; Claude Code revisa con la lista y el autor aprueba; revisor externo en al menos un PR | PR con revisión en pareja / PR fusionados | Un PR sin revisión no se fusiona (regla del informe) | WP-1 | Inmediata |
| AC-07 | B-07 | Respaldos manuales | RPO ≤ 1 h con respaldo automático y repetir el simulacro anotando T0 | Respaldo cada hora y diario con retención; cifrado; segunda copia; script que mide RTO y RPO | RTO ≤ 4 h y RPO ≤ 1 h con la política de la Tabla 11 | Restauración de prueba mensual y después de cada cambio importante (Tabla 20) | WP-11 | Iteración 2 |
| AC-08 | B-08, B-09 | 2 de 5 técnicas sin aplicar; aceptación parcial | 5 de 5 técnicas y 4 de 4 niveles en la iteración 2 | PHPStan (estática y ámbar); tiempos en staging (dinámica); sesión exploratoria; aceptación con un usuario de prueba externo | Técnicas y niveles aplicados | El ámbar de la Tabla 18 queda activo | WP-8, WP-9 | Iteración 2 |
| AC-09 | B-14 | CodeIgniter 3.1.6; PHP 7.4 y MySQL 5.7 sin soporte | Actualizar a 3.1.13 sin regresiones contra `pruebas-base-3.1.6`, y planificar PHP y MySQL | Pasos 6 y 7 del esquema; plan de migración | Casos que pasan después de actualizar (mejora 2, Tabla 21) | Revisar las versiones de las dependencias en cada cierre (Tabla 21) | Pasos 6 y 7 | Iteración 2 (decisión del autor) |
| AC-10 | B-10, B-11 | H-01 sin aplicar; integración descendente no secuenciada; S2 y S6 sin casos | Centralizar la autorización y medir el DPMO de los 6 subsistemas | Integración descendente E1 a E3 con dobles; permisos en un punto único; casos para S2 y S6 | DPMO de los 6 subsistemas; ningún resultado de prueba cambia | La mejora de diseño se hace solo con la suite en verde | WP-6 | Iteración 2 |
| AC-11 | B-12, B-13 | Controles de seguridad parciales; matriz sin configurar en `stock`; sin llaves foráneas | Cumplir los controles verificables de la Tabla 13 | HTTPS en staging; sesiones; política de contraseñas como validación; los 5 grupos en `stock`; revisión del modelo de datos (RV-02) antes de proponer llaves foráneas | Controles de la Tabla 13 en «cumple» | Cada control de seguridad tiene su prueba automatizada | WP-10 | Iteración 2 |
| AC-12 | B-15, B-16, B-17 | Sin versiones publicadas; responsable no asignado; licencia; auditorías pendientes | Cerrar la iteración 1 con versión, licencia revisada y auditoría externa | Release v1 con notas; asignación de responsable en las issues; revisión de la licencia de terceros; prueba independiente y paquete externo | Puntos 7 y 8 de la Tabla 17; auditoría externa entregada | Cada cierre de iteración publica una versión | — | Cierre de la iteración 1 |

## 10.3 Estandarización

Reglas que pasan a ser obligatorias desde la iteración 2. Las dos primeras ya estaban en la Tabla 21 del informe; las demás salen de la retrospectiva.

| N.º | Regla | Origen |
|---|---|---|
| E-1 | Todo cambio nuevo exige su prueba | Tabla 21 (mejora 1) |
| E-2 | Se revisan las versiones de las dependencias en cada cierre de iteración | Tabla 21 (mejora 2) |
| E-3 | Un PR sin revisión en pareja completa no se fusiona | Revisiones, prácticas informales; AC-06 |
| E-4 | Ninguna revisión de riesgo alto se omite sin excepción registrada | Lección 1 |
| E-5 | Todo hallazgo alto tiene un caso que lo confirma o lo descarta antes de cerrar las pruebas | Lección 2 |
| E-6 | Las decisiones que cambian un resultado esperado se toman antes de ejecutar | Lección 3 |
| E-7 | Todo simulacro de recuperación anota T0 | Lección 4 |
| E-8 | La integración continua bloquea si la cobertura baja del umbral | Tabla 18; lección 5 |

## 10.4 Reevaluación de madurez (Tabla 11 de PHVA)

Autodiagnóstico informal, igual que en el informe: no es una evaluación CMMI formal.

| Área | Nivel inicial | Meta | Situación tras la iteración 1 | Nivel estimado |
|---|---|---|---|---|
| Gestión de requisitos | 1 | 2: matriz de trazabilidad mantenida | Trazabilidad RQ-01 a RQ-06 con CP-01 a CP-20, actualizada en cada cambio | **2** |
| Planificación y seguimiento | 1 | 2: tablero de tareas y seguimiento por iteración | Plan con fechas, desfases medidos, issues y estado en `CLAUDE.md`; sin tablero de tareas | **1 → 2 (en transición)** |
| Gestión de la configuración | 1 | 2: repositorio, ramas y revisiones | Repositorio, ramas por tema, `main` protegida, PR obligatorios, etiquetas de referencia; revisión sin par | **2 (con salvedad)** |
| Aseguramiento de la calidad | 1 | 2: plan de calidad, pruebas y auditoría interna | Plan de pruebas, 29 pruebas en la integración continua, revisiones, autoverificación; sin independencia y con la auditoría externa pendiente | **1 → 2 (en transición)** |
| Métricas | 1 | 2: pocas métricas con umbral | Métricas de las Tablas 13 y 19 medidas contra su umbral, sin redondeo | **2** |

**Resultado:** 3 de 5 áreas alcanzan el nivel 2 y 2 están en transición. La meta del informe («nivel 2 en cinco áreas») se alcanza en parte. Las dos áreas en transición dependen de AC-06 (independencia en la revisión) y AC-12 (auditoría externa), y de agregar un tablero de tareas.
