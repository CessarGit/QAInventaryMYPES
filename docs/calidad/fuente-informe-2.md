# Fuente de diseño (parte 2): tablas para los pasos 2.3 a 10

Extracto literal del informe, complementario a `fuente-informe.md`. Mismas reglas: se ejecuta, no se reinterpreta.

## Estrategia y plan de pruebas (Tabla 15 de PHVA) · paso 2.3
**Tabla 15**  
*Estrategia y plan de pruebas del proyecto*

| Aspecto | Estrategia de pruebas (macro) | Plan de pruebas (micro, por iteración) |
| :---- | :---- | :---- |
| Alcance | Todo el sistema y los 12 módulos | Los módulos tocados en la iteración |
| Enfoque | Pruebas tempranas en lógica crítica; automatizar lo repetible; probar a mano la interfaz | Casos concretos de la matriz de trazabilidad |
| Herramientas | PHPUnit para unitarias; casos manuales en staging; análisis estático como opción | Cuáles se usan en la iteración |
| Criterio de salida | Sin defectos críticos y umbrales cumplidos | Casos ejecutados y resultados registrados |
| Responsables | César Roque (aprueba) | Quien desarrolla el módulo ejecuta; otro integrante revisa |

*Nota.* Elaboración a partir del material de la plataforma virtual.

## Regla de decisión ante retrasos (Tabla 10 de pruebas) · paso 2.3
**Tabla 10**  
*Respuesta del equipo ante un posible retraso del cronograma*

| Situación | Respuesta propuesta | Lo que no se hace |
| :---- | :---- | :---- |
| La liberación se atrasa y no hay tiempo para todas las pruebas | Reordenar por riesgo: ejecutar primero ventas, stock y control de acceso; paralelizar las pruebas entre los tres integrantes | Omitir pruebas de Orders, de stock o de permisos |
| Un integrante no está disponible para una prueba de aceptación | Reprogramar la prueba o designar a su suplente | Dar por hecho que funciona |
| El retraso es de varios días | Ampliar el cronograma con justificación escrita y comunicarla | Liberar sin los casos críticos aprobados |
| Quedan módulos de bajo riesgo sin probar | Registrar la excepción y probarlos en la siguiente iteración | Dejar la excepción sin registro |

*Nota.* Elaboración a partir del costo del defecto tardío y de los riesgos de omitir pruebas descritos en Prueba, mantenimiento y auditoría del material de la plataforma.

## Niveles de prueba (Tabla 6 de pruebas) · pasos 2.3 y 5
> Advertencia: la columna Estado dice "Ejecutado", pero el resto del informe indica que nada se ha ejecutado. Para la iteración, tratar todos los niveles como **Pendiente**.
**Tabla 6**  
*Niveles de prueba aplicados al sistema*

| Nivel | Alcance en el sistema | Responsable propuesto | Herramienta | Estado |
| :---- | :---- | :---- | :---- | :---- |
| Unitarias | Lógica de modelos: venta, descuento de stock, validaciones, cálculo del reporte | Oscar Nájera | PHPUnit | Ejecutado |
| De integración | Orders con Products; Reports con Orders; control de permisos con cada módulo | Oscar Nájera y César Roque | PHPUnit y staging | Ejecutado |
| De sistema | Flujos completos por rol en staging, incluida usabilidad | César Roque | Manual en staging | Ejecutado |
| De aceptación | Escenarios de uso de cada rol con datos de prueba; comparación con un registro manual | Miguel Ángel y representante de MYPE (si existe) | Staging con datos semilla | Ejecutado |

*Nota.* Elaboración a partir de los niveles descritos en Prueba, mantenimiento y auditoría y de las responsabilidades del equipo.

## Técnicas de prueba (Tabla 7 de pruebas) · pasos 2.3 y 5
**Tabla 7**  
*Técnicas de prueba y su aplicación al sistema*

