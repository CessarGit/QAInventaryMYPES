<?php

use PHPUnit\Framework\TestCase;

/**
 * NIVEL: Unitaria | TÉCNICA: TDD (rojo -> verde -> refactor) con valores límite
 *
 * Esta prueba se escribió ANTES que la librería Order_calculator (Fase 4, D-03).
 * Primero falló (🔴 "Class Order_calculator not found"), luego se escribió el
 * código mínimo para que pase (🟢) y al final se ordenó el código (refactor).
 * Para ejecutar solo las pruebas de la Fase 2:  vendor/bin/phpunit --exclude-group fase4
 *
 * @group fase4
 */
class OrderCalculatorTest extends TestCase
{
    /** @var Order_calculator */
    private $calc;

    protected function setUp(): void
    {
        require_once APPPATH . 'libraries/Order_calculator.php';
        $this->calc = new Order_calculator();
    }

    /**
     * @caso CP-09
     * @requisito RQ-09
     * @nivel unitaria
     * @tecnica tdd
     */
    public function test_calcula_bruto_servicio_iva_y_neto()
    {
        $t = $this->calc->calcular(array(
            array('precio' => 5.00, 'cantidad' => 3),   // 15.00
            array('precio' => 3.50, 'cantidad' => 2),   //  7.00
        ), 13, 10, 0);

        $this->assertSame(22.00, $t['bruto']);
        $this->assertSame(2.86, $t['servicio']);  // 22 x 13 %
        $this->assertSame(2.20, $t['iva']);       // 22 x 10 %
        $this->assertSame(27.06, $t['neto']);
        $this->assertSame(15.00, $t['lineas'][0]['importe']);
    }

    /**
     * Valores límite del descuento: 0, igual al total y total + 0.01.
     *
     * @caso CP-09
     * @requisito RQ-09
     * @nivel unitaria
     * @tecnica valores-limite
     */
    public function test_descuento_en_sus_valores_limite()
    {
        $linea = array(array('precio' => 10.00, 'cantidad' => 1)); // total con cargos = 12.30

        $this->assertSame(12.30, $this->calc->calcular($linea, 13, 10, 0)['neto']);
        $this->assertSame(0.00, $this->calc->calcular($linea, 13, 10, 12.30)['neto']);

        $this->expectException(InvalidArgumentException::class);
        $this->calc->calcular($linea, 13, 10, 12.31);
    }

    public function entradasInvalidas()
    {
        return array(
            'cantidad cero'      => array(array(array('precio' => 5, 'cantidad' => 0)), 0),
            'cantidad negativa'  => array(array(array('precio' => 5, 'cantidad' => -1)), 0),
            'precio negativo'    => array(array(array('precio' => -5, 'cantidad' => 1)), 0),
            'descuento negativo' => array(array(array('precio' => 5, 'cantidad' => 1)), -1),
            'venta sin líneas'   => array(array(), 0),
        );
    }

    /**
     * @dataProvider entradasInvalidas
     * @caso CP-09
     * @requisito RQ-09
     * @nivel unitaria
     * @tecnica particion-equivalencia
     */
    public function test_rechaza_entradas_invalidas($lineas, $descuento)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->calc->calcular($lineas, 13, 10, $descuento);
    }
}
