# Casos manuales (aceptación, compatibilidad y exploratoria)

Estos casos no se automatizan. Al ejecutarlos, cambia su `estado` en `calidad/registro_iteracion.json`
a `aprobado` o `fallido`; así entran en las métricas y en la matriz de trazabilidad.

## CA-01 · Usuario nuevo registra una venta sin ayuda (RQ-13, aceptación)
- **Precondición:** usuario `ventas@mype.test` (rol Ventas), stock de Café = 10.
- **Pasos:** 1) Iniciar sesión. 2) Ir a Orders → Add Order. 3) Agregar 2 Café y 1 Taza. 4) Guardar.
- **Resultado esperado:** venta guardada en menos de 3 minutos sin pedir ayuda; stock de Café = 8.
- **Evidencia:** tiempo cronometrado + captura del listado de ventas.

## CA-02 · Compatibilidad en dos navegadores (RQ-14, sistema)
- **Pasos:** repetir login → venta → imprimir comprobante en Chrome y en Firefox.
- **Resultado esperado:** mismo resultado en ambos; sin errores en la consola (F12).
- **Evidencia:** dos capturas del comprobante.

## CA-03 · Reporte anual contra hoja de cálculo (RQ-03, aceptación)
- **Precondición:** 10 ventas pagadas conocidas en 3 meses distintos.
- **Pasos:** el Gerente abre Reports del año y compara cada mes con la hoja de cálculo.
- **Resultado esperado:** totales iguales por mes (el reporte suma el monto **bruto**, sin servicio ni IVA).
- **Evidencia:** hoja de cálculo comparativa.

## EX-01 · Sesión exploratoria en Agregar/Editar venta (RQ-02, exploratoria)
- **Duración:** 30 minutos, con cronómetro.
- **Misión:** "intentar que el stock o los totales queden incorrectos".
- **Ideas:** cantidades 0, negativas o decimales; el mismo producto en dos filas; editar una venta pagada; doble clic en Guardar; dejar la pestaña abierta 2 horas y guardar.
- **Registro:** anota cada hallazgo (qué hiciste, qué pasó, qué esperabas). Cada fallo nuevo se agrega como defecto `D-xx` en el registro.
