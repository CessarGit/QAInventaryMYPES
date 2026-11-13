<?php

use PHPUnit\Framework\TestCase;

require_once ROOTPATH . 'tests/Support/FakeDb.php';
require_once APPPATH . 'models/Model_products.php';
require_once APPPATH . 'models/Model_company.php';
require_once APPPATH . 'models/Model_orders.php';

/**
 * NIVEL: Unitaria | TÉCNICA: Caja blanca con dobles de prueba (mocks)
 *
 * Se prueba SOLO Model_orders::create(). La base de datos y el modelo de
 * productos se reemplazan por dobles, así la prueba es rápida y no depende de MySQL.
 */
class VentaStockTest extends TestCase
{
    /** @var FakeDb */
    private $db;
    private $productos;
    private $ci;

    protected function setUp(): void
    {
        $this->ci = TestCI::reset();
        $this->db = new FakeDb();
        $this->ci->db = $this->db;

        // Doble del modelo de productos: stock conocido para cada producto.
        $this->productos = $this->createMock(Model_products::class);
        $this->productos->method('getProductData')->willReturnMap(array(
            array(1, array('id' => 1, 'name' => 'Café molido', 'price' => '5.00', 'qty' => '10')),
            array(2, array('id' => 2, 'name' => 'Taza', 'price' => '3.50', 'qty' => '5')),
            array('1', array('id' => 1, 'name' => 'Café molido', 'price' => '5.00', 'qty' => '10')),
            array('2', array('id' => 2, 'name' => 'Taza', 'price' => '3.50', 'qty' => '5')),
        ));
        $this->ci->model_products = $this->productos;

        // Doble del modelo de empresa (tasas de servicio e IVA).
        $empresa = $this->createMock(Model_company::class);
        $empresa->method('getCompanyData')->willReturn(array('service_charge_value' => '13', 'vat_charge_value' => '10'));
        $this->ci->model_company = $empresa;
    }

    private function post(array $lineas)
    {
        $p = array('product' => array(), 'qty' => array(), 'rate_value' => array(), 'amount_value' => array());
        foreach ($lineas as $id => $cant) {
            $p['product'][] = (string) $id;
            $p['qty'][] = (string) $cant;
            $p['rate_value'][] = $id == 1 ? '5.00' : '3.50';
            $p['amount_value'][] = number_format(($id == 1 ? 5 : 3.5) * $cant, 2, '.', '');
        }
        return array_merge($p, array(
            'customer_name' => 'Cliente', 'customer_address' => '', 'customer_phone' => '',
            'gross_amount_value' => '0', 'service_charge_rate' => '13', 'service_charge_value' => '0',
            'vat_charge_rate' => '10', 'vat_charge_value' => '0', 'net_amount_value' => '0', 'discount' => '0',
        ));
    }

    /**
     * @caso CP-02
     * @requisito RQ-02
     * @nivel unitaria
     * @tecnica caja-blanca
     */
    public function test_venta_descuenta_el_stock_de_cada_producto()
    {
        $this->ci->input->post = $this->post(array(1 => 3, 2 => 2)); // 3 cafés y 2 tazas

        // Se espera exactamente una actualización por producto con el stock nuevo.
        $this->productos->expects($this->exactly(2))->method('update')
            ->withConsecutive(
                array(array('qty' => 7), $this->equalTo('1')),  // 10 - 3
                array(array('qty' => 3), $this->equalTo('2'))   //  5 - 2
            )->willReturn(true);

        $orden = (new Model_orders())->create();

        $this->assertNotFalse($orden, 'La venta válida debe crearse');
        $this->assertCount(1, $this->db->insertsEn('orders'));
        $this->assertCount(2, $this->db->insertsEn('orders_item'));
    }

    /**
     * Defecto D-01: el sistema permite vender más de lo que hay en bodega.
     *
     * @caso CP-07
     * @requisito RQ-07
     * @nivel unitaria
     * @tecnica caja-blanca, valores-limite
     * @group critico
     */
    public function test_venta_rechaza_cantidad_mayor_al_stock()
    {
        $this->ci->input->post = $this->post(array(2 => 6)); // stock de tazas = 5 (límite + 1)

        $this->productos->expects($this->never())->method('update');

        $orden = (new Model_orders())->create();

        $this->assertFalse($orden, 'Una venta sin stock suficiente debe rechazarse');
        $this->assertCount(0, $this->db->insertsEn('orders'), 'No debe quedar ninguna orden registrada');
    }

    /**
     * Valor límite exacto: vender TODO el stock sí debe permitirse (queda en 0).
     *
     * @caso CP-07
     * @requisito RQ-07
     * @nivel unitaria
     * @tecnica valores-limite
     */
    public function test_venta_permite_vender_exactamente_el_stock_disponible()
    {
        $this->ci->input->post = $this->post(array(2 => 5));

        $this->productos->expects($this->once())->method('update')
            ->with(array('qty' => 0), $this->equalTo('2'))->willReturn(true);

        $this->assertNotFalse((new Model_orders())->create());
    }
}
