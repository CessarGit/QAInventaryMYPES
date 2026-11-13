<?php

/**
 * NIVEL: Integración | TÉCNICA: Caja gris
 * Alta de producto (CP-04), usuario + grupo (CP-05) y hash de contraseñas (CP-06).
 */
class CatalogoYUsuariosTest extends DatabaseTestCase
{
    /**
     * @caso CP-04
     * @requisito RQ-04
     * @nivel integracion
     * @tecnica caja-gris
     */
    public function test_producto_creado_con_marca_categoria_y_tienda_aparece_en_listado_de_venta()
    {
        $ci = get_instance();
        $ci->load->model('model_products');

        $ok = $ci->model_products->create(array(
            'name' => 'Pan dulce', 'sku' => 'PAN-01', 'price' => '0.50', 'qty' => '40', 'image' => '',
            'description' => '', 'attribute_value_id' => serialize(array()),
            'brand_id' => serialize(array('1')), 'category_id' => serialize(array('1')),
            'store_id' => 1, 'availability' => 1,
        ));

        $this->assertTrue($ok);
        $nombres = array_column($ci->model_products->getActiveProductData(), 'name');
        $this->assertContains('Pan dulce', $nombres, 'El producto nuevo debe poder venderse');
        $this->assertNotContains('Producto inactivo', $nombres, 'Los inactivos no se ofrecen en la venta');
    }

    /**
     * @caso CP-05
     * @requisito RQ-05
     * @nivel integracion
     * @tecnica caja-gris
     */
    public function test_usuario_asignado_a_grupo_ventas_recibe_solo_sus_permisos()
    {
        $ci = get_instance();
        $ci->load->model('model_users');
        $ci->load->model('model_groups');

        $this->assertTrue($ci->model_users->create(array(
            'username' => 'cajero1', 'password' => password_hash('Clave123!', PASSWORD_DEFAULT),
            'email' => 'cajero1@mype.test', 'firstname' => 'Ana', 'lastname' => 'López', 'phone' => '', 'gender' => 2,
        ), 3)); // 3 = Ventas

        $id = $this->db->query("SELECT id FROM users WHERE username = 'cajero1'")->row_array()['id'];
        $permisos = unserialize($ci->model_groups->getUserGroupByUserId($id)['permission']);

        $this->assertContains('createOrder', $permisos);
        $this->assertContains('viewProduct', $permisos);
        $this->assertNotContains('createProduct', $permisos, 'Ventas solo LEE productos (Tabla 5)');
        $this->assertNotContains('createUser', $permisos);
    }

    /**
     * Revisa TODA la tabla users: ninguna contraseña en texto plano.
     *
     * @caso CP-06
     * @requisito RQ-06
     * @nivel integracion
     * @tecnica estatica-datos
     */
    public function test_todas_las_contrasenas_estan_guardadas_con_hash()
    {
        $usuarios = $this->db->query('SELECT username, password FROM users')->result_array();
        $this->assertNotEmpty($usuarios);
        foreach ($usuarios as $u) {
            $info = password_get_info($u['password']);
            $this->assertNotSame(0, $info['algo'] ?: 0, "La clave de {$u['username']} no es un hash");
        }
    }
}
