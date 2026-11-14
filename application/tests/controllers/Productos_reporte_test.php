<?php
require_once __DIR__.'/../_soporte/Pc0TestCase.php';

/**
 * Grupo C · Productos y reporte (subpaso 5.3). Resultados esperados de trazabilidad.md
 * con las decisiones D-02, D-03 y D-05. Sesión de gerente_test.
 */
class Productos_reporte_test extends Pc0TestCase
{
	public function tearDown(): void
	{
		self::db()->query('DELETE FROM products WHERE id > '.self::PRODUCTO);
		parent::tearDown();
	}

	private function productos()
	{
		return (int) $this->valor('SELECT COUNT(*) FROM products');
	}

	private function datosProducto($cambios = [])
	{
		return array_merge([
			'product_name' => 'PRODUCTO-C', 'sku' => 'C-001', 'price' => '10', 'qty' => '3',
			'description' => '', 'brands' => ['4'], 'category' => ['4'], 'store' => '10',
			'availability' => '1',
		], $cambios);
	}

	/**
	 * CP-08 · Alta de producto con campos obligatorios vacíos.
	 * Campos obligatorios (D-02): nombre, SKU, precio, cantidad, marca, categoría y tienda.
	 * Esperado: rechazo con mensaje de validación.
	 *
	 * @dataProvider camposVacios
	 */
	public function test_CP08_alta_con_campos_obligatorios_vacios($cambios)
	{
		$this->comprobarRechazoPorValidacion($cambios);
	}

	public function camposVacios()
	{
		$todos = ['product_name' => '', 'sku' => '', 'price' => '', 'qty' => '', 'brands' => [], 'category' => [], 'store' => ''];
		$casos = ['todos' => [$todos]];
		foreach (['product_name', 'sku', 'price', 'qty', 'store'] as $campo) {
			$casos[$campo] = [[$campo => $todos[$campo]]];
		}
		return $casos;
	}

	/**
	 * CP-08 (marca y categoría, obligatorias según D-02). Mismo resultado esperado:
	 * rechazo con mensaje de validación.
	 *
	 * @dataProvider marcaCategoriaVacias
	 * @group defecto-abierto
	 * Defecto abierto: #38.
	 */
	public function test_CP08_alta_sin_marca_o_categoria($cambios)
	{
		$this->comprobarRechazoPorValidacion($cambios);
	}

	public function marcaCategoriaVacias()
	{
		return ['brands' => [['brands' => []]], 'category' => [['category' => []]]];
	}

	private function comprobarRechazoPorValidacion($cambios)
	{
		$this->iniciarSesion('gerente');
		$antes = $this->productos();
		$salida = $this->request('POST', 'products/create', $this->datosProducto($cambios));

		$this->assertSame($antes, $this->productos(), 'El producto no debe crearse');
		$this->assertNull($this->request->getStatus()['redirect'], 'Debe volver al formulario');
		$this->assertStringContainsString('required', (string) $salida, 'Debe mostrar el mensaje de validación');
	}

	/**
	 * CP-09 · Una venta se refleja en el reporte anual.
	 * Importe que suma el reporte: neto, con cargos e IVA (D-03).
	 * Esperado: la venta se refleja en los totales del año.
	 *
	 * @group defecto-abierto
	 * Defecto abierto: #37.
	 */
	public function test_CP09_venta_se_refleja_en_el_reporte_anual()
	{
		$this->iniciarSesion('gerente');
		$antes = $this->totalAnual();

		$venta = array_merge($this->datosVenta('5'), [
			'gross_amount_value' => '50', 'vat_charge_rate' => '13', 'vat_charge_value' => '6.5',
			'net_amount_value' => '56.5',
		]);
		$this->request('POST', 'orders/create', $venta);

		$this->assertEqualsWithDelta($antes + 56.5, $this->totalAnual(), 0.001, 'El total anual debe sumar el neto de la venta');
	}

	private function totalAnual()
	{
		$salida = $this->request('POST', 'reports', ['select_year' => date('Y')]);
		$this->assertSame(1, preg_match('/var report_data = \[([^\]]*)\]/', (string) $salida, $m), 'No se encontró el total del reporte');
		return array_sum(array_map('floatval', array_filter(explode(',', $m[1]), 'strlen')));
	}

	/**
	 * CP-20 · Nombre de producto más largo que el límite (256 caracteres; límite 255, D-05).
	 * Esperado: rechazo con mensaje de validación, o guardado truncado a 255 caracteres sin
	 * error de la base.
	 */
	public function test_CP20_nombre_mas_largo_que_el_limite()
	{
		$this->iniciarSesion('gerente');
		$antes = $this->productos();
		$salida = $this->request('POST', 'products/create', $this->datosProducto(['product_name' => str_repeat('a', 256)]));

		$this->assertNotSame(500, $this->request->getStatus()['code'], 'No debe haber error de la base');
		if ($this->productos() === $antes) {
			$this->assertStringContainsString('characters', (string) $salida, 'Rechazo con mensaje de validación');
		} else {
			$this->assertSame('255', $this->valor('SELECT LENGTH(name) FROM products WHERE id > '.self::PRODUCTO.' ORDER BY id DESC LIMIT 1'), 'Truncado a 255');
		}
	}
}
