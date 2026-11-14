# Simulacro RB-01: pérdida total del servidor de base de datos (subpaso 4.3)

Procedimiento detallado y resultados del simulacro de recuperación RB-01, adaptado al staging en Docker. Sirve también como guía para repetirlo (Tabla 20 de recuperación: simulacro de recuperación completa, anual).

| Campo | Contenido |
|---|---|
| Escenario | RB-01, falla del servidor, del disco o robo (Tabla 16 de recuperación) |
| Metas | RTO 4 h y RPO 1 h para la base de datos, el control de acceso y las ventas (Tabla 9 de recuperación) |
| Entorno | Staging: `docker-compose.yml` (servicios `app` y `db`, volumen `db_data`) |
| Fecha | 14/11/2026 (America/El_Salvador, CST, UTC−6) |
| Ejecutó | César Roque |
| Scripts | `scripts/respaldo.sh` y `scripts/restaurar.sh` (subpaso 4.1, revisión RV-10) |

## 1. Qué se demuestra

- **El desastre:** `docker compose down -v` elimina los contenedores **y el volumen `db_data`**, es decir, la base `stock` completa. Sobreviven el código, el `.env` y `backups/`, porque están en el disco del equipo y no dentro del volumen.
- **RTO medido:** cuánto se tarda en volver a operar (T-verificado − T0).
- **RPO demostrado:** cuántos datos se pierden (T0 − T-respaldo). Se pierde exactamente lo registrado después de la última copia.
- **Dos registros testigo:**
  - `TESTIGO-ANTES`, creado antes del respaldo: **debe aparecer** tras restaurar. Prueba que los datos vienen del respaldo y no del `stock.sql` inicial.
  - `TESTIGO-DESPUES`, creado después del respaldo: **no debe aparecer**. Muestra la pérdida dentro del RPO.

Con el volumen vacío, MySQL vuelve a cargar `stock.sql` al arrancar (`docker-compose.yml:49`), así que antes de restaurar la base queda "de fábrica": sin tiendas ni productos.

## 2. Preparación

1. Comprueba que el entorno funciona: `docker compose ps` muestra `app` y `db` en marcha y `db` como `healthy`.
2. Entra como administrador en `http://127.0.0.1/InventorySystem/`.
3. El alta de producto exige una tienda (`application/controllers/Products.php:100`) y `stock` no trae ninguna. En **Stores**, crea la tienda `TIENDA-SIMULACRO`.
4. Prepara la tabla de horas de la sección 4 para ir anotando.

## 3. Pasos

| # | Acción | Comando o pantalla | Resultado esperado | Hora que se anota |
|---|---|---|---|---|
| 1 | Crear el primer testigo | **Products → Add**: nombre `TESTIGO-ANTES`, SKU `T-ANTES`, precio 1, cantidad 5, tienda `TIENDA-SIMULACRO`, disponible | Mensaje de creación correcta; el producto aparece en el listado | — |
| 2 | Respaldar la base | `bash scripts/respaldo.sh` | `Respaldo de 'stock' · inicio/fin`, `Archivo: backups/stock_AAAAMMDD_HHMMSS.sql.gz` y su `.sha256`. **Anota también el nombre del archivo**: se usa en el paso 6 | **T-respaldo** = hora de la línea `fin:` |
| 3 | Crear el segundo testigo | **Products → Add**: `TESTIGO-DESPUES`, SKU `T-DESPUES`, mismos datos | El listado muestra los dos testigos (captura) | — |
| 4 | Provocar el desastre | `docker compose down -v` | `Container … Removed` y `Volume inventario_db_data Removed`; la página deja de responder | **T0** = antes de pulsar Enter |
| 5 | Levantar el sistema vacío | `docker compose up -d --build` y esperar a que `docker compose ps` muestre `db` como `healthy` y responda la página de login | Contenedores iniciados; MySQL carga `stock.sql`. Comprobación opcional: `bash scripts/verificar-conteos.sh stock stock` muestra `products 0` y `stores 0` | **T-sistema arriba** |
| 6 | Restaurar el respaldo | `docker compose stop app` · `bash scripts/restaurar.sh backups/<archivo del paso 2> stock --forzar` · `docker compose start app` | `Suma correcta.` y `Restauración en 'stock' · inicio/fin`, código 0. Sin `--forzar`, el script se niega a restaurar sobre `stock`; sin argumentos, muestra su forma de uso | **T-restaurado** = hora de la línea `fin:` |
| 7 | Verificar | Entrar como administrador y abrir **Products** (captura). Comprobación sin interfaz: `docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot -N -e "SELECT name FROM stock.products WHERE name LIKE '"'"'TESTIGO%'"'"'"'` | Aparece **`TESTIGO-ANTES`**; **no** aparece `TESTIGO-DESPUES`; existe `TIENDA-SIMULACRO`; el administrador inicia sesión | **T-verificado** |

En `stock` solo existe el usuario administrador del volcado, así que la verificación "un usuario por rol entra" de la Tabla 16 se limita a él. La verificación por rol se hace con PC-0 en `stock_test` (subpaso 4.4).

## 4. Tabla de horas (14/11/2026, CST)

