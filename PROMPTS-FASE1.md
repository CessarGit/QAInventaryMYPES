# Fase 1 · Planificar: guía de ejecución con Claude Code

Cubre los pasos 1 (preparar entorno) y 2 (Top-Down y plan) de la iteración.
Cada subpaso tiene: **modo** de Claude Code, **prompt** para copiar, **auditoría** (lo que tú verificas) y **evidencia** (lo que guardas).

Ciclo de cada subpaso:

```
Prompt → Claude propone un plan → apruebas → edita (ves el diff) → /auditar X.Y → verificas tú → /bitacora X.Y → commit
```

Modos de la extensión (selector al pie del cuadro de texto):
- **Plan:** Claude solo lee y propone, no edita. Úsalo para analizar.
- **Normal:** pide permiso antes de cada edición o comando. Úsalo para cambios.
- **Auto-aceptar:** no lo uses en esta iteración; anula tu auditoría.

---

## 0 · Preparación (manual, unos 10 minutos)

1. Abre la carpeta del proyecto en VS Code. Si VS Code pregunta, marca el espacio de trabajo como de confianza (Claude Code no funciona en modo restringido).
2. Verifica que el repositorio está limpio y marca la línea base **antes de cualquier cambio**:
   ```bash
   git status                      # debe decir "nothing to commit"
   git tag -a linea-base -m "Estado inicial antes de la iteración de calidad 1 (CI 3.1.6)"
   git checkout -b chore/fase1-planificar
   ```
3. Copia el contenido del kit en la raíz del repositorio: `CLAUDE.md`, `.claude/`, `docs/` y este archivo.
4. Haz el commit del kit tú mismo:
   ```bash
   git add CLAUDE.md .claude docs PROMPTS-FASE1.md
   git commit -m "docs(calidad): agrega kit de la iteración de calidad 1"
   ```
5. Abre el panel de Claude Code (icono Spark) y escribe: `Lee CLAUDE.md y docs/calidad/fuente-informe.md y resume en 5 líneas qué vamos a hacer y qué reglas debes respetar.`
   **Auditoría:** si el resumen omite "no hay funcionalidades nuevas" o "los resultados esperados salen de la matriz", corrígelo antes de seguir.

---

## Paso 1 · Preparar entorno

### 1.0 Inventario del repositorio · modo **Plan**

```
Sin modificar nada, haz un inventario del repositorio y entrégalo como tabla con columnas
Elemento | Encontrado | Evidencia (archivo:línea o comando) | ¿Contradice el informe?:

1. Versión real de CodeIgniter (constante CI_VERSION en system/core/CodeIgniter.php).
2. Archivos Docker existentes (Dockerfile, docker-compose*) y versiones de PHP, Apache y MySQL
   que usan, comparadas con la Tabla 2 de docs/calidad/fuente-informe.md.
3. Dónde están las credenciales de la base de datos y si alguna vez se versionaron
   (git log --oneline -- application/config/database.php). También $config['encryption_key'].
4. Si existen composer.json, application/tests/, .github/, docs/, CHANGELOG.md, .gitignore.
5. Controladores y modelos existentes y si corresponden a los 12 módulos de la Tabla 5.
6. Archivos .sql versionados y si contienen datos reales o contraseñas.
7. Si el entorno documentado es XAMPP o Docker (observación O-05 del informe).
Al final, lista los riesgos que debemos atender en los subpasos 1.1 a 1.5.
```

**Auditoría:**
- Abre tú mismo dos de las evidencias citadas y confirma que la línea dice lo que Claude afirma.
- Compara la versión de CodeIgniter y las versiones de Docker con la Tabla 2 y la Tabla 7.

**Evidencia:** pide a Claude que guarde la tabla en `docs/ejecucion/evidencias/1.0-inventario.md`.

### 1.1 Entorno Docker · modo **Normal**

