# RV-10 · Scripts de respaldo, restauración y verificación

## 1. Datos de la revisión

| Campo | Contenido |
|---|---|
| Producto y versión | RV-10: `scripts/respaldo.sh` (62 líneas), `scripts/restaurar.sh` (66), `scripts/verificar-conteos.sh` (77) y `backups/.htaccess` (4); rama `chore/scripts-respaldo`, antes del primer commit. Las líneas citadas en las secciones 2 y 3 son las de la versión revisada, antes de las correcciones |
| Riesgo y técnica planificada | Medio, walkthrough (Tabla 7 de revisiones) |
| Técnica aplicada | Verificación de escritorio (autor) + segunda pasada (Claude Code como apoyo, no como revisor humano independiente). Los scripts no se ejecutaron antes de la revisión (PROMPTS-FASE2.md, 4.1) |
| Lista usada | L5 (Tabla 15 de revisiones) |
| Participantes y roles | Productor: Claude Code (subpaso 4.1, plan aprobado por el autor). Revisor y registrador: César Roque. Apoyo: Claude Code |
| Fechas de las pasadas | Autor (César Roque): 14/11/2026 · Claude: 14/11/2026 |
| Desviación | La Tabla 7 prevé un walkthrough guiado por el autor ante el equipo; se trabaja solo. Se compensa con la lista y la doble pasada. El productor de los scripts es también quien hace la segunda pasada (Claude Code), lo que reduce la independencia de esa pasada |

## 2. Respuestas a la lista

| N.º | Pregunta | Respuesta (Sí / No / N/A) | Evidencia (`archivo:línea`) | Hallazgo |
|---|---|---|---|---|
| 1 | ¿Cada paso del script o del manual tiene un resultado esperado y verificable? | No (parcial) | Mensajes de inicio y fin, ruta y suma (`scripts/respaldo.sh:51`, `:60-62`); verificación de la suma (`scripts/restaurar.sh:51-53`); códigos de salida 2 (`scripts/verificar-conteos.sh:45`), 0 y 1 (`:72-77`). Pero si la consulta de conteo falla en las dos bases, el script declara que coinciden (`scripts/verificar-conteos.sh:59-60`, `:68`) | H-RV10-02 |
| 2 | ¿El script de respaldo indica qué se respalda, cada cuánto y dónde se guarda la copia? | Sí (con una omisión) | Qué, cada cuánto y dónde en la cabecera (`scripts/respaldo.sh:6-17`). La retención de la Tabla 11 (48 h, 30 días, 12 meses) se cita pero no se aplica: las copias se acumulan en `backups/` | H-RV10-03 |
| 3 | ¿Se probó la restauración del respaldo y el procedimiento para volver atrás? | No | Todavía no: la restauración se prueba en el 4.2 (Tabla 20) y el procedimiento completo en el simulacro RB-01 del 4.3 | H-RV10-05 |
| 4 | ¿Las credenciales y claves están fuera del código y del manual? | Sí | Solo aparece el nombre de la variable; el valor se resuelve dentro del contenedor (`scripts/respaldo.sh:38`, `:54`; `scripts/restaurar.sh:58`, `:62`; `scripts/verificar-conteos.sh:37`). `MYSQL_PWD` no añade exposición: el contenedor ya tiene `MYSQL_ROOT_PASSWORD` en su entorno (`docker-compose.yml:44`) | — |
| 5 | ¿El manual cubre todos los roles y todas las operaciones de su matriz de permisos? | N/A | Los scripts no son un manual de usuario ni operan sobre roles | — |
| 6 | ¿El lenguaje y las capturas corresponden a la versión vigente del sistema? | Sí | Las 12 tablas coinciden con `stock.sql` (`scripts/verificar-conteos.sh:32-33`; `stock.sql:31-248`); el servicio `db` y la variable coinciden con `docker-compose.yml:37`, `:44`; utf8 como `stock.sql` | — |
| 7 | ¿Se identifican los riesgos del despliegue y su tratamiento? | No (parcial) | Tratados: restaurar sobre `stock` exige `--forzar` (`scripts/restaurar.sh:38-41`), suma sha256 (`:51-53`), respaldo a medias eliminado (`scripts/respaldo.sh:48-49`, `:57`), acceso web denegado (`backups/.htaccess:4`). Sin tratar: permisos de lectura de los respaldos y escrituras de la aplicación durante una restauración sobre `stock` | H-RV10-01, H-RV10-04 |
| 8 | ¿Un usuario de la MYPE pudo seguir los pasos sin ayuda del equipo? | N/A | En staging solo opera el autor; L5 asigna esta pregunta también al paso 10, donde se evalúa con la documentación final | — |

Cada "No" es un hallazgo y va a la sección 3.

## 3. Lista de problemas (Tabla 16)

