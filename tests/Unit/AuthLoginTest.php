<?php

use PHPUnit\Framework\TestCase;

require_once ROOTPATH . 'tests/Support/FakeDb.php';
require_once APPPATH . 'models/Model_auth.php';

/**
 * NIVEL: Unitaria | TÉCNICA: Caja negra sobre la función, por PARTICIÓN DE EQUIVALENCIA
 *
 * Las entradas del login se dividen en clases que el sistema debe tratar igual:
 *  - clase válida:   correo existente + clave correcta  -> devuelve el usuario
 *  - clase inválida: correo existente + clave incorrecta -> rechaza
 *  - clase inválida: correo inexistente                  -> rechaza
 *  - clase inválida: campos vacíos                        -> rechaza
 * Basta con UN representante por clase.
 */
class AuthLoginTest extends TestCase
{
    public function clasesDeEquivalencia()
    {
        return array(
            'válida: credenciales correctas'  => array('ventas@mype.test', 'Clave123!', true, true),
            'inválida: clave incorrecta'      => array('ventas@mype.test', 'otraClave', true, false),
            'inválida: correo no registrado'  => array('nadie@mype.test', 'Clave123!', false, false),
            'inválida: clave vacía'           => array('ventas@mype.test', '', true, false),
        );
    }

    /**
     * @dataProvider clasesDeEquivalencia
     * @caso CP-08
     * @requisito RQ-08
     * @nivel unitaria
     * @tecnica particion-equivalencia
     */
    public function test_login_por_clases_de_equivalencia($correo, $clave, $existe, $debeEntrar)
    {
        $ci = TestCI::reset();
        $ci->db = new FakeDb();
        if ($existe) {
            $ci->db->filasConsulta = array(array(
                'id' => 3, 'email' => 'ventas@mype.test', 'username' => 'ventas',
                'password' => password_hash('Clave123!', PASSWORD_DEFAULT),
            ));
        }

        $resultado = (new Model_auth())->login($correo, $clave);

        if ($debeEntrar) {
            $this->assertIsArray($resultado);
            $this->assertSame(3, $resultado['id']);
        } else {
            $this->assertEmpty($resultado, 'El acceso debe rechazarse');
        }
    }

    /**
     * Caja blanca: el login compara contra un HASH, nunca contra texto plano.
     *
     * @caso CP-06
     * @requisito RQ-06
     * @nivel unitaria
     * @tecnica caja-blanca
     */
    public function test_login_no_acepta_contrasena_guardada_en_texto_plano()
    {
        $ci = TestCI::reset();
        $ci->db = new FakeDb();
        $ci->db->filasConsulta = array(array('id' => 9, 'email' => 'x@mype.test', 'password' => 'Clave123!'));

        $this->assertFalse((new Model_auth())->login('x@mype.test', 'Clave123!'));
    }
}
