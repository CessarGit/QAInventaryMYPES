<?php

use PHPUnit\Framework\TestCase;

require_once ROOTPATH . 'tests/Support/FakeDb.php';
require_once APPPATH . 'models/Model_reports.php';

/**
 * NIVEL: Unitaria | TÉCNICA: Caja blanca con datos conocidos
 *
 * CP-03 pide "totales iguales al cálculo manual". Aquí el cálculo manual está
 * escrito en la prueba: 100 + 50 en enero, 80 en marzo, 0 en los demás meses.
 */
class ReporteVentasTest extends TestCase
{
    /** @var FakeDb */
    private $db;

    protected function setUp(): void
    {
        $ci = TestCI::reset();
        $this->db = new FakeDb();
        $ci->db = $this->db;
        $this->db->filasConsulta = array(
            array('id' => 1, 'gross_amount' => '100.00', 'paid_status' => 1, 'date_time' => (string) mktime(10, 0, 0, 1, 5, 2026)),
            array('id' => 2, 'gross_amount' => '50.00',  'paid_status' => 1, 'date_time' => (string) mktime(10, 0, 0, 1, 20, 2026)),
            array('id' => 3, 'gross_amount' => '80.00',  'paid_status' => 1, 'date_time' => (string) mktime(10, 0, 0, 3, 2, 2026)),
            array('id' => 4, 'gross_amount' => '999.00', 'paid_status' => 1, 'date_time' => (string) mktime(10, 0, 0, 12, 31, 2025)),
        );
    }

    /** Mismo cálculo que hace el controlador Reports::index (suma por mes). */
    private function totalesPorMes(array $datos)
    {
        $totales = array();
        foreach ($datos as $mes => $ordenes) {
            $suma = 0;
            foreach ($ordenes as $o) {
                if (is_array($o)) {
                    $suma += (float) $o['gross_amount'];
                }
            }
            $totales[$mes] = $suma;
        }
        return $totales;
    }

    /**
     * @caso CP-03
     * @requisito RQ-03
     * @nivel unitaria
     * @tecnica caja-blanca
     */
    public function test_reporte_anual_suma_por_mes_igual_al_calculo_manual()
    {
        $totales = $this->totalesPorMes((new Model_reports())->getOrderData(2026));

        $this->assertCount(12, $totales, 'Debe haber una fila por cada mes');
        $this->assertEquals(150.00, $totales['2026-01']);
        $this->assertEquals(0.00, $totales['2026-02']);
        $this->assertEquals(80.00, $totales['2026-03']);
        $this->assertEquals(230.00, array_sum($totales), 'La venta de 2025 no debe sumarse a 2026');
    }

    /**
     * Caja blanca: el modelo solo debe consultar ventas PAGADAS (paid_status = 1).
     *
     * @caso CP-03
     * @requisito RQ-03
     * @nivel unitaria
     * @tecnica caja-blanca
     */
    public function test_reporte_consulta_solo_ventas_pagadas()
    {
        (new Model_reports())->getOrderData(2026);

        $consulta = $this->db->consultas[0];
        $this->assertStringContainsString('paid_status = ?', $consulta['sql']);
        $this->assertSame(array(1), $consulta['binds']);
    }
}
