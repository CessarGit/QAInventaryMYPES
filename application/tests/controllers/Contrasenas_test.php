<?php
require_once __DIR__.'/../_soporte/Pc0TestCase.php';

/**
 * Grupo D · Contraseñas (subpaso 5.3).
 */
class Contrasenas_test extends Pc0TestCase
{
	public function tearDown(): void
	{
		self::db()->query("DELETE ug FROM user_group ug JOIN users u ON u.id = ug.user_id WHERE u.username = 'hash_cp14'");
		self::db()->query("DELETE FROM users WHERE username = 'hash_cp14'");
		parent::tearDown();
	}

	/**
	 * CP-14 · La contraseña se guarda con hash.
	 * Esperado: almacenada con hash, no en texto plano; hash adaptativo, bcrypt o argon2
	 * con password_hash (D-04); MD5 o SHA-1 sin sal es falla.
	 */
	public function test_CP14_contrasena_guardada_con_hash_adaptativo()
	{
		$clave = 'ClaveConocida-14';
		$this->iniciarSesion('admin');
		$this->request('POST', 'users/create', [
			'groups' => '11', 'username' => 'hash_cp14', 'email' => 'hash_cp14@pc0.local',
			'password' => $clave, 'cpassword' => $clave,
			'fname' => 'Hash', 'lname' => 'CP14', 'phone' => '0', 'gender' => '1',
		]);
		$guardada = (string) $this->valor("SELECT password FROM users WHERE username = 'hash_cp14'");

		$this->assertNotSame('', $guardada, 'El usuario debe crearse');
		$this->assertNotSame($clave, $guardada, 'No debe guardarse en texto plano');
		$this->assertContains(password_get_info($guardada)['algoName'], ['bcrypt', 'argon2i', 'argon2id'], 'Hash adaptativo (D-04)');
		$this->assertTrue(password_verify($clave, $guardada));
	}
}
