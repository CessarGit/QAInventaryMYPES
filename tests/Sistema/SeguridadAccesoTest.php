<?php

/**
 * NIVEL: Sistema | TÉCNICA: Caja negra (peticiones HTTP como un navegador)
 *
 * No miramos el código: solo entradas (URL, rol) y salidas (página o redirección).
 * Cubre RQ-01 (acceso según rol), RQ-08 (login) y RQ-12 (entradas escapadas).
 */
class SeguridadAccesoTest extends HttpTestCase
{
    /**
     * @caso CP-08
     * @requisito RQ-08
     * @nivel sistema
     * @tecnica caja-negra
     */
    public function test_login_con_clave_incorrecta_muestra_error_y_no_entra()
    {
        $cookies = array();
        $r = $this->http('POST', 'auth/login', array('email' => 'ventas@mype.test', 'password' => 'mala'), $cookies);

        $this->assertSame(200, $r['status']);
        $this->assertNull($this->destino($r), 'No debe redirigir al dashboard');
        $this->assertStringContainsString('Incorrect username/password', $r['body']);
    }

    /**
     * Tabla de decisión derivada de la Tabla 5 (rol x módulo -> permitido/denegado).
     */
    public function matrizDeAcceso()
    {
        return array(
            'anónimo -> ventas'          => array(null, 'orders', false),
            'Ventas -> crear venta'      => array('ventas@mype.test', 'orders/create', true),
            'Ventas -> crear usuario'    => array('ventas@mype.test', 'users/create', false),
            'Ventas -> crear producto'   => array('ventas@mype.test', 'products/create', false),
            'Bodega -> ventas'           => array('bodega@mype.test', 'orders', false),
            'Bodega -> crear producto'   => array('bodega@mype.test', 'products/create', true),
            'Gerente -> reportes'        => array('gerente@mype.test', 'reports', true),
            'Administrador -> usuarios'  => array('admin@admin.com', 'users/create', true),
            'Administrador -> ventas'    => array('admin@admin.com', 'orders', false),
        );
    }

    /**
     * @dataProvider matrizDeAcceso
     * @caso CP-01
     * @requisito RQ-01
     * @nivel sistema
     * @tecnica caja-negra, tabla-decision
     */
    public function test_cada_rol_accede_solo_a_sus_modulos($correo, $ruta, $permitido)
    {
        $cookies = $correo ? $this->iniciarSesion($correo) : array();
        $r = $this->http('GET', $ruta, array(), $cookies);
        $destino = (string) $this->destino($r);

        if ($permitido) {
            $this->assertSame(200, $r['status']);
            $this->assertSame('', $destino, "El rol debería ver $ruta");
        } else {
            $this->assertMatchesRegularExpression('#(dashboard|auth/login)#', $destino, "Acceso a $ruta debía denegarse");
        }
    }

    /**
     * Defecto D-04: las rutas AJAX no revisan permisos. Bodega no tiene acceso
     * a Orders (Tabla 5), pero puede descargar todas las ventas en JSON.
     *
     * @caso CP-01
     * @requisito RQ-01
     * @nivel sistema
     * @tecnica caja-negra
     * @group critico
     */
    public function test_bodega_no_puede_descargar_las_ventas_por_ajax()
    {
        $this->db->insert('orders', array(
            'bill_no' => 'BILPR-TEST', 'customer_name' => 'Cliente secreto', 'customer_address' => '', 'customer_phone' => '',
            'date_time' => (string) time(), 'gross_amount' => '10', 'service_charge_rate' => '0', 'service_charge' => '0',
            'vat_charge_rate' => '0', 'vat_charge' => '0', 'net_amount' => '10', 'discount' => '0', 'paid_status' => 1, 'user_id' => 2,
        ));
        $cookies = $this->iniciarSesion('bodega@mype.test');

        $r = $this->http('GET', 'orders/fetchOrdersData', array(), $cookies);

        $this->assertFalse(strpos($r['body'], 'Cliente secreto') !== false, 'Bodega puede ver los datos de las ventas');
    }

    /**
     * Defecto D-04 (bis): Ventas no tiene acceso a Attributes, pero puede borrar valores.
     *
     * @caso CP-01
     * @requisito RQ-01
     * @nivel sistema
     * @tecnica caja-negra
     * @group critico
     */
    public function test_ventas_no_puede_borrar_valores_de_atributos()
    {
        $this->db->insert('attributes', array('id' => 1, 'name' => 'Tamaño', 'active' => 1));
        $this->db->insert('attribute_value', array('id' => 1, 'value' => 'Grande', 'attribute_parent_id' => 1));
        $cookies = $this->iniciarSesion('ventas@mype.test');

        $this->http('POST', 'attributes/removeValue', array('attribute_value_id' => 1), $cookies);

        $this->assertSame(1, $this->contar('attribute_value'), 'El valor fue borrado por un rol sin permiso');
    }

    /**
     * Defecto D-06: el comprobante imprime el nombre del cliente sin escapar (XSS almacenado).
     *
     * @caso CP-12
     * @requisito RQ-12
     * @nivel sistema
     * @tecnica caja-negra
     * @group critico
     */
    public function test_comprobante_escapa_datos_del_cliente()
    {
        $cookies = $this->iniciarSesion('gerente@mype.test');
        $this->http('POST', 'orders/create', $this->postVenta(array(1 => 1), array(
            'customer_name' => '<script>alert("xss")</script>',
        )), $cookies);
        $id = $this->db->query('SELECT MAX(id) AS id FROM orders')->row_array()['id'];
        $this->assertNotNull($id, 'La venta debió crearse');

        $r = $this->http('GET', "orders/printDiv/$id", array(), $cookies);

        $this->assertFalse(strpos($r['body'], '<script>alert("xss")</script>') !== false, 'El comprobante imprime HTML/JS del cliente sin escapar');
    }

    /**
     * D-07 (agregada en la Fase 4): un formulario enviado desde otro sitio,
     * sin el token CSRF, debe rechazarse aunque la víctima tenga sesión iniciada.
     *
     * @caso CP-13
     * @requisito RQ-15
     * @nivel sistema
     * @tecnica caja-negra
     * @group fase4
     */
    public function test_post_sin_token_csrf_es_rechazado()
    {
        $cookies = $this->iniciarSesion('admin@admin.com');

        $r = $this->http('POST', 'users/create', array(
            'groups' => '1', 'username' => 'atacante', 'email' => 'x@evil.test',
            'password' => 'Segura2026', 'cpassword' => 'Segura2026', 'fname' => 'X',
        ), $cookies, false);

        $this->assertSame(403, $r['status'], 'La petición sin token debe responder 403');
        $this->assertSame(0, (int) $this->db->query("SELECT COUNT(*) AS n FROM users WHERE username = 'atacante'")->row_array()['n']);
    }
}
