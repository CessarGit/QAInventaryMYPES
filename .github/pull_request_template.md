## Qué cambia
<!-- Requisito, caso o defecto: RQ-xx / CP-xx / D-xx -->

## Semáforo
- [ ] 🟢 VERDE: todas las pruebas pasan, sin advertencias y cobertura >= 60 %
- [ ] 🟡 ÁMBAR: pasan, pero hay advertencias o baja la cobertura → **completar el riesgo abajo**
- [ ] 🔴 ROJO: no se fusiona

### Riesgo aceptado (solo si es ÁMBAR)
| Advertencia | Por qué se acepta | Cuándo se corrige | Aprueba |
|---|---|---|---|
|  |  |  |  |

## Lista de verificación del revisor (ver calidad/lista_verificacion_codigo.md)
- [ ] Cada cambio de lógica tiene su prueba
- [ ] Las acciones nuevas del controlador llaman a `exigir_permiso()`
- [ ] Los datos que se imprimen usan `html_escape()`
- [ ] Varias escrituras en BD van dentro de una transacción
