<?php

/**
 * NIVEL: Sistema | TÉCNICA: Caja negra sobre flujos completos
 * Flujo principal del documento: inventario -> venta -> descuento de stock.
 */
class FlujosNegocioTest extends HttpTestCase
{
    /**
     * @caso CP-02
     * @requisito RQ-02
     * @nivel sistema
     * @tecnica caja-negra
     */
    public function test_vendedor_registra_venta_desde_la_web_y_el_stock_baja()
    {
        $cookies = $this->iniciarSesion('ventas@mype.test');

        $r = $this->http('POST', 'orders/create', $this->postVenta(array(1 => 2, 2 => 1)), $cookies);

        $this->assertStringContainsString('orders/update/', (string) $this->destino($r), 'Tras guardar, redirige a la venta');
        $orden = $this->db->query('SELECT * FROM orders')->row_array();
        $this->assertSame('3', (string) $orden['user_id'], 'La venta queda a nombre del vendedor');
        $this->assertSame(8, $this->stock(1));
        $this->assertSame(4, $this->stock(2));
    }

    /**
     * @caso CP-05
     * @requisito RQ-05
     * @nivel sistema
     * @tecnica caja-negra
     */
    public function test_administrador_crea_usuario_y_este_puede_iniciar_sesion()
    {
        $admin = $this->iniciarSesion('admin@admin.com');

        $this->http('POST', 'users/create', array(
            'groups' => '3', 'username' => 'cajero2', 'email' => 'cajero2@mype.test',
            'password' => 'Segura2026', 'cpassword' => 'Segura2026', 'fname' => 'Luis', 'lname' => 'Pérez',
            'phone' => '7000-1111', 'gender' => '1',
        ), $admin);

        $fila = $this->db->query("SELECT password FROM users WHERE username = 'cajero2'")->row_array();
        $this->assertNotNull($fila, 'El usuario debió crearse');
        $this->assertStringStartsNotWith('Segura2026', $fila['password'], 'CP-06: nunca en texto plano');
        $this->assertTrue(password_verify('Segura2026', $fila['password']));

        $this->iniciarSesion('cajero2@mype.test', 'Segura2026');
    }
}
