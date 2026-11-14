# Fase 2 · Objetivos y criterio de salida

## 1. Objetivos que debía cumplir

Todos los umbrales salen tal cual del informe. La columna «Resultado» viene de las evidencias de los pasos 3, 4 y 5 y no se redondea.

| Paso | Objetivo | Meta | Fuente en el documento | Resultado | ¿Cumple? |
|---|---|---|---|---|---|
| 3 | Revisar todos los productos de riesgo alto antes de probar | 100 % (4 de 4) | Tabla 19 de revisiones | 3 de 4 = 75 %. Falta RV-05 (Products) | **No** |
| 3 | Mantener el esfuerzo de cada revisión en un nivel razonable | ≤ 10 horas por revisión | Tabla 19 de revisiones | RV-01 2,15 h; RV-03 1,4 h; RV-04 0,7 h; RV-10 1,4 h | Sí |
| 3 | Fijar la línea base de densidad de error | Medida en las primeras revisiones | Tabla 19 de revisiones | Matriz: 233,3 por mil celdas. Código: 8,6 y 12,0 por mil líneas | Sí |
| 4 | Restaurar sin pérdida de registros | Mismo número de registros | Tabla 20 de recuperación | Las 12 tablas coinciden | Sí |
| 4 | Recuperar ventas y accesos a tiempo | RTO ≤ 4 h | Tabla 9 de recuperación | 0,06 h | Sí |
| 4 | Limitar la pérdida de datos | RPO ≤ 1 h con respaldo automático | Tablas 9 y 11 de recuperación | Menos de 0,14 h (cota), pero con respaldo **manual**, no automático | **Parcial** |
| 5 | Diseñar y ejecutar casos suficientes | ≥ 20 casos; ≥ 90 % ejecutados | Tablas 13 y 21 de PHVA | 20 casos; 20 de 20 = 100 % ejecutados | Sí |
| 5 | Probar la lógica crítica | Cobertura ≥ 60 % en ventas y productos | Tabla 13 de PHVA | Orders 49,06 %; Products 38,07 % | **No** |
| 5 | Aplicar los niveles y técnicas elegidos | 4 niveles y 5 técnicas | Tablas 16 y 17 de PHVA (ver nota) | 4 de 4 niveles. 3 de 5 técnicas: caja negra, valores límite y seguridad. Ni caja blanca ni usabilidad | **Parcial** |
| 5 | Comprobar que cada rol solo accede a lo suyo | 0 accesos indebidos | Tabla 13 de seguridad | Ronda 1: 1 (CP-15, escalamiento). Ronda 2: 0 en los casos ejecutados | Sí, en la ronda 2 (ver nota) |
| 5 | Liberar sin defectos graves | 0 críticos y 0 altos abiertos | Tabla 13 de PHVA y Tabla 9 de pruebas | 0 defectos críticos y 0 altos abiertos | Sí (ver nota) |
| 5 | Corregir rápido lo crítico | ≤ 1 iteración | Tabla 19 de PHVA | #34 se detectó y se corrigió en la misma iteración | Sí |
| Todos | Que ningún cambio entre sin revisión | 100 % de los cambios revisados | Tabla 13 de PHVA | 100 % de los cambios de la fase entraron por PR, con la lista L1 y la firma SQA, pero revisados por el autor, no por un par | **Parcial** |

**Resumen:** 8 objetivos se cumplen, 3 en parte y 2 no.

**Notas:**
- **Niveles y técnicas.** Las Tablas 16 y 17 de PHVA no están en las fuentes de diseño del repositorio. Se evaluó con sus equivalentes: la Tabla 6 de pruebas para los niveles (unitaria, integración, sistema y aceptación) y la Tabla 7 para las técnicas (caja negra, caja blanca, valores límite, seguridad y usabilidad; el estrés no se aplica).
- **Accesos indebidos.** «0» se refiere a los casos ejecutados. La confirmación de H-01 (Fase 1) detectó 20 métodos públicos que no comprueban el permiso, y ningún caso los prueba uno por uno (issue #4).
- **Defectos graves.** «0» se refiere a los defectos confirmados por una prueba. Siguen abiertos cuatro issues de mantenimiento de severidad alta (#6, #16, #17 y #18). Son hallazgos de revisión que ninguna prueba confirmó, y por eso no se registraron como defectos.

## 2. Criterio de salida

Para pasar a la Fase 3 (Verificar) debían cumplirse cuatro condiciones:

| Condición | Resultado | ¿Cumple? |
|---|---|---|
| Revisiones de riesgo alto completas | 3 de 4; RV-05 no se realizó | **No** |
| Recuperación demostrada dentro de las metas | Restauración sin pérdida; RTO 0,06 h; RPO menor de 0,14 h. Los respaldos no son automáticos | Sí, con la salvedad del respaldo manual |
| Todos los casos ejecutados | 20 de 20, en dos rondas | Sí |
| Ningún defecto crítico o alto abierto | 0 críticos y 0 altos entre los defectos registrados | Sí, con la salvedad de los issues de mantenimiento #6, #16, #17 y #18 |

## 3. ¿Cumplimos los objetivos y los criterios de salida?

**No del todo.**

**Objetivos:** se cumplen 8 de 13. No se cumplen la cobertura de revisión (75 %) ni la cobertura de pruebas (49,06 % y 38,07 %). Se cumplen solo en parte el RPO (el respaldo no es automático), las técnicas de prueba (3 de 5) y la revisión de los cambios (sin par independiente).

**Criterio de salida:** se cumplen 3 de 4 condiciones. Falta la de las revisiones de riesgo alto completas, porque RV-05 no se realizó. Las otras dos con salvedad tienen el riesgo registrado.

**Decisión registrada:** la fase se cerró y se pasó a la Fase 3 con estas brechas declaradas, sin redondear ni reinterpretar ningún resultado:
- RV-05 no realizada;
- cobertura por debajo del 60 %;
- respaldos manuales;
- cuatro issues de mantenimiento de severidad alta abiertos.

El paso 6 (CodeIgniter 3.1.13) se difirió a la próxima iteración (#52), y el paso 7 no aplica. Estas brechas son la entrada de la Fase 4 (Actuar).

**Evidencia:** `docs/calidad/registro-ejecucion.md`, `docs/calidad/revisiones/README.md`, `docs/ejecucion/evidencias/fase2-verificacion.txt`, PR #53.