| Técnica | Aplicación | Módulos prioritarios |
| :---- | :---- | :---- |
| Caja negra | Casos derivados de la matriz de roles y de la función de cada módulo, solo con entradas y salidas | Todos |
| Caja blanca | Cobertura de las ramas de decisión de la lógica de venta y de descuento de stock | Orders, Products |
| Valores límite | Cantidad igual a 0, 1, al stock disponible y mayor que el stock; campos vacíos o muy largos | Orders, Products |
| Pruebas de seguridad | Acceso denegado a módulos no permitidos; cambio de contraseña solo de la cuenta propia | Users, Groups, Settings |
| Usabilidad | Observar a una persona de cada rol completando sus tareas típicas | Orders, Products |
| Estrés | No se aplica: el proyecto no realiza pruebas de carga masiva | — |

*Nota.* Elaboración a partir de las técnicas de Prueba, mantenimiento y auditoría (s. f.).

## Un ciclo PHVA dentro de una iteración (Tabla 23 de PHVA) · pasos 8 a 10
**Tabla 23**  
*Un ciclo PHVA dentro de una iteración*

| Fase | Qué se hace | Artefacto |
| :---- | :---- | :---- |
| Planificar | Elegir historias, fijar criterios de aceptación y casos de la matriz | Plan de pruebas de la iteración |
| Hacer | Desarrollar, escribir pruebas unitarias y revisar por pares | Incremento y pruebas |
| Verificar | Ejecutar casos y actualizar métricas | Informe de resultados |
| Actuar | Retrospectiva corta y acciones para la próxima iteración | Registro de acciones |

*Nota.* Matriz propuesta para cada iteración en cada ciclo.

## Regla de selección de la técnica de revisión (Tabla 6 de revisiones) · paso 3
La formalidad se elige según el riesgo. Para el proyecto se propone estimarlo con cuatro preguntas: ¿el producto toca dinero, existencias o credenciales?; ¿su módulo tiene criticidad alta en la Tabla 2?; ¿un error se amplificaría porque otros productos se construyen sobre él, como ocurre con el diseño?; y ¿es difícil o costoso corregirlo tarde? Con ellas se aplica la regla de la Tabla 6.

**Tabla 6**  
*Regla para elegir la técnica de revisión según el riesgo*

| Riesgo | Cuándo aplica | Técnica | Características presentes |
| :---- | :---- | :---- | :---- |
| Alto | Responde Sí a dos o más de las cuatro preguntas | Inspección | Las cuatro |
| Medio | Responde Sí a una pregunta | Walkthrough | Estructura de la reunión; las demás de forma parcial |
| Bajo | Responde No a todas | Verificación de escritorio o revisión en pareja, con lista de revisión | Ninguna formal; se compensa con la lista |

*Nota.* Elaboración propia. Las preguntas y los umbrales son una propuesta que se recalibra después de las primeras revisiones (Pressman, 2010).

## Plan de revisiones (Tabla 7 de revisiones) · paso 3
**Tabla 7**  
*Plan de revisiones del proyecto*

| ID | Producto revisado | Riesgo | Técnica | Productor | Etapa |
| :---- | :---- | :---- | :---- | :---- | :---- |
| RV-01 | Requisitos y matriz de roles y permisos | Alto | Inspección | Oscar Nájera | Requerimientos |
| RV-02 | Modelo de datos (12 tablas), arquitectura y flujo principal | Medio | Walkthrough | Oscar Nájera | Diseño |
| RV-03 | Código del módulo Orders (venta y descuento de stock) | Alto | Inspección | Oscar Nájera | Código |
| RV-04 | Código de Users, Groups y permisos | Alto | Inspección | Oscar Nájera | Código |
| RV-05 | Código del módulo Products | Alto | Inspección | Oscar Nájera | Código |
| RV-06 | Código de los módulos de criticidad media y baja | Bajo | Revisión en pareja y por solicitud de cambio | Oscar Nájera y Miguel Ángel, cruzados | Código |
| RV-07 | Vistas y formularios (front-end) | Bajo | Revisión en pareja | Miguel Ángel | Código |
| RV-08 | Casos y datos de prueba | Medio | Walkthrough | César Roque | Prueba |
| RV-09 | Manual y material de usuario | Bajo | Revisión en pareja y prueba piloto | Miguel Ángel | Despliegue |
| RV-10 | Scripts de despliegue y de respaldo | Medio | Walkthrough | César Roque | Despliegue |

