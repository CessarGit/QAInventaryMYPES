# Lista de verificación para revisión por pares y auditoría de producto

Úsala en cada Pull Request (revisión por pares) y en la muestra de 3 archivos de la auditoría de producto (PR-06).
Marca **Sí / No / N/A** y anota la línea cuando sea **No**.

| # | Pregunta | Sí/No/N/A | Línea / nota |
|---|---|---|---|
| 1 | ¿Cada acción pública del controlador revisa el permiso (`exigir_permiso()` o `in_array`) antes de leer o escribir datos? | | |
| 2 | ¿Los métodos auxiliares del controlador son `private` (no se abren por URL)? | | |
| 3 | ¿Todo dato que viene del usuario o de la BD y se imprime en HTML pasa por `html_escape()`? | | |
| 4 | ¿Las consultas usan parámetros (`?`) o Query Builder, nunca concatenación de `$_POST`? | | |
| 5 | ¿Las operaciones con varias escrituras usan `trans_begin()` / `trans_commit()` / `trans_rollback()`? | | |
| 6 | ¿Los montos e inventario se calculan en el servidor (no se confía en el formulario)? | | |
| 7 | ¿Hay al menos una prueba nueva o actualizada para el cambio, con `@caso`, `@requisito`, `@nivel` y `@tecnica`? | | |
| 8 | ¿El nombre de la prueba dice qué comportamiento verifica? | | |
| 9 | ¿No hay contraseñas, claves ni datos reales de clientes en el código o en las pruebas? | | |
| 10 | ¿El semáforo del PR está en verde (o en ámbar con el riesgo registrado)? | | |

Revisor: __________  Fecha: __________  Resultado: ☐ Aprobado ☐ Cambios solicitados
