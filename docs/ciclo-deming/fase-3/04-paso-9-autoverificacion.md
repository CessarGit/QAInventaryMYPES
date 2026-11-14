# Fase 3 · Paso 9: autoverificación

El informe prevé una auditoría interna cruzada (Tabla 14 de auditoría), que no es posible con un solo autor. En su lugar se hace una **autoverificación** con la lista de la Tabla 17, siguiendo las fases del plan de auditoría (Tabla 16). La independencia se busca con la **auditoría externa** del docente, para la cual se prepara un paquete de evidencias.

| Subpaso | Fase del plan (Tabla 16) | Fecha de desarrollo | Estado |
|---|---|---|---|
| 9.1 Alcance y criterios | 1. Planear | Por registrar | Definido en este archivo |
| 9.2 Lista de verificación de 12 puntos | 2. Solicitar evidencia | Por registrar | Evaluada con las evidencias de las Fases 1 y 2 |
| 9.3 Observaciones O-01 a O-06 | 2. Solicitar evidencia | Por registrar | Evaluadas |
| 9.4 Auditoría de cumplimiento | Tabla 20 de PHVA | Por registrar | Evaluada |
| 9.5 Prueba independiente | 4. Probar de forma independiente | 14/11/2026 | Ejecutada: 4 de 4 casos pasan |
| 9.6 Paquete para el auditor externo | 5. Informar | 14/11/2026 | Preparado; la entrega al docente queda a cargo del autor |

La fase 3 del plan («Entrevistar a quien desarrolló cada módulo») no aplica: el código es de un proyecto de terceros y el autor de la calidad es el único participante. El auditor externo puede entrevistar al autor.

## 9.1 Alcance y criterios (Tabla 15 de auditoría)

| Aspecto | Qué se revisa | Evidencia |
|---|---|---|
| Eficacia de los controles | Acceso por rol, validación de datos, contraseñas, respaldos | CP-04 a CP-10, CP-14 a CP-18; evidencias 4.2 y 4.3 |
| Cumplimiento | Ramas, commits, aprobación por PR, versiones soportadas | Historial de git y de PR; configuración de GitHub |
| Calidad de los datos | Integridad referencial; stock coherente con las ventas | Esquema de la base; CP-01, CP-09, CP-12 |
| Eficiencia y uso | Si el sistema reemplaza los registros manuales | No aplica: no hay MYPE real |

