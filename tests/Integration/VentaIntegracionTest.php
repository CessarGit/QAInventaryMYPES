<?php

/**
 * NIVEL: Integración | TÉCNICA: Caja gris (modelo + driver de CodeIgniter + MySQL real)
 *
 * Aquí ya NO hay dobles: Model_orders, Model_products y la base `stock_test`
 * trabajan juntos. Se verifica lo que realmente queda guardado en las tablas.
 *
 * Datos sembrados (ver DatabaseTestCase): Café (id 1) stock 10 a $5.00,
 * Taza (id 2) stock 5 a $3.50. Servicio 13 %, IVA 10 %.
 */
class VentaIntegracionTest extends DatabaseTestCase
{
    private function vender(array $lineas, array $extra = array())
    {
        $ci = get_instance();
        $ci->input->post = $this->postVenta($lineas, $extra);
        $ci->load->model('model_orders');
        return $ci->model_orders->create();
    }

    /**
     * @caso CP-02
     * @requisito RQ-02
     * @nivel integracion
     * @tecnica caja-gris
     */
    public function test_crear_venta_guarda_orden_items_y_descuenta_stock()
    {
        $orden = $this->vender(array(1 => 3, 2 => 2));

        $this->assertNotFalse($orden);
        $this->assertSame(1, $this->contar('orders'));
        $this->assertSame(2, $this->contar('orders_item'));
        $this->assertSame(7, $this->stock(1), 'Café: 10 - 3');
        $this->assertSame(3, $this->stock(2), 'Taza: 5 - 2');
    }

    /**
     * Editar una venta devuelve el stock anterior y descuenta el nuevo.
     *
     * @caso CP-02
     * @requisito RQ-02
     * @nivel integracion
     * @tecnica caja-gris
     */
    public function test_editar_venta_devuelve_y_vuelve_a_descontar_stock()
    {
        $orden = $this->vender(array(1 => 3));
        $this->assertSame(7, $this->stock(1));

        $ci = get_instance();
        $ci->input->post = $this->postVenta(array(1 => 1), array('paid_status' => '1'));
        $this->assertTrue($ci->model_orders->update($orden));

        $this->assertSame(9, $this->stock(1), 'Se devuelven 3 y se descuenta 1: 10 - 1');
        $this->assertSame(1, $this->contar('orders_item'));
    }

    /**
     * Defecto D-02: al eliminar una venta el stock NO regresa a bodega.
     *
     * @caso CP-10
     * @requisito RQ-10
     * @nivel integracion
     * @tecnica caja-gris
     * @group critico
     */
    public function test_eliminar_venta_devuelve_el_stock()
    {
        $orden = $this->vender(array(1 => 4));
        $this->assertSame(6, $this->stock(1));

        $this->assertTrue(get_instance()->model_orders->remove($orden));

        $this->assertSame(0, $this->contar('orders'));
        $this->assertSame(10, $this->stock(1), 'Al anular la venta, los 4 cafés deben volver al inventario');
    }

    /**
     * Defecto D-05: sin transacción, una venta con un producto inexistente
     * deja registros a medias (Fiabilidad, ISO/IEC 25010).
     *
     * @caso CP-11
     * @requisito RQ-11
     * @nivel integracion
     * @tecnica caja-gris
     * @group critico
     */
    public function test_venta_con_producto_inexistente_no_deja_registros_a_medias()
    {
        $resultado = $this->vender(array(1 => 2, 999 => 1));

        $this->assertFalse($resultado, 'La venta debe rechazarse completa');
        $this->assertSame(0, $this->contar('orders'), 'No debe quedar la orden');
        $this->assertSame(0, $this->contar('orders_item'), 'No deben quedar ítems');
        $this->assertSame(10, $this->stock(1), 'El stock del café no debe cambiar');
    }

    /**
     * Defecto D-03: el servidor guarda el total que manda el navegador.
     * Un usuario puede editar el HTML y pagar $0.01 por una venta de $15.
     *
     * @caso CP-09
     * @requisito RQ-09
     * @nivel integracion
     * @tecnica caja-gris
     * @group critico
     */
    public function test_servidor_recalcula_totales_e_ignora_valores_manipulados()
    {
        $orden = $this->vender(array(1 => 3), array(      // 3 x $5.00 = $15.00
            'gross_amount_value' => '0.01',
            'net_amount_value'   => '0.01',
            'rate_value'         => array('0.01'),
            'amount_value'       => array('0.01'),
        ));

        $fila = $this->db->query('SELECT * FROM orders WHERE id = ?', array($orden))->row_array();
        $this->assertEquals(15.00, (float) $fila['gross_amount'], 'Bruto = 3 x 5.00');
        $this->assertEquals(1.95, (float) $fila['service_charge'], 'Servicio 13 %');
        $this->assertEquals(1.50, (float) $fila['vat_charge'], 'IVA 10 %');
        $this->assertEquals(18.45, (float) $fila['net_amount'], 'Neto = 15 + 1.95 + 1.50');
    }
}