```
Con base en el inventario, propón los cambios mínimos al entorno Docker. Si ya existe un
docker-compose, modifícalo; no lo reescribas desde cero. Requisitos:
- Servicio app: imagen php:7.4-apache, mod_rewrite activo, extensiones mysqli y pdo_mysql,
  extensión pcov para medir cobertura, Composer disponible.
- Servicio db: mysql:5.7, base "stock", volumen persistente, carga del dump inicial si existe
  en el repo, puerto publicado solo en 127.0.0.1 (o sin publicar si no lo necesito).
- Credenciales tomadas de un archivo .env (variables DB_HOST, DB_USER, DB_PASS, DB_NAME).
Muéstrame el plan antes de editar. Cita la Tabla 2 y la Tabla 13 de seguridad
(contenedores de staging).
```

**Auditoría (ejecútalo tú):**
```bash
docker compose config                          # el compose se interpreta sin errores
docker compose up -d --build
docker compose exec app php -v                 # esperado: PHP 7.4.33
docker compose exec app php -m | grep -i pcov  # esperado: pcov
docker compose exec db mysql --version         # esperado: 5.7.x
docker compose ps                              # puertos: db solo en 127.0.0.1 o sin publicar
```
Entra al sistema en el navegador con un usuario existente y abre un módulo.

**Evidencia:** captura de `php -v`, `mysql --version` y de la pantalla de inicio del sistema.

### 1.2 Secretos fuera del repositorio · modo **Normal**

```
Modifica application/config/database.php para que hostname, username, password y database
se lean con getenv() desde las variables de entorno del contenedor. Haz lo mismo con
$config['encryption_key'] en application/config/config.php si tiene un valor fijo.
Crea .env.example con las claves y valores de ejemplo NO reales, y agrega .env a .gitignore.
Si database.php tuvo credenciales en commits anteriores, dímelo con los hashes de commit;
no reescribas el historial. Cita la Tabla 13 de seguridad ("Secretos en el repositorio").
```

**Auditoría:**
```bash
git diff application/config/database.php application/config/config.php
git check-ignore .env        # esperado: imprime ".env"
git status                   # .env NO debe aparecer
```
- Crea tu `.env` copiando `.env.example` y pon los valores reales tú mismo (Claude no puede leerlo).
- Reinicia (`docker compose up -d`) y confirma que el sistema sigue conectando a la base.
- Si hubo credenciales en el historial, **cambia esa contraseña**: seguirá visible en commits antiguos.

**Evidencia:** diff de `database.php` y salida de `git check-ignore`.

### 1.3 Plantillas y etiquetas de GitHub · modo **Normal**

```
Crea, sin ejecutar nada contra GitHub:
1. .github/ISSUE_TEMPLATE/defecto.yml: caso de prueba (CP-xx), módulo, pasos, resultado
   esperado, resultado obtenido, severidad (crítica/alta/media/baja), evidencia.
2. .github/ISSUE_TEMPLATE/mantenimiento.yml: categoría (correctivo/adaptativo/perfectivo/
   preventivo), justificación, módulos afectados, tabla del informe que lo respalda.
3. .github/pull_request_template.md con casillas: caso de prueba enlazado, lista L1 revisada,
   CHANGELOG actualizado, pipeline en verde, sin secretos, sin funcionalidad nueva.
4. scripts/crear-etiquetas.sh con "gh label create" para tipo:correctivo, tipo:adaptativo,
   tipo:perfectivo, tipo:preventivo, sev:critica, sev:alta, sev:media, sev:baja (con --force
   para que se pueda repetir).
Relaciona cada plantilla con la Figura 1 y las Tablas 11 y 13 de mantenimiento, y con los
puntos 4 a 8 de la Tabla 17 de auditoría.
```

**Auditoría:**
- Las casillas del PR cubren los puntos 4 a 8 de la Tabla 17.
- Ejecuta tú: `gh auth status` y luego `bash scripts/crear-etiquetas.sh`. Revisa las etiquetas en GitHub.

**Evidencia:** captura de la página de etiquetas del repositorio.

### 1.4 Integración continua con semáforo · modo **Normal**

