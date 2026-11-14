# Fase 1 · Metas y criterio de salida

La fase se cierra con dos elementos: las metas que se medirán al final de la iteración y el criterio para pasar a la Fase 2.

## 1. Metas de la iteración (Tabla 13 de PHVA)

Se tomaron tal cual del informe y quedaron fijadas en el plan de pruebas. El informe las presenta como «umbrales propuestos que el equipo considera»; en esta iteración se adoptaron sin cambios.

| Objetivo | Métrica | Umbral |
|---|---|---|
| Los requisitos están cubiertos por pruebas | Requisitos con al menos un caso de prueba | 100 % de los requisitos prioritarios |
| La lógica crítica está probada | Cobertura de pruebas unitarias en los módulos Orders y Products | Al menos 60 % al cierre del proyecto |
| Sin defectos graves abiertos | Defectos críticos abiertos al cerrar una iteración | 0 |
| Los cambios se revisan | Cambios integrados con revisión por pares | 100 % |
| Los casos de prueba se ejecutan | Casos ejecutados frente a diseñados | Al menos 90 % |

### Estado de cada meta al cerrar la Fase 1

| Meta | Estado | Motivo |
|---|---|---|
| Requisitos con prueba | 6/6 = 100 % | Medida en el subpaso 2.2 |
| Cobertura de Orders y Products | Sin medir | La suite de pruebas aún no existía; pcov quedó instalado para medirla |
| Defectos críticos abiertos | Sin medir | No se había ejecutado ningún caso |
| Cambios revisados | Parcial | Desde el subpaso 1.4 los cambios entran por PR a `main` protegida (PR #2, #3, #9, #10 y #12). Los commits de 1.1 a 1.3 (`0ef5d0d`, `3255d55`, `8c9bb46`) se integraron antes de la protección y sin PR. La revisión la hace el autor, no un par |
| Casos ejecutados | 0/14 | Los casos estaban diseñados, sin ejecutar |

## 2. Criterio para pasar a la Fase 2

| Criterio | Estado al cerrar la Fase 1 | Evidencia |
|---|---|---|
| Etiqueta `linea-base` creada antes de cualquier cambio | Cumplido | Etiqueta en el repositorio |
| Entorno funcionando con PHP 7.4.33, MySQL 5.7 y pcov | Cumplido | Commit `0ef5d0d` (subpaso 1.1) |
| Sin credenciales en archivos versionados | Cumplido | Commit `3255d55` (subpaso 1.2) |
| Semáforo verde demostrado | Cumplido | PR #2; ejecución sobre `main` en `f9a93de` |
| Semáforo rojo demostrado | **Pendiente** | Se demostró después, en la Fase 2, con el PR #33 (`1.4-semaforo-rojo.txt`) |
| Etiquetas y plantillas de issue y de PR en GitHub | Cumplido | Commit `8c9bb46` (subpaso 1.3) |
| Hallazgos H-01 a H-06 confirmados, con issues | Cumplido | `hallazgos-topdown.md`; issues #4 a #7 |
| Trazabilidad CP-01 a CP-14 revisada con L4 | Cumplido | `2.2-L4.md`, firmada |
| Plan de pruebas aprobado, con fechas | Cumplido | PR #12 |
| Bitácora con una entrada por subpaso (1.0 a 2.3) | Cumplido | `bitacora.md`, firmada (PR #24) |

**Fecha de cierre:** en el diseño, evidencias y firmas el 10/11/2026; en el desarrollo, el 14/11/2026 (+4 días).

**Decisión de salida:** la fase se cerró con 9 de 10 criterios cumplidos. El único pendiente, la demostración del semáforo rojo, quedó registrado en el estado de la iteración de `CLAUDE.md` y se completó durante la Fase 2.
