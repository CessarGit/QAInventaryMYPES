<?php
/**
 * Prueba de humo (subpaso 5.2): CodeIgniter carga y la conexión apunta a stock_test.
 * Esperado: base stock_test (regla 14) con los 2 grupos de stock.sql y los 5 de PC-0.
 */
class Humo_test extends TestCase
{
	public function test_codeigniter_carga_y_conecta_a_stock_test()
	{
		$CI =& get_instance();
		$CI->load->database();

		$this->assertSame('stock_test', $CI->db->database);
		$grupos = (int) $CI->db->query('SELECT COUNT(*) AS n FROM `groups`')->row()->n;
		$this->assertSame(7, $grupos);
	}
}