```
Crea .github/workflows/calidad.yml que se ejecute en push y pull_request:
- Job "sintaxis": PHP 7.4 con shivammathur/setup-php; ejecuta php -l sobre todos los .php de
  application/ y falla si alguno tiene error.
- Job "pruebas": servicio mysql:5.7; si existe application/tests/phpunit.xml, instala
  dependencias con Composer y ejecuta PHPUnit; si no existe, informa "Sin suite todavía
  (paso 5)" y termina en éxito.
Al inicio del archivo, un comentario que explique la correspondencia con la Tabla 18
(verde = integra; rojo = bloquea; ámbar queda pendiente de análisis estático).
```

**Auditoría (tú haces el push; Claude no puede):**
```bash
git push -u origin chore/fase1-planificar
```
- En la pestaña Actions de GitHub, el flujo debe salir en **verde**.
- **Prueba del rojo:** en una rama temporal, rompe a propósito la sintaxis de un `.php`, haz push y confirma que sale en **rojo**. Luego borra la rama.
- Configura tú la protección de `main` en GitHub: Settings → Branches (o Rules) → exigir que el job pase antes de fusionar. GitHub Free solo permite proteger ramas en repositorios públicos; si el tuyo es privado y gratuito, regístralo como desviación.

**Evidencia:** capturas del semáforo verde y del rojo, y de la regla de protección.

### 1.5 Estructura de documentación · modo **Normal**

```
Crea la estructura de docs descrita en CLAUDE.md:
- docs/README.md: índice con enlaces y una línea por documento.
- docs/calidad/matriz-roles.md: la Tabla 5 copiada sin cambios desde fuente-informe.md.
- docs/arquitectura/despliegue.md: diagrama Mermaid del entorno Docker REAL (servicios,
  puertos, volúmenes), que resuelve la observación O-05 (XAMPP frente a Docker).
- CHANGELOG.md en la raíz, formato Keep a Changelog, con sección [Unreleased] que registre
  los cambios de los subpasos 1.1 a 1.4.
No inventes contenido que no esté en el repo o en fuente-informe.md.
```

**Auditoría:**
- `matriz-roles.md` es idéntica a la Tabla 5 (compárala celda por celda).
- El diagrama coincide con `docker compose ps`.
- Cada entrada del CHANGELOG corresponde a un cambio real.

**Evidencia:** commit de la documentación.

---

## Paso 2 · Top-Down y plan de la iteración

### 2.1 Confirmar H-01 a H-06 en el código · modo **Plan**

```
Lee la tabla "Hallazgos de cohesión y acoplamiento" de docs/calidad/fuente-informe.md.
Para cada hallazgo H-01 a H-06, investiga el código y decide: Confirmado, No confirmado o
Parcial, con evidencia archivo:línea. En concreto:
- H-01: cuántos controladores validan sesión y permisos por su cuenta y cómo (lista).
- H-02 y H-03: qué hace realmente cada controlador de Profile y Settings.
- H-04: dónde se descuenta el stock al crear una venta, si está dentro de trans_start()/
  trans_complete() y si la comprobación de stock ocurre dentro de la transacción.
- H-05: estructura real de la tabla orders (columnas de cliente).
- H-06: qué contiene el módulo Company.
Entrega el resultado como tabla para docs/calidad/hallazgos-topdown.md con columnas
ID | Hallazgo del informe | Estado | Evidencia | Recomendación | Tipo de mantenimiento.
No propongas cambios de código: solo el diagnóstico.
```

**Auditoría:**
- Abre la evidencia de **H-01** y de **H-04** tú mismo: son las que más pesan en los pasos 5 y 10.
- Si un hallazgo sale "No confirmado", no es un error: es un resultado. Corrige el informe en ese punto.

Después, en modo **Normal**:
```
Guarda la tabla en docs/calidad/hallazgos-topdown.md. Luego prepara (sin ejecutar) los
comandos "gh issue create" para cada hallazgo confirmado, usando la plantilla de
mantenimiento y las etiquetas tipo:* que correspondan.
```
Revisa los comandos y ejecútalos tú (o apruébalos uno por uno).

**Evidencia:** `hallazgos-topdown.md` e issues creados.

### 2.2 Trazabilidad unificada · modo **Normal**

