<?php
require_once __DIR__.'/../_soporte/Pc0TestCase.php';

/**
 * Grupo B · Permisos (subpaso 5.3). Resultados esperados copiados de trazabilidad.md
 * (Tabla 5 corregida; Tabla 13 de seguridad). Peticiones con la sesión de cada usuario PC-0.
 */
class Permisos_test extends Pc0TestCase
{
	private $usuario_guardado = [];

	public function setUp(): void
	{
		parent::setUp();
		foreach ([11, 12] as $id) {
			$this->usuario_guardado[$id] = [
				$this->valor("SELECT password FROM users WHERE id = $id"),
				$this->valor("SELECT group_id FROM user_group WHERE user_id = $id"),
			];
		}
	}

	public function tearDown(): void
	{
		foreach ($this->usuario_guardado as $id => list($hash, $grupo)) {
			$h = self::db()->real_escape_string($hash);
			self::db()->query("UPDATE users SET password = '$h', username = IF(id = 11, 'ventas_test', username) WHERE id = $id");
			self::db()->query("UPDATE user_group SET group_id = $grupo WHERE user_id = $id");
		}
		self::db()->query("DELETE ug FROM user_group ug JOIN users u ON u.id = ug.user_id WHERE u.username = 'nuevo_cp13'");
		self::db()->query("DELETE FROM users WHERE username = 'nuevo_cp13'");
		parent::tearDown();
	}

	private function datosSetting($extra = [])
	{
		return array_merge([
			'username' => 'ventas_test', 'email' => 'ventas_test@pc0.local', 'fname' => 'Ventas',
			'lname' => 'PC0', 'phone' => '00000000', 'gender' => '1',
			'password' => 'NuevaClave-123', 'cpassword' => 'NuevaClave-123',
		], $extra);
	}

	/**
	 * CP-04 · Ventas intenta eliminar una venta.
	 * Esperado: acceso denegado (Tabla 5: Ventas tiene CR en Orders).
	 */
	public function test_CP04_ventas_intenta_eliminar_una_venta()
	{
		$venta = $this->insertarVenta();
		$this->iniciarSesion('ventas');
		$this->request('POST', 'orders/remove', ['order_id' => $venta]);

		$this->assertAccesoDenegado('orders/remove');
		$this->assertSame('1', $this->valor("SELECT COUNT(*) FROM orders WHERE id = $venta"));
	}

	/**
	 * CP-05 · Gerente edita una venta.
	 * Esperado: permitido (Tabla 5: Gerente tiene CRUD en Orders).
	 */
	public function test_CP05_gerente_edita_una_venta()
	{
		$venta = $this->insertarVenta();
		$this->iniciarSesion('gerente');
		$datos = array_merge($this->datosVenta('2'), ['customer_name' => 'Cliente editado', 'paid_status' => '2']);
		$this->request('POST', "orders/update/$venta", $datos);

		$this->assertSame('Cliente editado', $this->valor("SELECT customer_name FROM orders WHERE id = $venta"));
	}

	/**
	 * CP-06 · Bodega intenta abrir Orders.
	 * Esperado: acceso denegado (Tabla 5: Bodega sin acceso a Orders).
	 */
	public function test_CP06_bodega_intenta_abrir_orders()
	{
		$this->iniciarSesion('bodega');
		$this->request('GET', 'orders');

		$this->assertAccesoDenegado('orders');
	}

	/**
	 * CP-07 · Administrador y Gerente abren Users.
	 * Esperado: permitido al Administrador; denegado al Gerente.
	 */
	public function test_CP07_administrador_abre_users()
	{
		$this->iniciarSesion('admin');
		$this->request('GET', 'users');

		$this->assertAccesoPermitido('users');
	}

	/** CP-07 (segunda parte): denegado al Gerente. */
	public function test_CP07_gerente_intenta_abrir_users()
	{
		$this->iniciarSesion('gerente');
		$this->request('GET', 'users');

		$this->assertAccesoDenegado('users');
	}

