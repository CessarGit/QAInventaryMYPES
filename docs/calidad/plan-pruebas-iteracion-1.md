# Plan de pruebas de la iteración 1

Plan de pruebas (micro) de la iteración de calidad 1. Sigue la estructura de la Tabla 9 de pruebas (`fuente-informe.md`) y la columna "Plan de pruebas (micro, por iteración)" de la Tabla 15 de PHVA (`fuente-informe-2.md`); la regla ante retrasos sale de la Tabla 10 de pruebas. Subpaso 2.3.

## 1. Alcance

| Aspecto | Contenido |
| :---- | :---- |
| Casos | CP-01 a CP-14, definidos en [trazabilidad.md](trazabilidad.md) |
| Requisitos cubiertos | RQ-01 a RQ-06 |
| Prioridad | Orders, Products y control de acceso (Tabla 9: "priorización por riesgo: ventas, stock y control de acceso primero") |
| Módulos de la iteración | Orders, Products, Users, Groups, Settings y Reports (Tabla 15, micro: "los módulos tocados en la iteración") |
| Fuera de alcance | Estrés y carga masiva (Tabla 7 de pruebas: "No se aplica"); usabilidad con usuarios de cada rol (requiere participantes externos) |

## 2. Estrategia

**Estrategia (Tabla 9):** pruebas por niveles, de lo unitario a la aceptación; priorización por riesgo (ventas, stock y control de acceso primero).

**Enfoque (Tabla 15, micro):** casos concretos de la matriz de trazabilidad.

| Caso | Requisito | Nivel (Tabla 6) | Técnica (Tabla 7) | Estado del nivel |
| :---- | :---- | :---- | :---- | :---- |
| CP-01 | RQ-02 | Unitaria | Valores límite | Pendiente |
| CP-02 | RQ-02 | Unitaria | Valores límite | Pendiente |
| CP-03 | RQ-02 | Unitaria | Valores límite | Pendiente |
| CP-04 | RQ-01 | Sistema | Caja negra | Pendiente |
| CP-05 | RQ-01 | Sistema | Caja negra | Pendiente |
| CP-06 | RQ-01 | Sistema | Pruebas de seguridad | Pendiente |
| CP-07 | RQ-01 | Sistema | Pruebas de seguridad | Pendiente |
| CP-08 | RQ-04 | Unitaria | Caja negra | Pendiente |
| CP-09 | RQ-03 | Integración | Caja negra (Orders con Reports) | Pendiente |
| CP-10 | RQ-01 | Sistema | Pruebas de seguridad | Pendiente |
| CP-11 | RQ-04 | Sistema | Caja negra (flujo completo) | Pendiente |
| CP-12 | RQ-03 | Aceptación | Caja negra (comparación con registro manual) | Pendiente |
| CP-13 | RQ-05 | Sistema | Caja negra | Pendiente |
| CP-14 | RQ-06 | Sistema | Pruebas de seguridad (inspección de la base) | Pendiente |

*Nota.* La Tabla 6 de pruebas indica "Ejecutado" en la columna Estado, pero ningún nivel se ha ejecutado; todos se tratan como Pendiente.

## 3. Recursos

| Recurso | Uso en la iteración |
| :---- | :---- |
| Staging Docker del paso 1 | PHP 7.4.33, Apache 2.4 y MySQL 5.7.44 ([despliegue.md](../arquitectura/despliegue.md)); pruebas de sistema y aceptación |
| PHPUnit con ci-phpunit-test | Pruebas unitarias y de integración. Versión compatible con PHP 7.4 y CodeIgniter 3.1.6: por confirmar en el paso 5 (PHPUnit 11 exige PHP 8.2) |
| pcov | Medición de cobertura de Orders y Products |
| Base `stock_test` | Base de pruebas con los datos de la precondición PC-0 (grupos de los cinco roles y un usuario por rol); ya definida en la integración continua |
| GitHub | Repositorio, issues (plantilla Defecto) y pull requests |
| Integración continua (`calidad.yml`) | Semáforo de la Tabla 18; jobs `sintaxis` y `pruebas` obligatorios en `main` |

Herramientas que se usan en la iteración (Tabla 15, micro): PHPUnit, ci-phpunit-test, pcov, casos manuales en staging. El análisis estático (opcional según la Tabla 15, macro) no se usa en esta iteración.

## 4. Cronograma

Unitarias e integración durante el desarrollo de cada cambio; sistema y aceptación antes de cada liberación (Tabla 9). Fechas a completar por el autor.

# Cronograma de Pruebas - Octubre 2026

| Actividad | Casos | Fecha de inicio | Fecha de fin |
| :--- | :--- | :--- | :--- |
| **Preparar la base stock_test y los datos de PC-0** | — | 15/10/2026 | 15/10/2026 |
| **Pruebas unitarias** | CP-01, CP-02, CP-03, CP-08 | 16/10/2026 | 19/10/2026 |
| **Pruebas de integración** | CP-09 | 20/10/2026 | 20/10/2026 |
| **Pruebas de sistema** | CP-04, CP-05, CP-06, CP-07, CP-10, CP-11, CP-13, CP-14 | 21/10/2026 | 23/10/2026 |
| **Pruebas de aceptación** | CP-12 | 26/10/2026 | 26/10/2026 |
| **Registro de resultados y defectos** | CP-01 a CP-14 | 16/10/2026 | 27/10/2026 |

