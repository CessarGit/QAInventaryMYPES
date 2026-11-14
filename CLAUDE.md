# Iteración de calidad 1: sistema de inventarios y punto de venta para MYPES

## Qué es este repositorio y qué estamos haciendo

- Aplicación web **ya desarrollada**: CodeIgniter 3.1.6, PHP 7.4.33, MySQL 5.7 (base `stock`, 12 tablas InnoDB), Apache 2.4, monolito MVC con 12 módulos y 5 roles. Staging en Docker.
- **No se desarrolla funcionalidad nueva** (exclusión del alcance del informe). Se le aplica la gestión de calidad diseñada en el informe *Gestión de la calidad de un sistema web de inventarios y punto de venta para MYPES*, mediante una iteración PHVA de 10 pasos.
- La referencia de diseño está en `docs/calidad/fuente-informe.md` y en `docs/calidad/fuente-informe-2.md` (tablas para los pasos 2.3 a 10). Léelas antes de proponer cualquier cosa. La **matriz de roles (Tabla 5)** es la única fuente de verdad sobre permisos.
- El autor (César Roque) trabaja solo y **audita cada cambio**. Tú produces; él revisa y aprueba.

## Reglas de trabajo (obligatorias)

1. **Nada de funcionalidades nuevas.** Solo configuración, pruebas, documentación y los cambios que el plan aprueba.
2. **No tocar `system/`**, salvo en el paso 6 y dentro de su propia rama.
3. **Plan antes de editar.** Antes de modificar archivos, presenta qué vas a cambiar y por qué, y espera la aprobación. Un tema por cambio.
4. **Sin acciones irreversibles ni externas sin permiso explícito en el chat:** commit, push, merge, crear issues, etiquetas o PR en GitHub, `docker compose down -v`, `DROP`, `rm -rf`, `git reset --hard`, reescribir el historial.
5. **Sin secretos en archivos versionados.** Credenciales en `.env` (ignorado por git); en el repositorio solo `.env.example` sin valores reales. No leas ni muestres el contenido de `.env`.
6. **Los resultados esperados de las pruebas salen de la matriz de roles y de los requisitos, nunca del código.** Si el código y la matriz difieren, eso es un hallazgo, no se "ajusta" la prueba.
7. **Toda afirmación sobre el código lleva evidencia `ruta/archivo.php:línea`.** Si algo no se puede confirmar, dilo de forma explícita.
8. **Cita la tabla del informe** que respalda cada acción (por ejemplo, "Tabla 18, semáforo").
9. Idioma: español. Commits en formato Conventional Commits en español, por ejemplo `chore(docker): agrega pcov para cobertura`.
10. **Al cerrar cada subpaso**, entrega: (a) resumen de archivos cambiados, (b) comandos para que el autor lo verifique, (c) la evidencia que debe guardar y (d) cualquier desviación respecto al diseño.

## Ramas y versiones

- `main`: estable y protegida. El trabajo va en ramas `chore/...`, `test/...`, `docs/...`, `fix/...`.
- La etiqueta `linea-base` marca el estado del sistema antes de la iteración.

## Documentación

```
docs/
  README.md                  índice
  calidad/                   fuente-informe.md, fuente-informe-2.md, matriz-roles.md,
                             trazabilidad.md, hallazgos-topdown.md,
                             plan-pruebas-iteracion-1.md, decisiones-autor.md,
                             registro-ejecucion.md
    plantillas/              plantilla-revision.md
    revisiones/              RV-xx (una por revisión) y README.md (indicadores)
  ejecucion/                 bitacora.md, plantilla-paso.md, evidencias/
  arquitectura/              diagramas en Mermaid
CHANGELOG.md                 formato Keep a Changelog
```

## Referencias de diseño

- `docs/calidad/fuente-informe.md` (parte 1) y `docs/calidad/fuente-informe-2.md` (parte 2).
- Hallazgos ya confirmados: `docs/calidad/hallazgos-topdown.md` (H-01 a H-06, A-01, A-04).
- Casos y resultados esperados: `docs/calidad/trazabilidad.md`. Decisiones del autor: `docs/calidad/decisiones-autor.md`.

## Reglas de la Fase 2 (Hacer)

11. **Una prueba nunca se modifica para que pase**, y el código nunca se cambia para "acomodar" una prueba fuera de una corrección aprobada (rama `fix/...`).
12. **Si una prueba falla por un defecto real:** se marca con `@group defecto-abierto` y el número de issue en el docblock, se registra el defecto con la plantilla *Defecto* y se deja tal cual. Nunca se borra ni se salta en silencio.
13. **Solo se corrigen defectos registrados de severidad crítica o alta**, con el cambio mínimo, en su propia rama `fix/...` y sin funcionalidad nueva (mantenimiento correctivo, Tabla 11 de mantenimiento).
14. **Las pruebas automatizadas usan solo la base `stock_test`.** Nunca escriben en `stock`.
15. **Los respaldos van en `backups/`, que está ignorado por git.** Nunca se versiona un dump ni se muestra su contenido.
16. **Los datos de prueba (PC-0) se derivan de la matriz de roles**, a través del mapeo de permisos de `docs/calidad/revisiones/RV-01-matriz.md`.
17. **Medición honesta:** si un resultado no alcanza el umbral, se reporta tal cual. No se redondea ni se reinterpreta.

## Estado de la iteración (el autor lo actualiza)

- [ ] Paso 1 · Preparar entorno (Docker, secretos, GitHub, CI, docs)
- [ ] Paso 2 · Top-Down y plan (H-01 a H-06, trazabilidad unificada, plan de pruebas)
- [ ] Paso 3 · Revisiones (L1 Orders, L2 matriz)
- [ ] Paso 4 · Respaldo y restauración
- [ ] Paso 5 · Pruebas y seguridad sobre 3.1.6
- [ ] Paso 6 · Actualizar a CodeIgniter 3.1.13
- [ ] Paso 7 · Reversión RB-06 (solo si hay regresión)
- [ ] Paso 8 · Medir
- [ ] Paso 9 · Autoverificación
- [ ] Paso 10 · Actuar y estandarizar