| ID | Ubicación | Descripción objetiva | Tipo | Gravedad | Encontrado por | Responsable | Estado |
|---|---|---|---|---|---|---|---|
| H-RV10-01 | `scripts/respaldo.sh:45-57` | Los respaldos se crean con los permisos por defecto del host (`umask 0002`: archivos `rw-rw-r--`), legibles por cualquier usuario del equipo. Contienen los hashes de las contraseñas y los datos de clientes y ventas | Defecto de diseño | Mayor (datos) | Claude | César Roque | Corregido (`umask 077`); se verifica con `ls -l backups/` en el 4.2 |
| H-RV10-02 | `scripts/verificar-conteos.sh:59-60`, `:68` | Los conteos se leen con sustitución de procesos (`< <(...)`), donde `set -e` no detiene un fallo. Si la consulta falla en las dos bases (por ejemplo, falta la misma tabla), ambas columnas quedan en `?`, se comparan como iguales y el script termina con 0: puede declarar íntegra una restauración que no lo es | Defecto de diseño | Mayor (datos) | Claude | César Roque | Corregido (conteos en variables; fallo de consulta o conteo ausente → código 2). Verificado con un `docker` simulado: consulta fallida → 2, iguales → 0, distintos → 1. Se verifica con datos reales en el 4.2 |
| H-RV10-03 | `scripts/respaldo.sh:10-12` | La retención de la Tabla 11 (48 h, 30 días, 12 meses) no se aplica: las copias se acumulan en `backups/` sin límite | Omisión | Menor | Claude | César Roque | Abierto |
| H-RV10-04 | `scripts/restaurar.sh:38-41`, `:61-62` | Al restaurar sobre `stock` con `--forzar` no se advierte que la aplicación sigue en marcha: una venta registrada durante la restauración se mezclaría con los datos restaurados | Omisión | Menor | Claude | César Roque | Corregido (advertencia si `app` está en marcha) |
| H-RV10-05 | Proceso (L5-3) | La restauración y la vuelta atrás aún no se han probado | Omisión | Menor | Claude | César Roque | Abierto (se cierra en 4.2 y 4.3) |

Criterio de gravedad (Tabla 17): **mayor** si corregirlo toma más de 1 hora-persona, o si afecta permisos, dinero, existencias o datos (aunque sea fácil de corregir). **Menor** en cualquier otro caso.

## 4. Métricas

| Métrica | Valor |
|---|---|
| Ep: preparación del autor (horas) | 0,4 |
| Ea: revisión del autor (horas) | 1,0 |
| Er: corrección (horas, si aplica) | 0,02 (≈ 1 min, de 16:52 a 16:53 UTC del 14/11/2026: correcciones de H-RV10-01, -02 y -04 aplicadas por Claude Code y su verificación simulada) |
| WPS: tamaño revisado (líneas, con `wc -l`) | 209 |
| Hallazgos mayores / menores | 2 / 3 |
| Densidad de error (hallazgos / WPS × 1000) | 5 / 209 × 1000 = 23,9 |
| Hallazgos encontrados solo por el autor | 0 |
| Hallazgos encontrados solo por Claude | 5 |
| Hallazgos encontrados por ambos | 0 |

## 5. Informe resumen y decisión (Tabla 18)

| Campo | Contenido |
|---|---|
| Decisión | Aceptar con correcciones |
| Justificación | Los dos hallazgos mayores afectan la confidencialidad de los respaldos (H-RV10-01) y la fiabilidad de la verificación de la restauración (H-RV10-02), pero ninguno obliga a rehacer los scripts ni impide verificarlos, que es el criterio de rechazo de la Tabla 18. Las correcciones son acotadas y verificables sin otra reunión: (1) H-RV10-01, `umask 077` al inicio de `respaldo.sh`, verificable con `ls -l backups/`; (2) H-RV10-02, leer los conteos en variables (donde `set -e` sí actúa) y tratar un conteo ausente como error (código 2), verificable comparando una base con una tabla inexistente; (3) H-RV10-04, advertencia al restaurar sobre `stock`. Se aplican antes del primer commit y antes de ejecutar los scripts en el 4.2. H-RV10-03 (retención) queda como desviación del staging, donde el respaldo es manual. H-RV10-05 se cierra con la prueba de restauración (4.2) y el simulacro RB-01 (4.3) |
| Hallazgos que pasan a pruebas | H-RV10-02 → prueba de la corrección con una base incompleta. H-RV10-05 → 4.2 (Tabla 20) y 4.3 (RB-01) |
| Hallazgos sin caso de prueba | H-RV10-01 (se verifica con `ls -l`), H-RV10-03, H-RV10-04 |
| Issues creados | Ninguno: los mayores se corrigen antes de integrar los scripts |

**Firma del revisor:** César Roque · fecha: 14/11/2026 (registrada por Claude Code con autorización del autor en el chat)