*Nota.* Los productores son los responsables propuestos según las responsabilidades del equipo.

## L5. Lista de revisión de scripts de despliegue y documentación (Tabla 15 de revisiones) · pasos 4 y 10

| N.º | Pregunta de revisión |
| :---- | :---- |
| 1 | ¿Cada paso del script o del manual tiene un resultado esperado y verificable? |
| 2 | ¿El script de respaldo indica qué se respalda, cada cuánto y dónde se guarda la copia? |
| 3 | ¿Se probó la restauración del respaldo y el procedimiento para volver atrás? |
| 4 | ¿Las credenciales y claves están fuera del código y del manual? |
| 5 | ¿El manual cubre todos los roles y todas las operaciones de su matriz de permisos? |
| 6 | ¿El lenguaje y las capturas corresponden a la versión vigente del sistema? |
| 7 | ¿Se identifican los riesgos del despliegue y su tratamiento? |
| 8 | ¿Un usuario de la MYPE pudo seguir los pasos sin ayuda del equipo? |

*Nota.* Elaboración a partir de los scripts y doc. del sistema

## Plantillas de registro de la revisión (Tablas 16, 17 y 18 de revisiones) · paso 3
**Tabla 16**  
*Plantilla de lista de problemas*

| ID | Producto y ubicación | Descripción | Tipo | Gravedad | Responsable | Estado |
| :---- | :---- | :---- | :---- | :---- | :---- | :---- |
| H-01 | RV-01, matriz de roles | Descripción objetiva del hallazgo | Omisión, ambigüedad, inconsistencia, defecto de diseño o forma | Mayor o menor | Productor | Abierto, corregido o verificado |

*Nota.* El estado pasa a verificado cuando el líder comprueba la corrección (etapa 6).

**Tabla 17**  
*Clasificación de los hallazgos y tratamiento*

| Gravedad | Criterio | Tratamiento |
| :---- | :---- | :---- |
| Mayor | La corrección exige más esfuerzo que el umbral (propuesta: una hora-persona), o el hallazgo afecta permisos, dinero, existencias o datos, aunque sea fácil de corregir | Se corrige y el líder lo verifica antes de aceptar el producto |
| Menor | La corrección exige menos que el umbral y no afecta lo anterior | Se corrige antes de cerrar la fase |

*Nota.* Elaboración a partir de la definición de errores mayores y menores de Pressman (2010). El umbral de una hora-persona se recalibra con las primeras revisiones.

Al terminar la verificación, el líder emite el informe resumen (Tabla 18) con la decisión sobre el producto. Se proponen tres decisiones. Se acepta si no quedan hallazgos mayores. Se acepta con correcciones si hay hallazgos mayores que el productor puede corregir y el líder puede verificar sin otra reunión. Se rechaza si los hallazgos mayores obligan a rediseñar el producto o si impiden verificarlo, y en ese caso se repite la revisión. La decisión se justifica por escrito.

**Tabla 18**  
*Plantilla del informe resumen de la revisión*

| Campo | Contenido |
| :---- | :---- |
| Producto y versión | Identificador de la revisión (RV-xx), nombre del producto y versión |
| Participantes y roles | Productor, líder, revisores, registrador y representantes |
| Tipo de revisión | Verificación de escritorio, revisión en pareja, walkthrough o inspección |
| Esfuerzo | Preparación (Ep), reunión (Ea) y corrección (Er), en horas-persona |
| Tamaño del producto | WPS, con su unidad (páginas, líneas de código u operaciones) |
| Hallazgos | Número de hallazgos mayores y menores, y densidad de error |
| Decisión | Aceptar, aceptar con correcciones o rechazar, con su justificación |
| Firmas | Líder, representante del SQA y, cuando corresponda, representante del usuario |