| Marca | Hora | Duración de la fase (autor) | Fuente |
|---|---|---|---|
| T-respaldo | 11:07:57 | — | Archivo `backups/stock_20261114_110756.sql.gz` (hora de modificación; el nombre registra el inicio, 11:07:56) |
| T0 | No anotada (anterior a 11:16:21) | — | Cota de Docker: el volumen `inventario_db_data` se volvió a crear a las 11:16:21, después del `down -v` |
| T-sistema arriba | No anotada (posterior a 11:16:21) | T0 → T-sistema arriba: **0,01 h** | Autor. Docker: `db` creado e iniciado a las 11:16:21 |
| T-restaurado | No anotada (anterior a 11:20:39) | T-sistema arriba → T-restaurado: **0,03 h** | Autor. Docker: `app` se volvió a iniciar a las 11:20:39 (`docker compose start app`, después de la restauración) |
| T-verificado | No anotada (posterior a 11:20:39) | T-restaurado → T-verificado: **0,02 h** | Autor |

El autor registró la duración de cada fase en horas, redondeada a centésimas (±0,005 h por fase), no la hora de cada marca. Las horas de Docker se obtuvieron con `docker volume inspect inventario_db_data` y `docker inspect` de cada contenedor (UTC convertido a CST) y son cotas.

## 5. Resultados

### Verificación de los testigos (14/11/2026, 11:27 CST)

| Comprobación | Resultado | Esperado | ¿Cumple? |
|---|---|---|---|
| Testigos en `stock` después de restaurar | Solo `TESTIGO-ANTES` | Solo `TESTIGO-ANTES` | Sí |
| Productos en `stock` | 1 | 1 (`TESTIGO-ANTES`) | Sí |
| Tiendas en `stock` | 1 | 1 (`TIENDA-SIMULACRO`) | Sí |
| Testigos dentro del respaldo del paso 2 | Solo `TESTIGO-ANTES` | Solo `TESTIGO-ANTES` | Sí |
| Confirmación del autor en la interfaz | Aparece `TESTIGO-ANTES` | Aparece `TESTIGO-ANTES` | Sí |

### RTO y RPO

| Medida | Cálculo | Valor | Meta (Tabla 9 de recuperación) | ¿Cumple? |
|---|---|---|---|---|
| RTO | T-verificado − T0 = 0,01 + 0,03 + 0,02 | **0,06 h** (≈ 3,6 min) según el autor. Cota de Docker: al menos 0,072 h (4 min 18 s, de 11:16:21 a 11:20:39); compatible con el redondeo de las tres fases (máximo 0,075 h) | 4 h | **Sí** |
| RPO demostrado | T0 − T-respaldo | **No calculable con exactitud** (T0 no anotada). Cota superior según Docker: menos de 0,14 h (8 min 24 s, de 11:07:57 a 11:16:21) | 1 h | **Sí** (aun con la cota superior) |

Los valores no se redondean ni se sustituyen por las cotas (regla 17): el RTO es el del autor y el RPO se reporta como cota porque T0 no se anotó.

**Aclaración sobre el RPO:** en staging no hay respaldo cada hora (la Tabla 11 lo propone). El RPO real depende de la frecuencia con que se ejecute `respaldo.sh`; el simulacro demuestra que se pierde exactamente lo registrado después de la última copia (`TESTIGO-DESPUES`).

## 6. Desviaciones respecto al diseño

- RB-01 prevé levantar la aplicación en un **sitio alterno** desde GitHub (Tabla 16, paso 2). En staging se reconstruye en el mismo equipo: se simula la pérdida de la base, no la del equipo.
- Los pasos 4 (archivos y configuración), 5 (redirigir a los usuarios) y 6 (registrar ventas manuales) de RB-01 no aplican al staging.
- "Un usuario por rol entra" se limita al administrador (sección 3).
- `docker compose down -v` está bloqueado para Claude Code (`.claude/settings.json`); el autor ejecutó el simulacro.

## 7. Si algo sale mal

| Síntoma | Qué significa | Qué hacer |
|---|---|---|
| `restaurar.sh` muestra solo `Uso: …` | Se ejecutó sin el archivo y la base | Repetir con `backups/<archivo> stock --forzar` |
| La suma sha256 no coincide | El respaldo cambió o está corrupto | Detenerse y registrarlo como fallo del simulacro |
| Aparece `TESTIGO-DESPUES` | Se restauró una copia posterior o el volumen no se borró | Revisar el archivo usado y la salida del paso 4 |
| No aparece `TESTIGO-ANTES` | La restauración no se aplicó a `stock` | Revisar el código de salida del paso 6 |
| La página no responde tras el paso 5 | MySQL aún carga `stock.sql` | Esperar a que `db` esté `healthy` |

## 8. Evidencia que se guarda en esta carpeta

- Capturas de los pasos 3 y 7: **omitidas por decisión del autor**. La presencia de `TESTIGO-ANTES` y la ausencia de `TESTIGO-DESPUES` se verificaron con la consulta de la sección 5 y por el autor en la interfaz.
- Salidas de los pasos 2, 4, 5 y 6: no se conservaron; quedan el archivo de respaldo (`backups/stock_20261114_110756.sql.gz`, fuera de git) y las horas de Docker de la sección 4.
- Tabla de horas: completa con las duraciones del autor (sección 4).

**Firma del autor:** César Roque · 14/11/2026 (registrada por Claude Code con autorización del autor en el chat)
