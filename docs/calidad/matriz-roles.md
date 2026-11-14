# Matriz de roles y permisos

Copia de la Tabla 5 de `fuente-informe.md` (única fuente de verdad sobre permisos) con una corrección: Settings del Administrador pasa de CRUD a RU (propia), según la revisión RV-01 (`docs/calidad/revisiones/RV-01-matriz.md`, H-RV01-01 y H-03, opción A). Fuera de esa celda, si difieren, manda `fuente-informe.md`.

| Módulo | Gerente | Ventas | Bodega | Logística | Administrador |
| :---- | :---- | :---- | :---- | :---- | :---- |
| Users | — | — | — | — | CRUD |
| Groups | — | — | — | — | CRUD |
| Settings | RU (propia) | RU (propia) | RU (propia) | RU (propia) | RU (propia) |
| Brands | CRUD | — | CRUD | CRUD | — |
| Categories | CRUD | — | CRUD | CRUD | — |
| Attributes | CRUD | — | CRUD | CRUD | — |
| Stores | CRUD | — | R | R | — |
| Products | CRUD | R | CRUD | CRUD | — |
| Orders | CRUD | CR | — | — | — |
| Reports | R | R | R | R | — |
| Company | CRU | — | — | R | — |
| Profile | R | R | R | R | R |

*Nota.* C = crear; R = leer; U = actualizar; D = eliminar; — = sin acceso; RU (propia) = el usuario lee y actualiza solo su propia cuenta.
