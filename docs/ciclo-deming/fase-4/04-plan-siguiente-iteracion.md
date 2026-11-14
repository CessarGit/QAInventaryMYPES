# Fase 4 · Plan de la iteración 2

La iteración 2 repite el ciclo PHVA con el mismo esquema de 10 pasos. Su alcance sale de las acciones AC-01 a AC-11; AC-12 se cierra en la iteración 1.

## 1. Alcance por fase

| Fase | Pasos | Qué se hace | Acciones |
|---|---|---|---|
| 1 · Planificar | 1 y 2 | Activar la revisión en pareja obligatoria; casos CP-21 a CP-24 y casos para S2 y S6; actualizar la trazabilidad y el plan con las reglas E-1 a E-8 | AC-06, AC-03, AC-10 |
| 2 · Hacer | 3 | RV-05 (Products) y revisión de Reports antes de probar; RV-02 (modelo de datos) | AC-02, AC-11 |
| 2 · Hacer | 4 | Respaldo automático, cifrado y con segunda copia; simulacro con T0 anotado | AC-07 |
| 2 · Hacer | 5 | Confirmar y corregir #6, #16, #17 y #18; cerrar #38, #42, #43 y #44; integración descendente E1 a E3; centralizar permisos (H-01); cobertura ≥ 60 %; técnicas y aceptación; controles de seguridad | AC-01, AC-03, AC-04, AC-05, AC-08, AC-10, AC-11 |
| 2 · Hacer | 6 y 7 | Actualizar a CodeIgniter 3.1.13 contra `pruebas-base-3.1.6`; reversión RB-06 si hay regresión; plan de migración de PHP y MySQL | AC-09 |
| 3 · Verificar | 8 y 9 | Medir las mismas métricas y compararlas con la iteración 1; lista de 12 puntos; auditoría externa | — |
| 4 · Actuar | 10 | Retrospectiva, acciones y decisión de aceptación | — |

## 2. Orden de prioridad

El orden sigue la regla de riesgo del informe (Tabla 10 de pruebas: ventas, stock y permisos primero) y las dependencias técnicas:

1. **AC-06**, revisión en pareja obligatoria: va primero porque desde ese momento todo cambio cuenta para la métrica.
2. **AC-03 y AC-04**, defectos y hallazgos altos: tocan dinero, stock y permisos.
3. **AC-02**, RV-05 y Reports: deben hacerse antes de probar esos módulos.
4. **AC-05**, cierre de hallazgos mayores: avanza a medida que se completan los anteriores.
5. **AC-10**, Top-Down (H-01): requiere la suite en verde como red de seguridad.
6. **AC-01**, cobertura: después de AC-10, porque la centralización cambia las líneas de los controladores.
7. **AC-08 y AC-11**, técnicas, aceptación y seguridad.
8. **AC-07**, respaldo automático: necesita al menos 2 ciclos de copia antes del simulacro.
9. **AC-09**, actualización del framework: al final, cuando la línea base de regresión ya incluya todas las correcciones.

## 3. Metas de la iteración 2

Son las mismas metas del informe. La columna central indica dónde quedó la iteración 1:

| Métrica | Iteración 1 | Meta de la iteración 2 |
|---|---|---|
| Cobertura de Orders y Products | 49,06 % y 38,07 % | ≥ 60 % |
| Cambios con revisión en pareja | 0 % con par | 100 % |
| Cobertura de revisión de riesgo alto | 75 % | 100 % |
| Cierre de hallazgos mayores | 21,62 % | 100 % (o diferidos con issue) |
| Defectos críticos o altos abiertos | 0 confirmados; 4 hallazgos altos sin confirmar | 0, sin salvedades |
| Casos que pasan | 19 / 20 | Todos |
| Técnicas y niveles | 1 de 5 técnicas completas (2 parciales); 3 de 4 niveles completos | 5 de 5 y 4 de 4 |
| RPO con respaldo automático | No cumple | ≤ 1 h |
| DPMO total | 14 285,7 (4 subsistemas) | Medido en los 6 subsistemas y sin aumentar |
| Casos que pasan tras actualizar CodeIgniter | No aplica | Sin regresiones |
