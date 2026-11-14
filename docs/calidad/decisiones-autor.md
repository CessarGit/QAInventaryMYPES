# Decisiones del autor

Decisiones que las fuentes de diseño no fijan y que el autor toma como requisito. Cada una se registra **antes** de ejecutar los casos afectados, para que el resultado esperado no se ajuste después al resultado obtenido.

| ID | Pregunta abierta | Caso | Opciones consideradas | Decisión | Justificación | Fecha |
|---|---|---|---|---|---|---|
| D-01 | Valor de N (stock inicial del producto de prueba) | CP-01, CP-02, CP-03, CP-19 | Cualquier entero positivo pequeño | **N = 5** | Es fácil de verificar a mano y deja margen para N + 1 (CP-03) y para N − 1 (CP-19) | 14/11/2026 |
| D-02 | Campos obligatorios en el alta de producto | CP-08, CP-20 | (a) Los mínimos para vender: nombre, SKU, precio, cantidad, tienda. (b) Todos los del formulario | **Nombre, SKU, precio, cantidad, marca, categoría y tienda** | RQ-04 pide un producto con marca, categoría y tienda; nombre, SKU, precio y cantidad son el mínimo para venderlo. No sale de lo que el código valida hoy | 14/11/2026 |
| D-03 | Importe que suma el reporte anual | CP-09, CP-12 | (a) Neto: total que paga el cliente, con cargos e IVA. (b) Bruto: suma de precio × cantidad | **(a) Neto** | El total cobrado (con cargos e IVA) es el que sirve para decidir compras (OA-4); el esperado se calcula a mano | 14/11/2026 |
| D-04 | Algoritmo de hash exigido para las contraseñas | CP-14 | (a) Cualquier hash. (b) Hash adaptativo (`password_hash`: bcrypt o argon2) | **(b) Hash adaptativo** (`password_hash`: bcrypt o argon2) | Recomendación de la Tabla 13 de seguridad; MD5 o SHA-1 sin sal son falla | 14/11/2026 |
| D-05 | Longitud máxima de los campos de texto | CP-20 | 255 (tamaño habitual de `varchar`) u otro | **255 caracteres** | Tamaño de los `varchar` de `stock.sql`; los requisitos no fijan otro valor | 14/11/2026 |
| D-06 | Tratamiento de las pruebas que fallan por defectos abiertos en la integración continua | CI | (a) Bloquear (rojo). (b) Excluirlas con `--exclude-group defecto-abierto` y registrar el riesgo (ámbar, Tabla 18) | **(b)** Excluirlas con `--exclude-group defecto-abierto` y registrar el riesgo | Es el ámbar de la Tabla 18: se integra con aprobación y riesgo registrado | 14/11/2026 |
| D-07 | ¿Qué permiso recibe Logística en Company, cuya fila de la Tabla 5 es R (solo lectura)? | CP-04 a CP-07, PC-0 | (a) `updateCompany`, que también concede editar y excede la matriz. (b) Ningún permiso, que niega la lectura. (c) Corregir la matriz y quitar el R | **(b) Ningún permiso** | El código no tiene un permiso de solo lectura para Company (`application/controllers/Company.php:25`). Conceder `updateCompany` daría a Logística una edición que la matriz no le permite (mínimo privilegio, L2-6). Con (b), la diferencia entre la matriz y el código queda visible como hallazgo (H-RV01-07, issue #14) en lugar de ocultarse | 14/11/2026 |
| D-08 | ¿El reporte anual cuenta las ventas no pagadas? | CP-09, CP-12 | (a) Todas las ventas registradas. (b) Solo las marcadas como pagadas (`paid_status = 1`) | **(a) Todas las ventas** | CP-09 registra una venta (que nace no pagada, `application/models/Model_orders.php:52`) y espera verla en el reporte; RQ-03 pide las ventas del año. Tomada en el subpaso 5.6 al corregir #37, después de ejecutar CP-09 y CP-12 (desviación: no fue previa) | 14/11/2026 |

**Firma del autor:** César Roque · fecha: 14/11/2026 (registrada por Claude Code con autorización del autor en el chat)

**Firma del autor (D-08):** César Roque · fecha: 14/11/2026 (registrada por Claude Code con autorización del autor en el chat)