Criterios: matriz de roles corregida (PR #13), plan de pruebas (PR #12), estrategia de ramas y Conventional Commits.

## 9.2 Lista de verificación (Tabla 17 de auditoría)

| N.º | Punto de control | Resultado | Evidencia | Observación |
|---|---|---|---|---|
| 1 | Los permisos de la tabla de grupos coinciden con la matriz | **Parcial** | PC-0 en `stock_test` (mapeo de RV-01); CP-04 a CP-07 y CP-13 pasan | Coinciden en `stock_test`. En `stock` solo existen los 2 grupos heredados: la matriz no está configurada en la base del staging |
| 2 | Las contraseñas se guardan con hash | **Sí** | CP-14 (D-04); revisión RV-04 | — |
| 3 | El acceso directo a la base de producción está restringido | **N/A** | — | No hay producción. En staging, la base no publica puertos |
| 4 | Los cambios llegan por PR con revisión por pares y firma SQA | **Parcial** | 27 PR fusionados (del #2 al #53): 27/27 con la integración continua en verde y 17/27 con la casilla de firma SQA marcada | 0/27 con un revisor distinto del autor. 6 de 48 commits entraron sin PR antes de la protección de `main` (paso 8, nota 2) |
| 5 | Los commits siguen la convención | **Parcial** | `git log linea-base..main` (48 commits) | 47/48 = 97,92 % en formato Conventional Commits y en español. La excepción es `84d55c4`, «Agregar los documentos de gestión de calidad» (13/11/2026 18:28), anterior al flujo de trabajo y sin PR |
| 6 | Hay casos documentados con sus resultados | **Sí** | `trazabilidad.md`; `registro-ejecucion.md` (rondas 1 y 2) | El Apéndice A citado no existe; se sustituye por las fichas y la plantilla Defecto |
| 7 | Los defectos tienen severidad, responsable y cierre | **Parcial** | Issues #34 a #44 | Severidad y cierre, sí. Responsable asignado solo en 5 de 23 issues (#4 a #8); ningún defecto (#34 a #44) lo tiene (desviación de la Fase 1, subpaso 1.3) |
| 8 | Hay un changelog actualizado por versión | **Parcial** | `CHANGELOG.md` | Actualizado, pero todo está en «Unreleased»: no se ha publicado ninguna versión |
| 9 | Hay respaldos y se probó su restauración | **Sí** | Evidencias 4.2 y 4.3 | Con salvedad: respaldos manuales, sin la frecuencia de la Tabla 11 |
| 10 | Versiones soportadas o con plan de actualización | **Parcial** | Issue #52 | Hay plan para CodeIgniter 3.1.13 (iteración 2). No hay plan para PHP 7.4 ni MySQL 5.7, que tampoco tienen soporte |
| 11 | Las tablas usan InnoDB con llaves foráneas | **No** | `stock.sql:31-252` (inventario 1.0) | 12 tablas InnoDB sin ninguna llave foránea |
| 12 | El stock coincide con las ventas registradas | **Parcial** | CP-01, CP-09 y CP-12 pasan | Al crear una venta, sí (PR #47). Al editarla o eliminarla no está verificado (#16); concurrencia sin probar |

**Resultado: 2 Sí, 8 Parcial, 1 No y 1 N/A.** Los puntos 4, 5 y 7 se confirmaron en GitHub el 14/11/2026.

## 9.3 Observaciones preliminares (Tabla 18 de auditoría)

| ID | Observación del informe | Estado | Evidencia |
|---|---|---|---|
| O-01 | No hay casos ni pruebas unitarias | **Resuelta** | 20 casos; 29 pruebas en PHPUnit en la integración continua |
| O-02 | CodeIgniter 3.1.6 frente a 3.1.13 | **Abierta, con plan** | Issue #52 (iteración 2); línea base `pruebas-base-3.1.6` |
| O-03 | PHP 7.4 sin soporte | **Abierta, sin plan** | No hay plan de migración documentado |
| O-04 | Sin repositorio central ni registro de tareas | **Resuelta** | Issues, PR, `main` protegida, integración continua |
| O-05 | La figura muestra XAMPP y el staging usa Docker | **Resuelta** | `docs/arquitectura/despliegue.md` |
| O-06 | Catálogo de clientes sin módulo | **Resuelta como decisión** | H-05 confirmado; fuera del alcance (issue #7, perfectivo) |

## 9.4 Auditoría de cumplimiento (Tabla 20 de PHVA)

El informe prevé una auditoría de cumplimiento antes del cierre: protección de datos y reglas del curso.

| Aspecto | Resultado | Observación |
|---|---|---|
| Datos personales (Tabla 16 de seguridad) | **Parcial** | Los datos del cliente en `orders` se escapan desde PR #49. Los campos obligatorios del alta de usuario no se compararon con el criterio de datos mínimos |
| Credenciales y secretos | **Cumple** | Fuera del repositorio; clave expuesta rotada |
| Política de privacidad y de contraseñas | **No cumple** | No están definidas (Tabla 17 de seguridad, etapa inmediata) |
| Licencias de terceros | **Observación** | El código proviene de un proyecto de terceros con licencia MIT, y en la línea base se decidió no restaurar su LICENSE. La licencia MIT exige conservar el aviso de copyright y de permiso en las copias. Debe revisarse antes de distribuir el sistema |
| Uso de IA | **Cumple** | Declarado en la bitácora; cada salida la revisó y firmó el autor |

## 9.5 Prueba independiente (fase 4 del plan de auditoría)

El plan pide «ejecutar casos de control propios». Se ejecutaron sin usar la suite automatizada, sobre `main` (`d0bbb34`), el 14/11/2026 a las 16:10 CST:
- C1 a C3, con los usuarios PC-0 en `stock_test`;
- C4, sobre `stock`.

| Caso de control | Resultado esperado | Resultado obtenido | ¿Pasa? |
|---|---|---|---|
| C1 · Entrar como Bodega y abrir la dirección de una venta | Acceso denegado | Redirigido al panel; no recibe el formulario de la venta | Sí |
| C2 · Registrar una venta con cantidad 0 y otra con más unidades que el stock | Rechazo en ambos casos; el stock no cambia | Ninguna venta registrada; el stock sigue en 4, tras la venta de 1 unidad que creó C1 | Sí |
| C3 · Entrar como Ventas y enviar Settings con un campo de grupo añadido | El grupo no cambia | Formulario procesado («Successfully updated»); el grupo sigue en 11 (Ventas) y no pasa a 14 (Administrador) | Sí |
| C4 · Restaurar el último respaldo en una base aparte y comparar los conteos | Las 12 tablas coinciden | Respaldo `stock_20261114_161024` con suma sha256 correcta, restaurado en `stock_restore`: las 12 tablas coinciden | Sí |

**Evidencia:** `docs/ejecucion/evidencias/9.5-casos-de-control.txt`.

**Desviaciones:**
- C1 a C3 no se hicieron en el navegador. Claude Code los ejecutó por HTTP, con sesión, cookies, token CSRF y formularios reales.
- La prueba no es independiente del productor: la independencia la aporta el auditor externo, que repite estos casos (9.6).
- En C3 se usó una contraseña temporal de 12 caracteres, porque Settings exige al menos 8 y la de PC-0 es más corta. El primer intento, con la de PC-0, no se procesó y no se contó como resultado. `stock_test` se recreó al terminar.

## 9.6 Paquete para el auditor externo

El paquete se entrega al docente para la auditoría externa (Tabla 14 de auditoría). Se comprobó que cada archivo existe. Los marcados «sin versionar» deben subirse al repositorio antes de la entrega.

| N.º | Contenido | Archivos | Estado |
|---|---|---|---|
| 1 | Fuentes de diseño y matriz corregida | `docs/calidad/fuente-informe.md`, `docs/calidad/fuente-informe-2.md`, `docs/calidad/matriz-roles.md` | Versionados |
| 2 | Requisitos, casos, decisiones y resultados | `docs/calidad/trazabilidad.md`, `docs/calidad/decisiones-autor.md`, `docs/calidad/registro-ejecucion.md`, `docs/ejecucion/evidencias/5.7-suite-base-3.1.6.txt`, `docs/ejecucion/evidencias/5.4-pruebas-manuales/guion.md` | Versionados |
| 3 | Revisiones e indicadores | `docs/calidad/revisiones/RV-01-matriz.md`, `RV-03-orders.md`, `RV-04-usuarios-permisos.md`, `RV-10-scripts.md` y `README.md` | Versionados |
| 4 | Restauración y simulacro | `docs/ejecucion/evidencias/4.2-restauracion.txt`, `docs/ejecucion/evidencias/4.3-simulacro-rb01/simulacro-rb01.md` | Versionados |
| 5 | Lista de verificación y métricas del paso 8 | `docs/ciclo-deming/fase-3/04-paso-9-autoverificacion.md`, `docs/ciclo-deming/fase-3/03-paso-8-medir.md` | **Sin versionar** |
| 6 | Casos de control para repetir | Sección 9.5 de este archivo y `docs/ejecucion/evidencias/9.5-casos-de-control.txt` | **Sin versionar** |
| 7 | Contexto del proceso | `docs/calidad/hallazgos-topdown.md`, `docs/calidad/plan-pruebas-iteracion-1.md`, `docs/ejecucion/bitacora.md`, `CHANGELOG.md` | Versionados |
| 8 | Acceso | Repositorio en GitHub (issues, PR y ejecuciones de la integración continua), con acceso de lectura para el docente | A cargo del autor |

**Cómo repetir los casos de control (para el auditor):**
1. Levantar el staging con `docker compose up -d`.
2. Recrear la base de pruebas con `bash scripts/preparar-stock-test.sh`.
3. Iniciar la aplicación contra `stock_test`.
4. Entrar con los usuarios `bodega_test`, `ventas_test` y `gerente_test` de PC-0 y seguir C1 a C3.
5. Para C4: `bash scripts/respaldo.sh`, `bash scripts/restaurar.sh <último respaldo> stock_restore` y `bash scripts/verificar-conteos.sh stock stock_restore`.

**Pendiente del autor:** versionar los archivos marcados, dar acceso al docente y entregar el paquete.