*Nota.* Elaboración a partir de lo que Pressman (2010) indica que debe contener el informe de una revisión: qué se revisó, quién lo revisó y qué se encontró.

## Indicadores del proceso de revisión (Tabla 19 de revisiones) · pasos 3 y 8
**Tabla 19**  
*Indicadores del proceso de revisión*

| Indicador | Fórmula | Meta propuesta |
| :---- | :---- | :---- |
| Cobertura de revisión | Revisiones de riesgo alto realizadas / revisiones de riesgo alto planificadas | 100 % antes de ejecutar las pruebas |
| Cierre de hallazgos mayores | Hallazgos mayores cerrados y verificados / hallazgos mayores registrados | 100 % antes de aceptar el producto |
| Eficacia de eliminación de defectos | E / (E \+ D), con E los hallazgos antes de liberar y D los defectos detectados después | Al menos 85 % (propuesta inicial) |
| Esfuerzo por revisión formal | Ep \+ Ea de cada revisión formal | No más de 10 horas-persona |
| Densidad de error | Errtotal / WPS por tipo de producto | Línea base con las tres primeras revisiones |

*Nota.* Las metas son un diseño;

## Inventario de activos y orden de recuperación (Tabla 7 de recuperación) · paso 4
**Tabla 7**  
*Inventario de activos y orden de recuperación*

| Orden | Activo | Depende de | Dónde está |
| :---- | :---- | :---- | :---- |
| 1 | Servidor o contenedores de la aplicación (Apache, PHP, CodeIgniter) | Energía, red, imagen de contenedor | Servidor de la MYPE o alojamiento en la nube (L-02) |
| 2 | Base de datos MySQL (12 tablas) | Servidor, almacenamiento | Mismo servidor |
| 3 | Archivos de la aplicación y configuración (imágenes, certificado, archivos de configuración) | Servidor | Servidor; configuración fuera del repositorio |
| 4 | Código fuente y definición de contenedores | GitHub | Repositorio en GitHub (copia fuera del sitio) |
| 5 | Acceso y nombre de dominio, si existen | Proveedor de dominio | Proveedor externo |
| 6 | Equipos de punto de venta y navegadores | Energía, red local | Local de la MYPE |
| 7 | Respaldos | Medios de copia | Local, disco externo y nube |
| 8 | Registros en papel | Local | Local de la MYPE |

*Nota.* Elaboración a partir de la arquitectura descrita en el proyecto. El inventario se completa al conocer el entorno real.

## Procedimientos de recuperación (Tabla 16 de recuperación) · pasos 4 y 7
**Tabla 16**  
*Procedimientos de recuperación por escenario*

