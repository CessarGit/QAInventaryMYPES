# Fase 2 · Paso 4: respaldo y recuperación

El paso 4 demuestra que la información se puede recuperar. Para eso se crearon scripts de respaldo y restauración, se hizo una restauración de prueba y se ejecutó un simulacro de pérdida total con medición del tiempo de recuperación (RTO) y de la pérdida de datos (RPO). Al final se preparó la base de pruebas `stock_test`. **Los respaldos no quedaron automáticos:** en staging se ejecutan a mano (ver desviaciones).

| Subpaso | Fecha de desarrollo | Evidencia |
|---|---|---|
| 4.0 Ajustes previos | 14/11/2026 | PR #25, commit `637fba6` |
| 4.1 Scripts y revisión RV-10 | 14/11/2026 | PR #26; `RV-10-scripts.md` |
| 4.2 Restauración de prueba | 14/11/2026 | `4.2-restauracion.txt` |
| 4.3 Simulacro RB-01 | 14/11/2026 | `4.3-simulacro-rb01/simulacro-rb01.md` |
| 4.4 Base `stock_test` con PC-0 | 14/11/2026 | PR #28, commit `13fd565` |

Ningún subpaso del paso 4 tenía fecha de diseño en el plan.

## 1. Ajustes previos (subpaso 4.0)

- La zona horaria del staging se fijó en `America/El_Salvador`, para que las horas de los respaldos y del simulacro coincidan con las del autor.
- La carpeta `backups/` quedó fuera de git. Solo se versiona un `.htaccess` que impide a Apache servirla.

## 2. Scripts (subpaso 4.1)

| Script | Qué hace | Protecciones |
|---|---|---|
| `respaldo.sh` | Copia completa de una base (tablas, datos, rutinas y disparadores) en `backups/`, comprimida y con su suma sha256 | Archivos legibles solo por el dueño (permisos 600). La contraseña se toma dentro del contenedor y no aparece en la línea de comandos. Si la copia falla a medias, se borra |
| `restaurar.sh` | Restaura una copia en la base indicada | Verifica la suma sha256 antes de restaurar. Restaurar sobre `stock` exige una confirmación explícita (`--forzar`). Avisa si la aplicación sigue en marcha |
| `verificar-conteos.sh` | Compara el número de registros de las 12 tablas entre dos bases | Devuelve 0 si coinciden, 1 si difieren y 2 si una base no existe o un conteo falla: un error nunca cuenta como coincidencia |

Los scripts se revisaron con la lista L5 en RV-10. Se encontraron 2 hallazgos mayores, los dos corregidos antes de integrar:
- **H-RV10-01:** las copias, que contienen hashes de contraseñas y datos de ventas, quedaban legibles por otros usuarios.
- **H-RV10-02:** la verificación podía informar éxito aunque fallara la consulta.

## 3. Restauración de prueba (subpaso 4.2)

**Respaldo:** Tabla 20 de recuperación, restauración mensual en una base aparte y comparación de conteos.

**Qué se hizo:**
1. Se respaldó `stock`.
2. Se restauró la copia en `stock_restore`; la restauración tardó 1,2 s.
3. Se compararon los conteos de las 12 tablas.

| Comprobación | Resultado |
|---|---|
| Suma sha256 antes de restaurar | Correcta |
| Conteos de las 12 tablas entre `stock` y `stock_restore` | Las 12 coinciden |
| Permisos de los respaldos (H-RV10-01) | 600, solo el dueño |
| Base inexistente (H-RV10-02) | Código de salida 2, como se esperaba |

**Resultado:** restauración sin pérdida de registros. **Desviación:** la ejecutó Claude Code con autorización del autor; el manual indicaba que la ejecutara el autor.

## 4. Simulacro de pérdida total RB-01 (subpaso 4.3)

**Respaldo:** Tabla 16 de recuperación (RB-01: falla del servidor o del disco) y metas de la Tabla 9 de recuperación (RTO 4 h y RPO 1 h para la base, el control de acceso y las ventas).

**Qué se hizo.** El autor eliminó los contenedores **y el volumen de datos**, es decir, la base `stock` completa. Luego levantó el sistema vacío, restauró la última copia y verificó el resultado. Para comprobar de dónde venían los datos usó dos registros testigo:

| Testigo | Cuándo se creó | Resultado esperado tras restaurar | Resultado obtenido |
|---|---|---|---|
| `TESTIGO-ANTES` | Antes del respaldo | Debe aparecer | Aparece |
| `TESTIGO-DESPUES` | Después del respaldo | No debe aparecer | No aparece |

**Tiempos registrados por el autor (14/11/2026):**

| Tramo | Duración |
|---|---|
| Desastre → sistema levantado | 0,01 h |
| Sistema levantado → base restaurada | 0,03 h |
| Base restaurada → verificación | 0,02 h |

| Medida | Valor | Meta (Tabla 9) | ¿Cumple? |
|---|---|---|---|
| RTO | **0,06 h** (unos 3,6 minutos). Según Docker, al menos 0,072 h; la diferencia es compatible con el redondeo del autor | 4 h | Sí |
| RPO | No calculable con exactitud, porque no se anotó la hora del desastre. Cota superior según Docker: **menos de 0,14 h** | 1 h | Sí, aun con la cota superior |

El simulacro demostró que se pierde exactamente lo registrado después de la última copia (`TESTIGO-DESPUES`).

**Desviaciones:**
- RB-01 prevé levantar la aplicación en un sitio alterno. En staging se reconstruyó en el mismo equipo: se simuló la pérdida de la base, no la del equipo.
- La verificación «un usuario por rol entra» se limitó al administrador, que es el único usuario de `stock`. La verificación por rol se hizo con PC-0 en `stock_test`.
- La hora del desastre no se anotó; por eso el RPO se reporta como cota.

## 5. Base de pruebas `stock_test` con PC-0 (subpaso 4.4)

Se creó una base aislada para las pruebas, para que nunca escriban en `stock`. Se recrea con un script que se niega a operar sobre cualquier otra base.

| Dato PC-0 | Contenido |
|---|---|
| Grupos | Los 5 roles de la Tabla 5 (Gerente, Ventas, Bodega, Logística y Administrador), con los permisos que salen del mapeo de RV-01 |
| Usuarios | Uno activo por rol; la contraseña sale de una variable de `.env`, no del repositorio |
| Producto | `PRODUCTO-PC0`, con stock 5 (D-01) |
| Tienda | `TIENDA-PC0` |

**Verificación:** el autor entró con cada usuario de PC-0 y confirmó que el menú de cada rol coincide con la matriz. **Desviación:** sin capturas, por decisión del autor.

## 6. Desviaciones del paso

| Desviación | Diseño (Tabla 11 de recuperación) | Lo realizado |
|---|---|---|
| Respaldos no automáticos | Copias cada hora, diarias y mensuales | Copias manuales con `respaldo.sh`; sin programación |
| Retención no aplicada | 48 h, 30 días y 12 meses | Las copias se acumulan en `backups/` |
| Sin copias fuera del equipo | Nube en otra región y disco desconectado | Solo disco local |

Por eso el RPO real depende de la frecuencia con que se ejecute el respaldo a mano. El simulacro mide la recuperación, no la política de copias.
