# Fase 3 · Paso 8: medir

El paso 8 calcula todas las métricas que define el informe y las compara con su umbral. Se agrupan por la tabla que las define. Ningún valor se redondea.

| Subpaso | Fecha de diseño | Fecha de desarrollo | Evidencia |
|---|---|---|---|
| 8.1 Objetivos de calidad (Tabla 13 de PHVA) | — | Por registrar | Este archivo |
| 8.2 Métricas en tres niveles (Tabla 19 de PHVA) | — | Por registrar | Este archivo |
| 8.3 Indicadores de revisión (Tabla 19 de revisiones) | — | Por registrar | `revisiones/README.md` |
| 8.4 Métricas de mantenimiento (Tabla 13 de mantenimiento) | — | Por registrar | Issues de GitHub |
| 8.5 DPMO por subsistema (Tablas 9 y 10 de Top-Down) | — | Por registrar | `registro-ejecucion.md` |
| 8.6 Recuperación (Tabla 9 de recuperación) | — | Por registrar | Evidencias 4.2 y 4.3 |
| 8.7 Niveles, técnicas y controles (Tablas 16 y 17 de PHVA; Tabla 13 de seguridad) | — | Por registrar | Este archivo |

## 8.1 Objetivos de calidad (Tabla 13 de PHVA)

| Objetivo | Métrica | Umbral | Valor | Marca | ¿Cumple? |
|---|---|---|---|---|---|
| Requisitos cubiertos por pruebas | Requisitos con al menos un caso | 100 % de los prioritarios | 6 / 6 = 100 % | Medido | Sí |
| Lógica crítica probada | Cobertura unitaria de Orders y Products | ≥ 60 % | Orders 49,06 % (130/265); Products 38,07 % (67/176) | Medido | **No** |
| Sin defectos graves abiertos | Defectos críticos abiertos al cerrar | 0 | 0 | Medido | Sí, entre los defectos confirmados por prueba (ver nota 1) |
| Los cambios se revisan | Cambios integrados con revisión por pares | 100 % | 0 % con revisión de un par: todos los PR los revisó el autor, y 17 de 27 tienen la firma SQA marcada | Medido | **No** (ver nota 2) |
| Los casos se ejecutan | Casos ejecutados frente a diseñados | ≥ 90 % | 20 / 20 = 100 % | Medido | Sí |