| ID y escenario | Disparador | Pasos principales | Verificación |
| :---- | :---- | :---- | :---- |
| RB-01 Falla del servidor, del disco o robo (R-03, R-07) | El sistema no responde y el servidor no se recupera en 30 minutos | 1) Declarar el desastre. 2) Levantar la aplicación en el sitio alterno desde GitHub y los contenedores. 3) Restaurar la última copia de la base de datos. 4) Restaurar archivos y configuración. 5) Redirigir a los usuarios. 6) Registrar a mano las ventas en el intervalo | Contar productos y ventas; comparar el último registro con el cuaderno; un usuario por rol entra |
| RB-02 Ransomware (R-04) | Archivos cifrados o aviso de rescate | 1) Aislar los equipos de la red. 2) Avisar al coordinador. 3) No restaurar sobre el equipo afectado. 4) Reconstruir en un entorno limpio. 5) Restaurar desde la copia desconectada o inmutable más reciente anterior al ataque. 6) Cambiar todas las contraseñas y claves. 7) Revisar cómo entró | Datos íntegros; accesos nuevos; sin rastros del software malicioso |
| RB-03 Pérdida del local por sismo, incendio o inundación (R-05, R-06) | El local es inhabitable | 1) Evacuar y reunir a las personas; atender su bienestar. 2) Contar al personal. 3) Decidir si se opera. 4) Recuperar el sistema en el sitio alterno (RB-01). 5) Operar desde casa o un local alterno | Todas las personas contadas; sistema disponible dentro del RTO |
| RB-04 Borrado o modificación indebida de datos (R-01, R-09) | Datos faltantes o incorrectos detectados | 1) Limitar el acceso para evitar más cambios. 2) Restaurar la copia anterior al error en una base temporal. 3) Extraer los registros afectados. 4) Reincorporarlos a la base actual. 5) Revisar permisos y quién lo causó | Los registros coinciden con los de la copia; no se pierden operaciones posteriores |
| RB-05 Corte de energía (R-02) | Sin energía en el local | 1) Cerrar el sistema de forma ordenada con la UPS. 2) Operar con registro manual. 3) Al regresar la energía, iniciar y revisar la integridad de la base de datos. 4) Capturar las ventas manuales | Base sin errores; existencias cuadradas |
| RB-06 Actualización fallida (R-10) | El sistema falla tras un cambio | 1) Detener el cambio. 2) Volver a la versión anterior del código y de los contenedores. 3) Restaurar la base si se modificó. 4) Registrar la causa | La versión anterior opera; pruebas básicas pasan |

*Nota.* Elaboración a partir de las fases descritas por Microsoft Azure. Los pasos se ajustan al entorno real y se prueban en un simulacro.

## Regreso al entorno principal (Tabla 17 de recuperación) · paso 7
**Tabla 17**  
*Regreso al entorno principal (failback)*

| Paso | Acción |
| :---- | :---- |
| 1 | Confirmar que la causa original está resuelta y que el entorno principal es estable |
| 2 | Programar el regreso fuera del horario de ventas y avisar a los usuarios |
| 3 | Tomar una copia final de la base del sitio alterno |
| 4 | Restaurar esa copia en el entorno principal y verificar la integridad |
| 5 | Redirigir a los usuarios al entorno principal y mantener el alterno sin cambios hasta confirmar |
| 6 | Registrar lo ocurrido, los tiempos reales de RTO y RPO, y las lecciones aprendidas |

*Nota.* Elaboración a partir del entorno principal.

## Pruebas periódicas del plan (Tabla 20 de recuperación) · pasos 4 y 10
**Tabla 20**  
*Pruebas periódicas del plan*

| Prueba | Frecuencia | Qué verifica | Criterio de éxito |
| :---- | :---- | :---- | :---- |
| Restauración de un respaldo | Mensual | Que la copia se puede abrir y restaurar | Base restaurada con el mismo número de registros y sin errores |
| Ejercicio de mesa | Cada seis meses | Que el equipo conoce roles, decisiones y canales | Cada rol explica qué hace; se corrigen los vacíos |
| Simulacro de recuperación completa | Anual | Que el sistema se reconstruye en el sitio alterno | RTO y RPO medidos dentro de las metas |
| Simulacro de evacuación | Anual | Rutas, puntos de reunión y conteo de personas | Todas las personas contadas en el tiempo previsto |
| Revisión de configuración | Tras cada cambio importante | Que el entorno real coincide con lo documentado | Sin diferencias no registradas |

*Nota.* Elaboración propia. El Apéndice B da el registro de una prueba.

## Plan de implantación de controles de seguridad (Tabla 17 de seguridad) · pasos 5 y 10
**Tabla 17**  
*Plan de implantación de los controles de seguridad*

| Etapa | Controles | Responsable propuesto |
| :---- | :---- | :---- |
| Inmediata | Verificar el control de acceso por roles con un usuario por rol; revisar cómo se guardan las contraseñas; sacar secretos del repositorio; definir la política de contraseña y de baja de accesos | Oscar Nájera; César Roque |
| Corto plazo | HTTPS y firewall; respaldos fuera del servidor con restauración probada; registro de eventos; publicar la política de privacidad en el sistema | Oscar Nájera; Miguel Ángel (política visible); César Roque |
| Corto Plazo | Actualizar CodeIgniter a 3.1.13; planear PHP y MySQL; autenticación multifactor; VPN; revisión periódica de roles y accesos | Oscar Nájera; César Roque |
| Continua | Revisión de accesos fallidos, actualización de equipos y revisión de vulnerabilidades | César Roque |

