# Fase 4 · Punto de partida

## 1. Resultado de la iteración al llegar a Actuar

| Dimensión | Antes (línea base) | Después (ronda 2 y verificación) |
|---|---|---|
| Casos de prueba | 0 | 20 diseñados, 20 ejecutados, 19 pasan |
| Pruebas automatizadas | 0 | 29 en PHPUnit, en la integración continua |
| Defectos conocidos | Ninguno registrado | 10 registrados; 6 corregidos (1 crítico y 5 altos) |
| Respaldo y restauración | Inexistentes | Scripts revisados; restauración probada; RTO 0,06 h |
| Revisiones | Ninguna | 4 revisiones, 50 hallazgos; 8 de 10 defectos anticipados |
| DPMO total | No medido | Ronda 1: 114 285,7. Ronda 2: 14 285,7 |
| Entorno y control de cambios | XAMPP sin repositorio central | Docker; `main` protegida; semáforo verde y rojo |

## 2. Brechas recibidas de la Fase 3

Las 17 brechas (B-01 a B-17) de `fase-3/05-objetivos-y-criterio-de-salida.md` se agrupan en 12 acciones (AC-01 a AC-12) en el paso 10.

## 3. Decisiones del autor que enmarcan la fase

| Decisión | Efecto en Actuar |
|---|---|
| La actualización de CodeIgniter 3.1.13 es de la iteración 2 (#52) | AC-09 queda en el plan de la iteración 2, no en esta |
| Claude Code es la pareja de revisión | B-02 se atiende con un check obligatorio de revisión en pareja (AC-06). Se declara que no es un revisor humano independiente |
| El foco son las métricas y el código, no la documentación | Las acciones se definen por su efecto medible. La documentación se limita a lo que exige la estandarización |
