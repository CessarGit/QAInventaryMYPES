# Fase 2 · Punto de partida

La Fase 2 parte de lo que entregó la Fase 1. Este archivo registra esas entradas, el pendiente que se arrastró y las decisiones que hubo que tomar antes de ejecutar los casos.

## 1. Entradas recibidas de la Fase 1

| Entrada | Estado al iniciar la Fase 2 |
|---|---|
| Staging en Docker (PHP 7.4.33, MySQL 5.7.44, pcov) | Funcionando |
| Credenciales fuera del código | Cumplido |
| Plantillas, etiquetas y `main` protegida | Cumplido |
| Integración continua con `sintaxis` y `pruebas` | Verde demostrado; la verificación `pruebas` informaba «Sin suite todavía» |
| Hallazgos Top-Down H-01 a H-06 | Confirmados; issues #4 a #7 |
| Trazabilidad | RQ-01 a RQ-06 con CP-01 a CP-14; 5 datos POR DEFINIR |
| Plan de pruebas | Aprobado, con el cronograma de diseño del 15/10/2026 al 27/10/2026 |
| Metas | Tabla 13 de PHVA |

## 2. Pendiente de la Fase 1

| Pendiente | Cómo se resolvió en la Fase 2 |
|---|---|
| Demostrar el semáforo rojo (Tabla 18) | PR #33: un error de sintaxis intencional hizo fallar `sintaxis`, y GitHub bloqueó la fusión. El PR se cerró sin fusionar (`1.4-semaforo-rojo.txt`) |

## 3. Decisiones previas a la ejecución

Los datos POR DEFINIR de la trazabilidad, y dos dudas que surgieron al preparar los datos de prueba, se resolvieron como decisiones del autor antes de ejecutar los casos. Están en `docs/calidad/decisiones-autor.md`, firmadas.

| ID | Pregunta | Decisión | Casos afectados |
|---|---|---|---|
| D-01 | Valor de N (stock inicial del producto de prueba) | N = 5 | CP-01, CP-02, CP-03, CP-19 |
| D-02 | Campos obligatorios en el alta de producto | Nombre, SKU, precio, cantidad, marca, categoría y tienda | CP-08, CP-20 |
| D-03 | Importe que suma el reporte anual | Neto: el total que paga el cliente, con cargos e IVA | CP-09, CP-12 |
| D-04 | Algoritmo de hash de las contraseñas | Hash adaptativo (`password_hash`: bcrypt o argon2) | CP-14 |
| D-05 | Longitud máxima de los campos de texto | 255 caracteres | CP-20 |
| D-06 | Qué hacer en la integración continua con las pruebas que fallan por defectos abiertos | Excluirlas y registrar el riesgo (ámbar de la Tabla 18) | Integración continua |
| D-07 | Permiso de Logística en Company, cuya fila de la Tabla 5 es solo lectura | Ningún permiso, para no conceder una edición que la matriz no permite | PC-0 |

La decisión D-08, sobre si el reporte cuenta las ventas no pagadas, se tomó durante la Fase 2, al corregir el defecto #37. Se describe en el paso 5.

## 4. Cambios en la matriz de roles

La revisión RV-01 (paso 3) corrigió la matriz antes de derivar de ella los datos de prueba. Siguiendo la opción A de H-03, Settings del Administrador pasó de CRUD a «RU (propia)» (PR #13). Desde ese cambio, la matriz corregida es la fuente de los datos PC-0 y de los resultados esperados.
