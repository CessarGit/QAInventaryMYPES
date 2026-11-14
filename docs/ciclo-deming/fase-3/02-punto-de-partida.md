# Fase 3 · Punto de partida

La Fase 3 parte de lo que entregaron las Fases 1 y 2.

## 1. Entradas recibidas

| Entrada | Origen | Estado |
|---|---|---|
| Metas de la Tabla 13 de PHVA | Fase 1, paso 2 | Adoptadas sin cambios |
| Trazabilidad RQ-01 a RQ-06 con CP-01 a CP-20 | Fases 1 y 2 | 20 casos |
| Registro de ejecución, rondas 1 y 2 | Fase 2, paso 5 | Ronda 1: 12 pasan. Ronda 2: 19 pasan (29 pruebas: 27 pasan y 2 fallan, ambas de CP-08) |
| Defectos #34 a #44 | Fase 2, paso 5 | 10 registrados: 6 corregidos (1 crítico y 5 altos); 4 abiertos (3 medios y 1 bajo) |
| Revisiones RV-01, RV-03, RV-04 y RV-10 | Fase 2, paso 3 | 50 hallazgos (37 mayores y 13 menores); RV-05 no realizada |
| Restauración y simulacro RB-01 | Fase 2, paso 4 | 12 tablas coinciden; RTO 0,06 h; RPO menor de 0,14 h (cota), con respaldo manual |
| Línea base de regresión | Fase 2, paso 5.7 | Etiqueta `pruebas-base-3.1.6` |

## 2. Brechas declaradas al cerrar la Fase 2

La Fase 2 se cerró con estas brechas, que la Fase 3 debe medir y confirmar:
- RV-05 no realizada;
- cobertura por debajo del 60 %;
- respaldos manuales;
- cuatro issues de mantenimiento de severidad alta abiertos (#6, #16, #17 y #18);
- paso 6 (CodeIgniter 3.1.13) diferido a la próxima iteración (#52).

## 3. Correcciones a la evaluación de la Fase 2

Al contrastar los resultados de la Fase 2 con el informe completo aparecieron tres puntos que cambian la evaluación. La Fase 3 los aplica:

| Punto | Lo que se dijo en la Fase 2 | Lo que corresponde |
|---|---|---|
| Niveles y técnicas de prueba | «Las Tablas 16 y 17 de PHVA no están en las fuentes de diseño»; se evaluó con la Tabla 7 de pruebas | Las dos tablas sí están en el informe (sección Hacer de PHVA); solo faltaban en los extractos del repositorio. La Tabla 17 elige **caja blanca, caja negra, estática, dinámica y exploratoria**, y la Tabla 16 define la aceptación como «un usuario de prueba, con datos de prueba, completa tareas típicas». Se reevalúa en el paso 8 |
| Grupos de la matriz | CP-04 a CP-07 y CP-13 pasan | Pasan en `stock_test`. La base `stock` solo tiene los 2 grupos heredados (Administrator y Owners): la matriz no está configurada en ella. Afecta al punto 1 de la Tabla 17 |
| Defectos que las revisiones no anticiparon | «#37 lo detectó solo la prueba, porque Reports no se revisó a fondo» | Son dos: #37 (Reports, sin revisión) y #38 (Products, cuya revisión RV-05 no se hizo). Los dos defectos que escaparon a las revisiones están en módulos no revisados |