**Nota 1.** Siguen abiertos cuatro hallazgos de revisión de severidad alta (#6, #16, #17 y #18) que ninguna prueba confirmó ni descartó. Por eso «0 críticos» describe lo confirmado, no la ausencia de riesgo. En particular, #17 (importes aceptados del navegador sin recalcular) afecta al dinero, y la Tabla 17 de revisiones lo trataría como mayor.

**Nota 2.** La métrica del informe es «revisión **por pares**». Ningún PR tuvo un revisor distinto del autor, así que no se alcanza. Además, de los 48 commits de `main` desde `linea-base`, **6 entraron sin PR** (confirmado en GitHub el 14/11/2026), todos el 13/11/2026 y antes de la protección de la rama:
- `84d55c4`: carga inicial de los documentos de calidad;
- `bef9e17`: eliminación de los archivos heredados;
- `0ef5d0d`: subpaso 1.1;
- `820dc94`: extracto de mantenimiento;
- `3255d55`: subpaso 1.2;
- `8c9bb46`: subpaso 1.3.

Los otros 42 entraron por PR (87,5 %).

Como medida sustituta y complementaria se reporta (confirmado con `gh pr list --state merged`):

| Medida | Valor |
|---|---|
| PR fusionados | 27 (del #2 al #53; #1 y #33 se cerraron sin fusionar) |
| Con la integración continua en verde | 27 / 27 = 100 % |
| Con la casilla de firma SQA marcada | 17 / 27 = 62,96 %. Sin marcar: #2, #3, #9, #10, #11, #12, #13, #24, #31 y #32 |
| Con revisión de otra persona en GitHub | 0 / 27: un solo autor en todos los PR |
| Con la lista L1 aplicada | No verificable desde GitHub: la casilla L1 de la plantilla no se marcó de forma sistemática |

## 8.2 Métricas en tres niveles (Tabla 19 de PHVA)

| Nivel | Métrica | Umbral | Valor | Marca | ¿Cumple? |
|---|---|---|---|---|---|
| Proyecto | Tareas terminadas frente a planificadas | ≥ 80 % | Por pasos: 5 de 6 planificados hasta Hacer (1 a 6; el 7 es condicional) = 83,33 %. Por subpasos: 26 de 29 (1.0 a 6.3) = 89,66 % | Medido | Sí |
| Proyecto | Casos de prueba ejecutados | ≥ 90 % | 100 % | Medido | Sí |
| Proceso | Cambios integrados con revisión | 100 % | Ver 8.1, nota 2 | Medido | **No** |
| Proceso | Tiempo para corregir un defecto crítico | ≤ 1 iteración | #34: registrado y corregido el 14/11/2026, en la misma iteración (PR #46) | Medido | Sí |
| Producto | Defectos críticos abiertos | 0 | 0 (ver 8.1, nota 1) | Medido | Sí |
| Producto | Cobertura de pruebas unitarias | ≥ 60 % | 49,06 % y 38,07 % | Medido | **No** |
| Producto | Requisitos prioritarios con prueba | 100 % | 100 % | Medido | Sí |

## 8.3 Indicadores del proceso de revisión (Tabla 19 de revisiones)

| Indicador | Fórmula | Meta | Valor | Marca | ¿Cumple? |
|---|---|---|---|---|---|
| Cobertura de revisión | RV de riesgo alto hechas / planificadas | 100 % antes de probar | 3 / 4 = 75 % (falta RV-05) | Medido | **No** |
| Cierre de hallazgos mayores | Cerrados y verificados / registrados | 100 % antes de aceptar | 8 / 37 = 21,62 % | Medido | **No** |
| Eficacia de eliminación de defectos | E / (E + D) | ≥ 85 % | No calculable: D son los defectos detectados después de liberar, y el sistema no se ha liberado | — | No aplica todavía |
| Esfuerzo por revisión | Ep + Ea | ≤ 10 h | RV-01 2,15 h; RV-03 1,4 h; RV-04 0,7 h; RV-10 1,4 h | Medido | Sí |
| Densidad de error | Hallazgos / tamaño | Línea base | Matriz: 233,3 por mil celdas. Código: Orders 8,6; Users 12,0; scripts 23,9 por mil líneas | Medido | Sí (línea base registrada) |

**Indicador complementario: defectos de prueba anticipados por revisión.** De los 10 defectos encontrados en el paso 5, 8 ya figuraban como hallazgos de revisión antes de ejecutar las pruebas (80 %):

| Defecto | ¿Lo anticipó una revisión? |
|---|---|
| #34 escalamiento | Sí: A-01 (Top-Down), H-RV01-02 y H-RV04-01 |
| #35 y #36 stock y cantidad | Sí: H-RV03-02 y H-RV03-05 |
| #40 CSRF | Sí: H-RV04-02 y H-RV04-04 |
| #41 XSS | Sí: H-RV03-04 |
| #42 sesión | Sí: H-RV04-03 |
| #43 avisos de PHP | Sí: H-RV03-09 |
| #44 contraseña visible | Sí: H-RV04-07 |
| #37 reporte | **No:** Reports no se revisó |
| #38 marca y categoría | **No:** Products no se revisó (RV-05) |

No sustituye a la eficacia de eliminación de defectos, pero muestra con datos propios lo que el informe afirma: un producto revisado llega a las pruebas con menos defectos ocultos.

## 8.4 Métricas de mantenimiento (Tabla 13 de mantenimiento)

El informe no fija metas numéricas hasta tener línea base. Estos valores son esa línea base.

| Métrica | Valor | Marca |
|---|---|---|
| Defectos reportados por mes | Noviembre de 2026: 16 issues con la etiqueta `tipo:correctivo`. Son 10 defectos de prueba (#34 a #38 y #40 a #44) y 6 hallazgos de revisión (#6 y #16 a #20) | Medido (GitHub) |
| Tiempo promedio de resolución por severidad | Crítica: 0,86 h (#34, 51 min 29 s). Alta: 0,67 h de promedio (40 min 14 s; de 0,28 h en #41 a 0,94 h en #37; 5 issues). Media y baja: sin cerrar | Medido (GitHub) |
| Distribución por categoría | 23 issues y 24 etiquetas, porque #6 tiene dos. Correctivo: 16 (66,67 %). Preventivo: 5 (20,83 %: #4, #5, #6, #8 y #14). Perfectivo: 2 (8,33 %: #7 y #15). Adaptativo: 1 (4,17 %: #52) | Medido (GitHub) |
| Regresiones detectadas | 0: ningún caso que pasaba en la ronda 1 falló en la ronda 2 | Medido |

## 8.5 DPMO por subsistema (Tablas 9 y 10 de Top-Down)

Fórmula del informe: DPMO = defectos ÷ (unidades × oportunidades por unidad) × 1 000 000.

**Criterio de medición propuesto (lo valida el autor):** unidad = un caso ejecutado del subsistema; defecto = un caso que falla. Un caso fallido cuenta como un solo defecto, aunque pueda fallar en más de una oportunidad, así que el valor es conservador hacia abajo.

| Subsistema | Oportunidades por unidad (Tabla 10) | Casos (unidades) | Oportunidades totales | Defectos ronda 1 | DPMO ronda 1 | Defectos ronda 2 | DPMO ronda 2 |
|---|---|---|---|---|---|---|---|
| S1 Seguridad y acceso | 3 | CP-04, 05, 06, 07, 10, 13, 14, 15, 16, 18 (10) | 30 | 2 (CP-15, CP-18) | 66 666,7 | 0 | 0 |
| S2 Datos maestros | 3 | Ninguno | — | — | No medible | — | No medible |
| S3 Inventario | 4 | CP-08, 11, 17, 20 (4) | 16 | 2 (CP-08, CP-17) | 125 000 | 1 (CP-08) | 62 500 |
| S4 Ventas | 5 | CP-01, 02, 03, 19 (4) | 20 | 2 (CP-02, CP-03) | 100 000 | 0 | 0 |
| S5 Información | 2 | CP-09, 12 (2) | 4 | 2 (CP-09, CP-12) | 500 000 | 0 | 0 |
| S6 Empresa | 2 | Ninguno | — | — | No medible | — | No medible |
| **Total** | | 20 | 70 | 8 | **114 285,7** | 1 | **14 285,7** |

**Lectura.**
- El DPMO total bajó de 114 285,7 a 14 285,7 entre las rondas: la mejora se debe a las 6 correcciones.
- **S2 (datos maestros) y S6 (empresa) no tienen ningún caso**, así que su calidad no se puede medir. Es una brecha de cobertura del Top-Down.
- Con 20 unidades, el valor no tiene significancia estadística: es una línea base. El informe ya califica la meta de 3,4 DPMO como aspiracional.

**Estado del ciclo DMAIC (Tabla 9 de Top-Down):**

| Fase | Estado en el informe | Estado tras la iteración |
|---|---|---|
| Definir | Realizado | Realizado |
| Medir | Por medir | **Realizado:** DPMO de las rondas 1 y 2 |
| Analizar | Parcial, sin código | **Realizado:** H-01 a H-06 confirmados con `archivo:línea`; causa raíz de cada defecto corregido |
| Mejorar | Propuesto | **Parcial:** ventas atómicas en la creación (H-04, PR #47). H-01 (permisos centralizados) sin aplicar (#4) |
| Controlar | Propuesto | **Realizado:** la suite corre en cada cambio y la etiqueta `pruebas-base-3.1.6` fija la referencia de regresión |

## 8.6 Recuperación (Tabla 9 de recuperación)

| Función | Meta RTO | Meta RPO | RTO medido | RPO medido | ¿Cumple? |
|---|---|---|---|---|---|
| Base de datos y control de acceso | 4 h | 1 h | 0,06 h | < 0,14 h (cota) | RTO sí. RPO solo en el simulacro (ver nota) |
| Registro de ventas | 4 h | 1 h | 0,06 h | < 0,14 h (cota) | Igual |
| Control de existencias | 8 h | 1 h | 0,06 h | < 0,14 h (cota) | Igual |
| Restauración con el mismo número de registros (Tabla 20) | — | — | 12 / 12 tablas | — | Sí |

**Nota.** El RPO medido corresponde a un respaldo hecho a mano minutos antes del simulacro. La política de la Tabla 11 exige copias cada hora para que el RPO de 1 h se cumpla siempre. Con respaldos manuales, el RPO real no está acotado. **RPO de la política: no cumple.**

## 8.7 Niveles, técnicas y controles de seguridad

### Niveles de prueba (Tabla 16 de PHVA)

| Nivel | Definición del informe | Lo ejecutado | ¿Cumple? |
|---|---|---|---|
| Unitaria | Totales, descuento de stock, validaciones | CP-01, 02, 03, 08, 19, 20 en PHPUnit | Sí |
| Integración | Controlador, modelo y base en una venta; permisos con grupos | CP-09 y los casos de permisos con PC-0 | Sí |
| Sistema | Alta de producto, venta, reporte | CP-04 a CP-07, 10, 11, 13 a 18 | Sí |
| Aceptación | Un usuario de prueba, con datos de prueba, completa tareas típicas | CP-12: comparación del reporte con una hoja de cálculo, hecha por el autor y Claude | **Parcial:** valida los datos, pero ningún usuario de prueba completó tareas |

**Resultado: 3 de 4 completos y 1 parcial.**

### Técnicas de prueba (Tabla 17 de PHVA)

| Técnica | Uso previsto en el informe | Lo ejecutado | ¿Cumple? |
|---|---|---|---|
| Caja blanca | Pruebas unitarias de ventas y stock (PHPUnit) | PHPUnit con cobertura medida; los casos se diseñaron desde los requisitos | Parcial |
| Caja negra | Flujos de venta y reportes | Casos de sistema y aceptación | Sí |
| Estática | Revisar complejidad y seguridad antes de ejecutar (análisis estático) | Revisiones manuales con las listas L1, L2 y L5; sin herramienta | Parcial |
| Dinámica | Tiempos y recursos en staging | No ejecutada | **No** |
| Exploratoria | Sesiones cortas con notas | No ejecutada | **No** |

**Resultado: 1 completa, 2 parciales y 2 no aplicadas, de 5.**

### Controles de seguridad lógica (Tabla 13 de seguridad)

| Control | Estado en el informe | Estado verificado |
|---|---|---|
| Control de acceso por roles | Verificar | **Cumple** en los casos ejecutados. Riesgo: 20 métodos públicos sin comprobación de permiso (#4) |
| Contraseñas | Por verificar | **Parcial:** el hash adaptativo cumple (CP-14); la política de contraseña no está definida |
| Autenticación multifactor | Propuesto | No aplica (funcionalidad nueva) |
| HTTPS | Propuesto | **No cumple:** staging sin HTTPS; la cookie no lleva `Secure` |
| Sesiones | Por verificar | **Parcial:** HttpOnly sí; el id no cambia al entrar (#42) |
| Validación de entradas y CSRF | Por verificar | **Parcial:** CSRF y XSS de ventas y productos corregidos; XSS de usuarios y grupos abierto (#18); importes del navegador (#17) |
| Firewall | Propuesto | **Cumple** en staging: la base no publica puertos |
| Acceso remoto (VPN) | Propuesto | No aplica |
| Registro de eventos | Propuesto | No aplica (funcionalidad nueva) |
| Secretos en el repositorio | Por verificar | **Cumple** |
| Contenedores de staging | Por verificar | **Cumple** |

## Resumen del paso 8

| Grupo | Cumple | Parcial | No cumple | No calculable o no aplica |
|---|---|---|---|---|
| Tabla 13 de PHVA (5) | 3 | 0 | 2 | 0 |
| Tabla 19 de PHVA (7) | 5 | 0 | 2 | 0 |
| Tabla 19 de revisiones (5) | 2 | 0 | 2 | 1 |
| Recuperación (RTO, RPO, restauración) | 2 | 0 | 1 (RPO de la política) | 0 |
| Niveles (4) | 3 | 1 | 0 | 0 |
| Técnicas (5) | 1 | 2 | 2 | 0 |
| Controles de seguridad verificables (8) | 4 | 3 | 1 | — (3 no aplican) |
