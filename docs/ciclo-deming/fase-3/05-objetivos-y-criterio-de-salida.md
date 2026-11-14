# Fase 3 · Objetivos y criterio de salida

## 1. Objetivos que debe cumplir

| Paso | Objetivo | Meta | Fuente en el documento | Resultado | ¿Cumple? |
|---|---|---|---|---|---|
| 8 | Medir todas las métricas definidas en el informe | 100 % de las métricas con valor o con motivo de «no calculable» | Tablas 13 y 19 de PHVA; Tabla 19 de revisiones; Tabla 13 de mantenimiento; Tablas 9 y 10 de Top-Down; Tabla 9 de recuperación | Todas tienen valor, salvo la eficacia de eliminación de defectos (no calculable sin liberación) y el DPMO de S2 y S6 (sin casos) | Sí; los 4 valores pendientes se confirmaron en GitHub el 14/11/2026 |
| 8 | Reportar sin redondear ni reinterpretar | 0 valores ajustados | Regla de medición honesta | Se corrigieron tres evaluaciones de la Fase 2 a la baja (técnicas, aceptación, punto 1) | Sí |
| 8 | Medir la calidad del diseño modular | DPMO por subsistema | Tablas 9 y 10 de Top-Down | Ronda 1: 114 285,7. Ronda 2: 14 285,7. S2 y S6 no medibles | Sí |
| 9 | Aplicar la lista de verificación completa | 12 de 12 puntos evaluados con evidencia | Tabla 17 de auditoría | 12 de 12 evaluados; el punto 5 se confirmó como Parcial (47/48 commits en la convención) | Sí |
| 9 | Actualizar las observaciones preliminares | O-01 a O-06 con estado | Tabla 18 de auditoría | 6 de 6 | Sí |
| 9 | Auditoría de cumplimiento antes del cierre | Ejecutada | Tabla 20 de PHVA | Evaluada, con 2 incumplimientos y 1 observación | Sí |
| 9 | Prueba independiente con casos de control propios | Ejecutada | Tabla 16 de auditoría, fase 4 | 4 de 4 casos pasan, por HTTP y con los scripts, no en el navegador (`9.5-casos-de-control.txt`) | Sí |
| 9 | Preparar la auditoría externa | Paquete entregado | Tabla 14 de auditoría | Paquete preparado y comprobado; faltan versionar 3 archivos y entregarlo | **Parcial** |

## 2. Criterio de salida

Para pasar a la Fase 4 (Actuar) deben cumplirse tres condiciones:

| Condición | Resultado | ¿Cumple? |
|---|---|---|
| Cada métrica del informe tiene valor, umbral y estado | Sí; los 4 valores se confirmaron en GitHub. Dos cambian a la baja: los PR con firma SQA son 17/27, no el 100 %, y los defectos correctivos del mes son 16, no 10 | Sí |
| La lista de verificación está completa y cada «No» o «Parcial» tiene evidencia | Completa: el punto 5 queda en Parcial, con evidencia | Sí |
| Las brechas están consolidadas en una lista única | Ver la sección 3 | Sí |

La prueba independiente y el paquete externo no bloquean el paso a Actuar, pero deben estar hechos antes de cerrar la iteración. La prueba independiente se ejecutó el 14/11/2026. Del paquete externo solo falta que el autor lo entregue.

## 3. Brechas consolidadas (entrada de la Fase 4)

| ID | Brecha | Origen | Métrica o punto afectado |
|---|---|---|---|
| B-01 | Cobertura de Orders y Products por debajo del 60 % | Paso 8.1 | Tabla 13 de PHVA; mejora 1 de la Tabla 21 |
| B-02 | Ningún cambio con revisión de un par; commits sin PR en la Fase 1 | Pasos 8.1 y 9.2 (punto 4) | Tabla 13 de PHVA |
| B-03 | RV-05 no realizada; Reports sin revisión | Pasos 8.3 y 3 | Tabla 19 de revisiones; #37 y #38 escaparon a las revisiones |
| B-04 | Cierre de hallazgos mayores: 21,62 % | Paso 8.3 | Tabla 19 de revisiones |
| B-05 | Cuatro hallazgos altos sin confirmar ni descartar (#6, #16, #17, #18) | Paso 8.1, nota 1 | Tabla 9 de pruebas (ningún alto abierto) |
| B-06 | Defectos abiertos #38, #42, #43 y #44 | Paso 5 | Tabla 9 de pruebas (registrados con plan) |
| B-07 | Respaldos manuales, sin retención, sin cifrado ni segunda copia | Pasos 8.6 y 9.2 (punto 9) | Tablas 9, 11 y 20 de recuperación |
| B-08 | Técnicas dinámica y exploratoria sin aplicar; estática y caja blanca parciales | Paso 8.7 | Tabla 17 de PHVA |
| B-09 | Aceptación sin un usuario de prueba | Paso 8.7 | Tabla 16 de PHVA |
| B-10 | H-01 sin aplicar (20 métodos sin permiso); integración descendente no aplicada como secuencia | Pasos 8.5 y 8.7 | Tablas 8, 9 y 11 de Top-Down |
| B-11 | S2 y S6 sin casos de prueba | Paso 8.5 | Tabla 10 de Top-Down |
| B-12 | Seguridad: HTTPS, política de contraseñas y de privacidad, sesión (#42), XSS en usuarios y grupos (#18) | Pasos 8.7 y 9.4 | Tablas 13, 16 y 17 de seguridad |
| B-13 | Matriz sin configurar en `stock`; sin llaves foráneas | Paso 9.2 (puntos 1 y 11) | Tabla 17 de auditoría |
| B-14 | CodeIgniter 3.1.6 (#52) y PHP y MySQL sin plan | Pasos 9.2 (punto 10) y 9.3 (O-02, O-03) | Tabla 11 de mantenimiento |
| B-15 | Changelog sin versiones; responsable no asignado en defectos | Paso 9.2 (puntos 7 y 8) | Tabla 17 de auditoría |
| B-16 | Licencia de terceros no conservada | Paso 9.4 | Cumplimiento general |
| B-17 | Auditoría externa pendiente: paquete preparado, sin entregar (la prueba independiente se ejecutó y pasó) | Pasos 9.5 y 9.6 | Tablas 14 y 16 de auditoría |

## 4. ¿Cumplimos los objetivos y el criterio de salida?

**Sí, salvo la entrega del paquete externo.** La medición está completa y confirmada en GitHub, la lista de verificación tiene sus 12 puntos evaluados y la prueba independiente pasó 4 de 4 casos. Se cumplen las 3 condiciones del criterio de salida.

Queda una sola tarea, a cargo del autor y antes de cerrar la iteración: versionar los 3 archivos marcados en 9.6 y entregar el paquete externo.

Las 17 brechas pasan a la Fase 4.
