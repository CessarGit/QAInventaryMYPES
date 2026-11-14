<?php
/**
 * Base de las pruebas con PC-0 (regla 16). Solo opera sobre stock_test (regla 14).
 * Cada prueba deja la base como la encontró: stock del producto PC-0 en N = 5 (D-01)
 * y sin las ventas que haya creado (independencia, L4-6).
 *
 * Sesión: en CLI CodeIgniter no inicia la sesión y CI_Session lee $_SESSION, así que se
 * simula escribiendo $_SESSION con las mismas claves que Auth::login
 * (application/controllers/Auth.php:36-41). Esto omite el formulario de inicio de sesión.
 */
abstract class Pc0TestCase extends TestCase
{
	const PRODUCTO = 10;
	const N = 5;
	const USUARIOS = [
		'gerente' => 10, 'ventas' => 11, 'bodega' => 12, 'logistica' => 13, 'admin' => 14,
	];

	private static $db;
	private $max_venta;

	protected static function db()
	{
		if (getenv('DB_NAME') !== 'stock_test') {
			throw new RuntimeException('Las pruebas solo usan stock_test (regla 14).');
		}
		if (! self::$db) {
			self::$db = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), 'stock_test');
		}
		return self::$db;
	}

	protected function valor($sql)
	{
		$fila = self::db()->query($sql)->fetch_row();
		return $fila ? $fila[0] : null;
	}

	protected function stock()
	{
		return (int) $this->valor('SELECT qty FROM products WHERE id = '.self::PRODUCTO);
	}

	protected function ventas()
	{
		return (int) $this->valor('SELECT COUNT(*) FROM orders');
	}

	protected function iniciarSesion($rol)
	{
		$_SESSION = [
			'id' => self::USUARIOS[$rol], 'username' => $rol.'_test',
			'email' => $rol.'_test@pc0.local', 'logged_in' => TRUE,
		];
	}

	protected function cerrarSesion()
	{
		$_SESSION = [];
	}

	/** Datos del formulario de venta (application/views/orders/create.php) para el producto PC-0. */
	protected function datosVenta($cantidad)
	{
		$importe = 10 * (int) $cantidad;
		return [
			'customer_name' => 'Cliente PC0', 'customer_address' => 'Dirección', 'customer_phone' => '0',
			'product' => [self::PRODUCTO], 'qty' => [$cantidad],
			'rate_value' => ['10'], 'amount_value' => [(string) $importe],
			'gross_amount_value' => (string) $importe, 'net_amount_value' => (string) $importe,
			'service_charge_rate' => '0', 'service_charge_value' => '0',
			'vat_charge_rate' => '0', 'vat_charge_value' => '0', 'discount' => '0',
		];
	}

	/** Redirección a otra página: acceso denegado (no se muestra ni ejecuta lo pedido). */
	protected function assertAccesoDenegado($uri)
	{
		$estado = $this->request->getStatus();
		$this->assertNotNull($estado['redirect'], "Se esperaba acceso denegado en '$uri'");
		$this->assertStringNotContainsString($uri, (string) $estado['redirect']);
	}

	/** Página servida sin redirección: acceso permitido. */
	protected function assertAccesoPermitido($uri)
	{
		$estado = $this->request->getStatus();
		$this->assertNull($estado['redirect'], "Se esperaba acceso permitido en '$uri'");
		$this->assertResponseCode(200);
	}

	/** Venta registrada directamente en la base (precondición "al menos una venta"). */
	protected function insertarVenta($cantidad = 1)
	{
		$db = self::db();
		$db->query("INSERT INTO orders (bill_no, customer_name, customer_address, customer_phone, date_time,
			gross_amount, service_charge_rate, service_charge, vat_charge_rate, vat_charge, net_amount,
			discount, paid_status, user_id) VALUES ('PC0-TEST', 'Cliente PC0', 'Dirección', '0',
			UNIX_TIMESTAMP(), '10', '0', '0', '0', '0', '10', '0', 2, 11)");
		$id = $db->insert_id;
		$db->query("INSERT INTO orders_item (order_id, product_id, qty, rate, amount)
			VALUES ($id, ".self::PRODUCTO.", '$cantidad', '10', '10')");
		return $id;
	}

	public function setUp(): void
	{
		parent::setUp();
		self::db()->query("UPDATE products SET qty = '".self::N."' WHERE id = ".self::PRODUCTO);
		$this->max_venta = (int) $this->valor('SELECT COALESCE(MAX(id), 0) FROM orders');
		$this->cerrarSesion();
	}

	public function tearDown(): void
	{
		self::db()->query('DELETE FROM orders_item WHERE order_id > '.$this->max_venta);
		self::db()->query('DELETE FROM orders WHERE id > '.$this->max_venta);
		self::db()->query("UPDATE products SET qty = '".self::N."' WHERE id = ".self::PRODUCTO);
		$this->cerrarSesion();
		parent::tearDown();
	}
}