	/**
	 * CP-10 · Un usuario cambia su contraseña e intenta cambiar la de otro.
	 * Esperado: solo se permite la propia (Tabla 5: Settings RU, propia).
	 */
	public function test_CP10_cambia_su_contrasena_y_no_la_de_otro()
	{
		$propia_antes = $this->valor('SELECT password FROM users WHERE id = 11');
		$ajena_antes = $this->valor('SELECT password FROM users WHERE id = 12');
		$this->iniciarSesion('ventas');

		$this->request('POST', 'users/setting', $this->datosSetting());
		$this->assertNotSame($propia_antes, $this->valor('SELECT password FROM users WHERE id = 11'), 'La propia debe cambiar');

		$this->request('POST', 'users/edit/12', array_merge($this->datosSetting(), ['username' => 'bodega_test', 'email' => 'bodega_test@pc0.local']));
		$this->assertSame($ajena_antes, $this->valor('SELECT password FROM users WHERE id = 12'), 'La de otro no debe cambiar');
	}

	/**
	 * CP-13 · Crear un usuario, asignarlo al grupo Ventas y verificar sus permisos.
	 * Esperado: fila Ventas de la Tabla 5: Settings RU (propia); Products R; Orders CR;
	 * Reports R; Profile R; sin acceso a Users, Groups, Brands, Categories, Attributes,
	 * Stores ni Company.
	 */
	public function test_CP13_usuario_nuevo_del_grupo_ventas()
	{
		$this->iniciarSesion('admin');
		$this->request('POST', 'users/create', [
			'groups' => '11', 'username' => 'nuevo_cp13', 'email' => 'nuevo_cp13@pc0.local',
			'password' => 'ClaveCP13-123', 'cpassword' => 'ClaveCP13-123',
			'fname' => 'Nuevo', 'lname' => 'CP13', 'phone' => '0', 'gender' => '1',
		]);
		$id = (int) $this->valor("SELECT id FROM users WHERE username = 'nuevo_cp13'");
		$this->assertGreaterThan(0, $id, 'El usuario debe crearse');
		$this->assertSame('11', $this->valor("SELECT group_id FROM user_group WHERE user_id = $id"));

		$_SESSION = ['id' => $id, 'username' => 'nuevo_cp13', 'email' => 'nuevo_cp13@pc0.local', 'logged_in' => TRUE];
		foreach (['users/setting', 'products', 'orders', 'orders/create', 'reports', 'users/profile'] as $uri) {
			$this->request('GET', $uri);
			$this->assertAccesoPermitido($uri);
		}
		foreach (['users', 'groups', 'brands', 'category', 'attributes', 'stores', 'company'] as $uri) {
			$this->request('GET', $uri);
			$this->assertAccesoDenegado($uri);
		}
	}

	/**
	 * CP-15 · Ventas intenta cambiar su grupo desde Settings (campo groups añadido a mano).
	 * Esperado: el grupo de ventas_test no cambia (Tabla 5). Origen: A-01.
	 * Defecto corregido: #34.
	 */
	public function test_CP15_ventas_intenta_cambiar_su_grupo_desde_settings()
	{
		$this->iniciarSesion('ventas');
		$this->request('POST', 'users/setting', $this->datosSetting(['groups' => '14']));

		$this->assertSame('11', $this->valor('SELECT group_id FROM user_group WHERE user_id = 11'));
	}

	/**
	 * CP-16 · Acceso sin sesión a Reports y a métodos sin comprobación de permiso.
	 * Esperado: redirección al inicio de sesión o acceso denegado, sin devolver datos ni
	 * ejecutar la operación. Origen: H-01.
	 *
	 * @dataProvider urlsSinSesion
	 */
	public function test_CP16_acceso_sin_sesion($metodo, $uri, $datos)
	{
		$valores_antes = $this->valor('SELECT COUNT(*) FROM attribute_value');
		$this->request($metodo, $uri, $datos);

		$this->assertAccesoDenegado($uri);
		$this->assertSame($valores_antes, $this->valor('SELECT COUNT(*) FROM attribute_value'));
	}

	public function urlsSinSesion()
	{
		return [
			'reports' => ['GET', 'reports', []],
			'orders/fetchOrdersData' => ['GET', 'orders/fetchOrdersData', []],
			'products/upload_image' => ['GET', 'products/upload_image', []],
			'attributes/createValue' => ['POST', 'attributes/createValue', ['attribute_value_name' => 'CP16', 'attribute_parent_id' => '1']],
		];
	}
}