```
Crea docs/calidad/trazabilidad.md que resuelva el choque de IDs entre la matriz de
trazabilidad original (CP-01 a CP-06) y la tabla de casos de prueba (CP-01 a CP-12).
Usa los IDs de la tabla de casos de prueba y esta correspondencia:
RQ-01 acceso por rol → CP-04, CP-06, CP-07
RQ-02 la venta descuenta stock → CP-01, CP-02, CP-03
RQ-03 reporte anual → CP-09, CP-12
RQ-04 alta de producto → CP-08, CP-11
RQ-05 usuarios y grupos → CP-13 (nuevo: crear usuario, asignarlo a un grupo y verificar sus permisos)
RQ-06 contraseñas protegidas → CP-14 (nuevo: revisar que la contraseña se guarda con hash)
CP-05 y CP-10 quedan asociados a RQ-01.

Secciones:
1. Matriz RQ → CP con estado (Pendiente/Iniciado/Rechazado).
2. Ficha de cada caso CP-01 a CP-14: módulo, nivel, técnica, precondiciones, datos,
   pasos, resultado esperado, evidencia prevista.
3. Nota de cambios respecto al informe (qué se renumeró y por qué).

REGLA: el resultado esperado sale de la matriz de roles (Tabla 5) y de la descripción del
requisito, NUNCA del código. Si no puedes deducirlo de esas fuentes, escribe "POR DEFINIR".
```

**Auditoría:** aplica tú la lista **L4** (en `fuente-informe.md`) a las 14 fichas:
- ¿Cada requisito tiene al menos un caso?
- ¿Hay casos negativos (permiso denegado, stock insuficiente)?
- ¿Los datos cubren valores límite?
- ¿Cada resultado esperado coincide con la Tabla 5? Revisa CP-04, CP-06 y CP-07 contra la matriz.

**Evidencia:** `trazabilidad.md` y tu lista L4 marcada. Sirve como revisión parcial de RV-08.

### 2.3 Plan de pruebas de la iteración · modo **Normal**

```
Crea docs/calidad/plan-pruebas-iteracion-1.md siguiendo la estructura de la Tabla 9 de
pruebas (estrategia, recursos, cronograma, criterios de éxito y de fallo, roles, registro)
y la columna "Plan de pruebas (micro)" de la Tabla 15 de PHVA. Datos de esta iteración:
- Alcance: CP-01 a CP-14, prioridad Orders, Products y control de acceso.
- Recursos: staging Docker del paso 1, PHPUnit con ci-phpunit-test, base stock_test.
- Roles: César Roque en todos (ejecuta y revisa); declara la desviación frente al equipo
  de referencia de tres personas.
- Criterios: los de la Tabla 9 y los umbrales de la Tabla 13 de PHVA.
- Cronograma: deja columnas de fecha vacías para que yo las llene.
Incluye la regla de decisión ante retrasos (no omitir casos de Orders, stock o permisos).
```

**Auditoría:** los criterios de éxito y fallo son idénticos a los del informe; las desviaciones están declaradas.

**Evidencia:** el plan con tus fechas llenas.

---

## Cierre de la Fase 1 (criterio de salida de Planificar)

- [ ] Etiqueta `linea-base` creada antes de cualquier cambio
- [ ] El entorno levanta con PHP 7.4.33, MySQL 5.7 y pcov
- [ ] Sin credenciales en archivos versionados
- [ ] Semáforo verde y rojo demostrados
- [ ] Etiquetas, plantillas de issues y de PR en GitHub
- [ ] `hallazgos-topdown.md` con H-01 a H-06 resueltos e issues creados
- [ ] `trazabilidad.md` con CP-01 a CP-14 revisados con L4
- [ ] `plan-pruebas-iteracion-1.md` con fechas
- [ ] Bitácora con una entrada por subpaso (`/bitacora 1.0` … `/bitacora 2.3`)

Cuando todo esté marcado: abre un PR de `chore/fase1-planificar` a `main`, espera el semáforo verde, fusiónalo tú y marca los pasos 1 y 2 en `CLAUDE.md`.