*Nota.* Los responsables se proponen a partir de las responsabilidades del equipo.

## Alcance de la auditoría (Tabla 15 de auditoría) · paso 9
**Tabla 15**  
*Alcance de la auditoría aplicado al sistema*

| Aspecto | Qué se revisa | Evidencia |
| :---- | :---- | :---- |
| Eficiencia y uso | Si el sistema reemplaza de verdad los registros manuales y si los roles se usan según su función | Matriz de roles; observación del uso |
| Eficacia de los controles | Control de acceso por rol; validación de datos; almacenamiento seguro de contraseñas; existencia de respaldos de la base de datos | Configuración de la tabla de grupos; Cassos de prueba: CP-04 a CP-10; configuración del respaldo |
| Cumplimiento | Estrategia de ramas, mensajes de commit, aprobación de cambios mediante pull request y versiones soportadas | Historial del repositorio; configuración de GitHub |
| Calidad de los datos | Integridad referencial con InnoDB y consistencia del stock con las ventas registradas | Esquema de la base de datos; Casos de Prueba: CP-09 y CP-12 |

*Nota.* Elaboración a partir de los aspectos de alcance de Prueba, mantenimiento y auditoría.

## Plan de auditoría (Tabla 16 de auditoría) · paso 9
**Tabla 16**  
*Plan de auditoría propuesto*

| Fase | Actividad | Producto |
| :---- | :---- | :---- |
| 1. Planear | Definir alcance, criterios (estrategia de ramas, matriz de roles, plan de pruebas) y fechas | Plan de auditoría |
| 2. Solicitar evidencia | Pedir acceso al repositorio, a los casos de prueba y resultados, a los registros de cambios y a la configuración | Lista de evidencias recibidas |
| 3. Entrevistar | Conversar con quien desarrolló cada módulo sobre decisiones y controles | Notas de entrevista |
| 4. Probar de forma independiente | Ejecutar casos de control propios, como intentar un acceso no permitido o una venta con datos inválidos | Registro de pruebas del auditor |
| 5. Informar | Redactar el informe con no conformidades, observaciones y recomendaciones, y acordar un plan de corrección | Informe de auditoría |

*Nota.* Elaboración a partir del proceso de auditoría de Prueba, mantenimiento y auditoría.

## Hallazgos preliminares de la documentación (Tabla 18 de auditoría) · paso 9
**Tabla 18**  
*Hallazgos preliminares de la documentación del proyecto*

| ID | Observación | Evidencia | Recomendación |
| :---- | :---- | :---- | :---- |
| O-01 | No hay casos de prueba documentados ni pruebas unitarias, aunque el alcance incluye pruebas en cuatro niveles | Línea base del proyecto | Ejecutar el plan de pruebas y documentar los resultados |
| O-02 | CodeIgniter está en 3.1.6 y existe la 3.1.13 con correcciones de seguridad | Tabla de componentes tecnológicos | Planificar la actualización como mantenimiento adaptativo |
| O-03 | PHP 7.4 no tiene soporte oficial | Nota de la tabla de componentes | Documentar un plan de migración |
| O-04 | El desarrollo ocurrió sin repositorio central ni registro de tareas | Línea base del proyecto | Mantener las issues y los pull requests como registro |
| O-05 | La figura de arquitectura muestra XAMPP, mientras que el staging usa Docker | Nota de la figura de arquitectura | Unificar o aclarar la documentación del entorno |
| O-06 | El proyecto menciona un catálogo de clientes que ningún módulo implementa | Descripción del proyecto y lista de módulos | Aclarar si queda fuera del alcance |

*Nota.* Elaboración propia a partir de los documentos del proyecto.