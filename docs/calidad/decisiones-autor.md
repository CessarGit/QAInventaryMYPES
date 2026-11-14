# Decisiones del autor

Decisiones que las fuentes de diseño no fijan y que el autor toma como requisito. Cada una se registra **antes** de ejecutar los casos afectados, para que el resultado esperado no se ajuste después al resultado obtenido.

| ID | Pregunta abierta | Caso | Opciones consideradas | Decisión | Justificación | Fecha |
|---|---|---|---|---|---|---|
| D-01 | Valor de N (stock inicial del producto de prueba) | CP-01, CP-02, CP-03, CP-19 | Cualquier entero positivo pequeño | **N = 5** | Es fácil de verificar a mano y deja margen para N + 1 (CP-03) y para N − 1 (CP-19) | 14/11/2026 |
| D-02 | Campos obligatorios en el alta de producto | CP-08, CP-20 | (a) Los mínimos para vender: nombre, SKU, precio, cantidad, tienda. (b) Todos los del formulario | | Debe salir del requisito RQ-04 ("producto con marca, categoría y tienda") y del uso de una MYPE, **no** de lo que el código valida hoy | |
| D-03 | Importe que suma el reporte anual | CP-09, CP-12 | (a) Neto: total que paga el cliente, con cargos e IVA. (b) Bruto: suma de precio × cantidad | | Para decidir compras (OA-4), lo usual es el total cobrado. Defínelo y calcula el esperado a mano | |
| D-04 | Algoritmo de hash exigido para las contraseñas | CP-14 | (a) Cualquier hash. (b) Hash adaptativo (`password_hash`: bcrypt o argon2) | | La Tabla 13 de seguridad recomienda un hash adaptativo. MD5 o SHA-1 sin sal se considerarían falla | |
| D-05 | Longitud máxima de los campos de texto | CP-20 | 255 (tamaño habitual de `varchar`) u otro | | Debe salir del requisito. Si el requisito no lo fija, decide un valor razonable y regístralo | |
| D-06 | Tratamiento de las pruebas que fallan por defectos abiertos en la integración continua | CI | (a) Bloquear (rojo). (b) Excluirlas con `--exclude-group defecto-abierto` y registrar el riesgo (ámbar, Tabla 18) | | Sugerencia: (b). Es lo que la Tabla 18 describe como ámbar: se integra con aprobación y riesgo registrado | |
| D-07 | ¿Qué permiso recibe Logística en Company, cuya fila de la Tabla 5 es R (solo lectura)? | CP-04 a CP-07, PC-0 | (a) `updateCompany`, que también concede editar y excede la matriz. (b) Ningún permiso, que niega la lectura. (c) Corregir la matriz y quitar el R | **(b) Ningún permiso** | El código no tiene un permiso de solo lectura para Company (`application/controllers/Company.php:25`). Conceder `updateCompany` daría a Logística una edición que la matriz no le permite (mínimo privilegio, L2-6). Con (b), la diferencia entre la matriz y el código queda visible como hallazgo (H-RV01-07, issue #14) en lugar de ocultarse | 14/11/2026 |

**Firma del autor:** César Roque · fecha: ____
