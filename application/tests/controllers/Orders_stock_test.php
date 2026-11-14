<?php
require_once __DIR__.'/../_soporte/Pc0TestCase.php';

/**
 * Grupo A · Stock (subpaso 5.3). Resultados esperados copiados de trazabilidad.md.
 * Sesión de ventas_test (Tabla 5: Orders CR).
 */
class Orders_stock_test extends Pc0TestCase
{
	/**
	 * CP-01 · Vender una cantidad igual al stock (N = 5, D-01).
	 * Esperado: venta registrada y stock en 0.
	 */
	public function test_CP01_vender_cantidad_igual_al_stock()
	{
		$this->iniciarSesion('ventas');
		$antes = $this->ventas();
		$this->request('POST', 'orders/create', $this->datosVenta(self::N));

		$this->assertSame($antes + 1, $this->ventas(), 'La venta debe registrarse');
		$this->assertSame(0, $this->stock(), 'El stock debe quedar en 0');
	}

	/**
	 * CP-02 · Vender una cantidad mayor que el stock (N + 1 = 6).
	 * Esperado: rechazo; el stock no cambia.
	 *
	 * @group defecto-abierto
	 * Defecto abierto: #35.
	 */
	public function test_CP02_vender_cantidad_mayor_que_el_stock()
	{
		$this->iniciarSesion('ventas');
		$antes = $this->ventas();
		$this->request('POST', 'orders/create', $this->datosVenta(self::N + 1));

		$this->assertSame($antes, $this->ventas(), 'La venta debe rechazarse');
		$this->assertSame(self::N, $this->stock(), 'El stock no debe cambiar');
	}

	/**
	 * CP-03 · Cantidad igual a 0 o negativa.
	 * Esperado: rechazo por validación en ambos casos.
	 *
	 * @dataProvider cantidadesNoValidas
	 *
	 * @group defecto-abierto
	 * Defecto abierto: #36.
	 */
	public function test_CP03_cantidad_cero_o_negativa($cantidad)
	{
		$this->iniciarSesion('ventas');
		$antes = $this->ventas();
		$this->request('POST', 'orders/create', $this->datosVenta($cantidad));

		$this->assertSame($antes, $this->ventas(), "La venta con cantidad $cantidad debe rechazarse");
		$this->assertSame(self::N, $this->stock(), 'El stock no debe cambiar');
	}

	public function cantidadesNoValidas()
	{
		return ['cero' => ['0'], 'negativa' => ['-1']];
	}

	/**
	 * CP-19 · Vender una unidad (valor límite).
	 * Esperado: venta registrada y stock 4 (N − 1).
	 */
	public function test_CP19_vender_una_unidad()
	{
		$this->iniciarSesion('ventas');
		$antes = $this->ventas();
		$this->request('POST', 'orders/create', $this->datosVenta('1'));

		$this->assertSame($antes + 1, $this->ventas(), 'La venta debe registrarse');
		$this->assertSame(self::N - 1, $this->stock(), 'El stock debe quedar en N − 1');
	}
}