## 5. Criterios de éxito y de fallo

**Criterios de la Tabla 9:**

| Criterio | Contenido |
| :---- | :---- |
| Éxito | Todos los casos críticos aprobados; ningún defecto crítico o alto abierto; los defectos menores quedan registrados con un plan |
| Fallo | Cualquier caso crítico fallido o defecto crítico abierto detiene la liberación |

**Criterio de salida (Tabla 15, micro):** casos ejecutados y resultados registrados.

**Umbrales (Tabla 13 de PHVA):**

| Objetivo | Métrica | Umbral propuesto |
| :---- | :---- | :---- |
| Los requisitos están cubiertos por pruebas | Requisitos con al menos un caso de prueba | 100 % de los requisitos prioritarios |
| La lógica crítica está probada | Cobertura de pruebas unitarias en los módulos Orders y Products | Al menos 60 % al cierre del proyecto |
| Sin defectos graves abiertos | Defectos críticos abiertos al cerrar una iteración | 0 |
| Los cambios se revisan | Cambios integrados con revisión por pares | 100 % |
| Los casos de prueba se ejecutan | Casos ejecutados frente a diseñados | Al menos 90 % |

Casos críticos de esta iteración: los no omitibles de la sección 8.

## 6. Roles

| Rol | Responsable |
| :---- | :---- |
| Pruebas unitarias e integración | César Roque |
| Pruebas de sistema y coordinación | César Roque |
| Usabilidad y aceptación | César Roque |
| Revisión y firma SQA | César Roque |

## 7. Registro

| Qué | Dónde |
| :---- | :---- |
| Estado y resultado de cada caso | [trazabilidad.md](trazabilidad.md) (estado Pendiente, Iniciado o Rechazado) |
| Defectos | Issue con la plantilla **Defecto**, vinculada al caso (CP-xx) y a la severidad (`sev:*`) |
| Evidencias | `docs/ejecucion/evidencias/` |
| Ejecución de cada subpaso | [bitacora.md](../ejecucion/bitacora.md) |

## 8. Regla de decisión ante retrasos (Tabla 10 de pruebas)

| Situación | Respuesta propuesta | Lo que no se hace |
| :---- | :---- | :---- |
| La liberación se atrasa y no hay tiempo para todas las pruebas | Reordenar por riesgo: ejecutar primero ventas, stock y control de acceso; paralelizar las pruebas entre los tres integrantes | Omitir pruebas de Orders, de stock o de permisos |
| Un integrante no está disponible para una prueba de aceptación | Reprogramar la prueba o designar a su suplente | Dar por hecho que funciona |
| El retraso es de varios días | Ampliar el cronograma con justificación escrita y comunicarla | Liberar sin los casos críticos aprobados |
| Quedan módulos de bajo riesgo sin probar | Registrar la excepción y probarlos en la siguiente iteración | Dejar la excepción sin registro |

**Aplicación a los casos de la iteración:**

| Clase | Casos | Regla |
| :---- | :---- | :---- |
| No omitibles | CP-01, CP-02, CP-03, CP-04, CP-05, CP-06, CP-07, CP-09, CP-10, CP-11, CP-13, CP-14 | Tocan Orders, stock o permisos (Tabla 10); CP-14 toca credenciales (Tabla 17, punto 2) |
| Omitibles con excepción registrada | CP-08, CP-12 | Si se posponen, se registra la excepción y se ejecutan en la siguiente iteración |

## 9. Desviaciones respecto al informe

1. **Una persona en lugar de tres.** La Tabla 9 reparte los roles entre Oscar Nájera, César Roque y Miguel Ángel; la Tabla 15 (micro) indica que otro integrante revisa; la Tabla 6 asigna responsables por nivel. En esta iteración César Roque ejecuta y revisa todo. Compensación: autorrevisión con las listas L1 y L4, semáforo de integración continua y firma SQA en cada pull request.
2. **Regla ante retrasos con un solo integrante.** "Paralelizar las pruebas entre los tres integrantes" y "designar a su suplente" (Tabla 10) no son aplicables; las respuestas posibles son reordenar por riesgo, reprogramar o ampliar el cronograma con justificación escrita.
3. **Usabilidad.** La Tabla 7 propone observar a una persona de cada rol; sin participantes externos, queda fuera de esta iteración.
4. **Apéndice A.** La Tabla 9 cita la plantilla del Apéndice A, que no está en la fuente; se usan la plantilla de issue Defecto y las fichas de `trazabilidad.md`.
5. **CP-14 no omitible.** La Tabla 10 protege Orders, stock y permisos; CP-14 se añade a los no omitibles porque verifica el almacenamiento de credenciales.
6. **Número de casos.** La Tabla 21 (mejora SMART 1) pide al menos 20 casos antes del cierre del proyecto; esta iteración tiene 14.
