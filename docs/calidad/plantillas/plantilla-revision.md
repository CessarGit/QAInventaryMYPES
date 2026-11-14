# Plantilla de revisión técnica (Tablas 16, 17 y 18 de revisiones)

Copia este archivo como `docs/calidad/revisiones/RV-xx-<producto>.md` para cada revisión.

---

# RV-xx · Producto revisado

## 1. Datos de la revisión

| Campo | Contenido |
|---|---|
| Producto y versión | RV-xx, archivos revisados, commit `abc1234` |
| Riesgo y técnica planificada | Según la Tabla 7 de revisiones |
| Técnica aplicada | Verificación de escritorio (autor) + segunda pasada (Claude Code como apoyo, no como revisor humano independiente) |
| Lista usada | L1 / L2 / L3 / L4 / L5 |
| Participantes y roles | Productor: autor original del código (proyecto de terceros). Revisor y registrador: César Roque. Apoyo: Claude Code |
| Desviación | La Tabla 7 pide inspección formal con tres personas; se trabaja solo. Se compensa con la lista y la doble pasada |

## 2. Respuestas a la lista

| N.º | Pregunta | Respuesta (Sí / No / N/A) | Evidencia (`archivo:línea`) | Hallazgo |
|---|---|---|---|---|
| 1 | | | | H-RVxx-01 |

Cada "No" es un hallazgo y va a la sección 3.

## 3. Lista de problemas (Tabla 16)

| ID | Ubicación | Descripción objetiva | Tipo | Gravedad | Encontrado por | Responsable | Estado |
|---|---|---|---|---|---|---|---|
| H-RVxx-01 | `archivo.php:línea` | Describe el artefacto, no a la persona | Omisión / ambigüedad / inconsistencia / defecto de diseño / forma | Mayor / menor | Autor / Claude / ambos | César Roque | Abierto |

Criterio de gravedad (Tabla 17): **mayor** si corregirlo toma más de 1 hora-persona, o si afecta permisos, dinero, existencias o datos (aunque sea fácil de corregir). **Menor** en cualquier otro caso.

## 4. Métricas

| Métrica | Valor |
|---|---|
| Ep: preparación del autor (horas) | |
| Ea: revisión del autor (horas) | |
| Er: corrección (horas, si aplica) | |
| WPS: tamaño revisado (líneas, con `wc -l`) | |
| Hallazgos mayores / menores | |
| Densidad de error (hallazgos / WPS × 1000) | |
| Hallazgos encontrados solo por el autor | |
| Hallazgos encontrados solo por Claude | |
| Hallazgos encontrados por ambos | |

## 5. Informe resumen y decisión (Tabla 18)

| Campo | Contenido |
|---|---|
| Decisión | Aceptar / Aceptar con correcciones / Rechazar |
| Justificación | |
| Hallazgos que pasan a pruebas | Caso de prueba que los confirmará (CP-xx) |
| Issues creados | #… |

**Firma del revisor:** César Roque · fecha: ____
