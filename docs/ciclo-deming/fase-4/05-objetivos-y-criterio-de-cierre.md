# Fase 4 · Objetivos y criterio de cierre de la iteración

## 1. Objetivos que debe cumplir

| Subpaso | Objetivo | Meta | Fuente en el documento | Resultado | ¿Cumple? |
|---|---|---|---|---|---|
| 10.1 | Retrospectiva con datos | Qué funcionó, qué no y lecciones, cada punto con su dato | Tabla 23 de PHVA | 6 prácticas que funcionaron, 7 problemas y 5 lecciones | Sí |
| 10.2 | Una acción por brecha | 17 de 17 brechas con acción | Tabla 21 de PHVA | 17 brechas cubiertas por 12 acciones con los cinco pasos | Sí |
| 10.2 | Acciones SMART | Cada acción con objetivo, métrica y plazo | Tabla 21 de PHVA | 12 de 12 | Sí |
| 10.3 | Estandarizar | Reglas aplicables desde la iteración 2 | Tabla 21 de PHVA | 8 reglas (E-1 a E-8), incorporadas como reglas 18 a 25 de `CLAUDE.md` y a la plantilla de PR | Sí (falta el commit) |
| 10.4 | Reevaluar la madurez | 5 áreas con su nivel | Tabla 11 de PHVA | 3 en nivel 2 y 2 en transición | Sí |
| Cierre | Publicar la versión de la iteración | Release con notas | Tabla 12 de mantenimiento; Tabla 17, punto 8 | Versión `QAInventaryMYPES 1.0.0` preparada en el CHANGELOG (nombre según el proyecto, decisión del autor). La etiqueta y la release en GitHub se crean al fusionar | **Parcial** |
| Cierre | Auditoría externa | Paquete entregado al docente | Tabla 14 de auditoría | Por ejecutar (AC-12) | **Pendiente** |
| Cierre | Decisión de aceptación con firma SQA | Registrada y firmada | Tabla 12 de PHVA; Tabla 9 de pruebas; Tabla 19 de revisiones | Ver la sección 2 | **Pendiente de firma** |

## 2. Decisión de aceptación del producto

El informe fija dos criterios para aceptar el producto:

| Criterio | Fuente | Resultado de la iteración 1 | ¿Se cumple? |
|---|---|---|---|
| Todos los casos críticos aprobados; ningún defecto crítico o alto abierto | Tabla 9 de pruebas | Los casos críticos (no omitibles) pasan. 0 defectos críticos o altos confirmados abiertos, pero hay 4 hallazgos altos sin confirmar | **Con salvedad** |
| Cierre de hallazgos mayores del 100 % antes de aceptar | Tabla 19 de revisiones | 21,62 % | **No** |

**Consecuencia según el informe:** el producto no cumple el criterio de aceptación de la Tabla 19. El informe no contempla una aceptación parcial, así que las opciones honestas son dos:

| Opción | Significado | Cuándo corresponde |
|---|---|---|
| **No aceptado para liberación** | La iteración 1 se cierra con brechas registradas; el producto no se libera hasta cumplir los criterios | Es la lectura literal de la Tabla 19 |
| Aceptado con riesgo registrado | Se libera asumiendo las brechas, con un plan para cada una | Exige que el autor justifique por qué la Tabla 19 no se aplica tal cual. Debe declararse como desviación |

**Recomendación:** *no aceptado para liberación.* Las acciones AC-03 y AC-05 de la iteración 2 son justamente las que permiten cumplir los dos criterios.

**Firma SQA:** César Roque · decisión: __________ · fecha: ____

## 3. Criterio de cierre de la iteración 1

| Condición | Estado |
|---|---|
| Las 4 fases tienen su criterio de salida evaluado | Fases 1, 2 y 3 evaluadas; la Fase 4 se evalúa en este archivo |
| Todas las brechas tienen una acción | Sí (AC-01 a AC-12) |
| Estandarización incorporada | Hecha en local: reglas 18 a 25 de `CLAUDE.md` y 5 casillas nuevas en la plantilla de PR. Falta el commit |
| Versión publicada y auditoría externa entregada | Versión preparada en el CHANGELOG; falta la release al fusionar. Auditoría externa pendiente de entrega (AC-12) |
| Decisión de aceptación firmada | Pendiente |
| Licencia de terceros (B-16) | Decisión del autor (14/11/2026): no se agrega la licencia. La observación de la auditoría de cumplimiento (9.4) queda registrada |
| Etiqueta de cierre en `main` (por ejemplo `iteracion-1-cerrada`) | Pendiente; la crea el autor al completar lo anterior |

## 4. ¿Se cumple el ciclo PHVA de la iteración 1?

**Sí como ciclo, no como producto.**

Cada fase hizo lo que el informe le asigna:
- **Planificar** fijó las metas y el plan.
- **Hacer** produjo los resultados.
- **Verificar** los midió contra las metas, sin redondear.
- **Actuar** convirtió cada brecha en una acción con su métrica.

Eso es lo que la Tabla 23 de PHVA describe como una iteración completa.

El producto, en cambio, todavía no alcanza los criterios de aceptación de la Tabla 9 de pruebas y de la Tabla 19 de revisiones. Esa es la entrada de la iteración 2.
