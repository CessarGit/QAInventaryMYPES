<?php

use PHPUnit\Framework\TestCase;

/**
 * Base para pruebas de INTEGRACIÓN: usa el driver real de CodeIgniter (mysqli)
 * contra una base de pruebas (por defecto `stock_test`). NUNCA apuntes esto a la
 * base de producción: cada prueba vacía las tablas y vuelve a sembrar datos.
 *
 * Variables de entorno (con sus valores por defecto):
 *   DB_HOST=127.0.0.1  DB_PORT=3306  DB_USER=test  DB_PASS=test  DB_NAME=stock_test
 */
abstract class DatabaseTestCase extends TestCase
{
    /** @var bool el esquema se carga una sola vez por ejecución */
    private static $schemaLoaded = false;

    /** @var CI_DB_mysqli_driver */
    protected $db;

    const TABLAS = array(
        'attributes', 'attribute_value', 'brands', 'categories', 'company', 'groups',
        'orders', 'orders_item', 'products', 'stores', 'users', 'user_group',
    );

    /** Contraseña de todos los usuarios sembrados. */
    const CLAVE = 'Clave123!';

    public static function dbConfig()
    {
        $name = getenv('DB_NAME') ?: 'stock_test';
        if ($name === 'stock') {
            throw new RuntimeException('Por seguridad, las pruebas no se ejecutan sobre la base `stock`.');
        }
        return array(
            'hostname' => getenv('DB_HOST') ?: '127.0.0.1',
            'port'     => getenv('DB_PORT') ?: 3306,
            'username' => getenv('DB_USER') ?: 'test',
            'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'test',
            'database' => $name,
        );
    }

    /** Recrea las 12 tablas a partir de stock.sql (el mismo esquema del proyecto). */
    public static function cargarEsquema()
    {
        if (self::$schemaLoaded) {
            return;
        }
        mysqli_report(MYSQLI_REPORT_OFF);
        $c = self::dbConfig();
        $m = new mysqli($c['hostname'], $c['username'], $c['password'], $c['database'], (int) $c['port']);
        if ($m->connect_errno) {
            throw new RuntimeException('No se pudo conectar a MySQL de pruebas: ' . $m->connect_error);
        }
        $m->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (self::TABLAS as $t) {
            $m->query("DROP TABLE IF EXISTS `$t`");
        }
        $sql = file_get_contents(ROOTPATH . 'stock.sql');
        if (!$m->multi_query($sql)) {
            throw new RuntimeException('Error cargando stock.sql: ' . $m->error);
        }
        do {
            if ($r = $m->store_result()) {
                $r->free();
            }
        } while ($m->more_results() && $m->next_result());
        if ($m->errno) {
            throw new RuntimeException('Error cargando stock.sql: ' . $m->error);
        }
        $m->close();
        self::$schemaLoaded = true;
    }

    /** Conexión con el driver real de CodeIgniter. */
    public static function conectarCI()
    {
        require_once BASEPATH . 'database/DB.php';
        $c = self::dbConfig();
        $params = array(
            'dsn' => '', 'hostname' => $c['hostname'], 'port' => $c['port'],
            'username' => $c['username'], 'password' => $c['password'], 'database' => $c['database'],
            'dbdriver' => 'mysqli', 'dbprefix' => '', 'pconnect' => false,
            'db_debug' => false, // los errores devuelven FALSE en vez de detener PHP
            'cache_on' => false, 'cachedir' => '', 'char_set' => 'utf8', 'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '', 'encrypt' => false, 'compress' => false, 'stricton' => false,
            'failover' => array(), 'save_queries' => true,
        );
        $db = DB($params, true);
        $db->initialize();
        return $db;
    }

    protected function setUp(): void
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        self::cargarEsquema();
        $ci = TestCI::reset();
        $ci->db = self::conectarCI();
        $this->db = $ci->db;
        $this->sembrarDatos();
    }

    protected function tearDown(): void
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /** Datos conocidos: con ellos sabemos de antemano el resultado esperado. */
    protected function sembrarDatos()
    {
        $db = $this->db;
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (self::TABLAS as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }

        $db->insert('company', array(
            'id' => 1, 'company_name' => 'MYPE de prueba', 'service_charge_value' => '13',
            'vat_charge_value' => '10', 'address' => 'San Salvador', 'phone' => '2222-2222',
            'country' => 'El Salvador', 'message' => '', 'currency' => 'USD',
        ));

        // Grupos (roles) exactamente como la Tabla 5 del documento.
        $roles = require ROOTPATH . 'tests/fixtures/roles.php';
        foreach ($roles as $id => $rol) {
            $db->insert('groups', array('id' => $id, 'group_name' => $rol['nombre'], 'permission' => serialize($rol['permisos'])));
        }

        // Un usuario por rol. Todos con la misma clave para facilitar las pruebas.
        $hash = password_hash(self::CLAVE, PASSWORD_DEFAULT);
        $usuarios = array(
            1 => array('admin', 'admin@admin.com', 1),
            2 => array('gerente', 'gerente@mype.test', 2),
            3 => array('ventas', 'ventas@mype.test', 3),
            4 => array('bodega', 'bodega@mype.test', 4),
            5 => array('logistica', 'logistica@mype.test', 5),
        );
        foreach ($usuarios as $id => $u) {
            $db->insert('users', array(
                'id' => $id, 'username' => $u[0], 'password' => $hash, 'email' => $u[1],
                'firstname' => ucfirst($u[0]), 'lastname' => 'Prueba', 'phone' => '7000-0000', 'gender' => 1,
            ));
            $db->insert('user_group', array('id' => $id, 'user_id' => $id, 'group_id' => $u[2]));
        }

        $db->insert('brands', array('id' => 1, 'name' => 'Marca A', 'active' => 1));
        $db->insert('categories', array('id' => 1, 'name' => 'Bebidas', 'active' => 1));
        $db->insert('stores', array('id' => 1, 'name' => 'Tienda Central', 'active' => 1));

        $this->crearProducto(1, 'Café molido', '5.00', 10);
        $this->crearProducto(2, 'Taza', '3.50', 5);
        $this->crearProducto(3, 'Producto inactivo', '1.00', 50, 2);
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function crearProducto($id, $nombre, $precio, $stock, $disponible = 1)
    {
        $this->db->insert('products', array(
            'id' => $id, 'name' => $nombre, 'sku' => 'SKU-' . $id, 'price' => $precio, 'qty' => (string) $stock,
            'image' => '', 'description' => '', 'attribute_value_id' => serialize(array()),
            'brand_id' => serialize(array('1')), 'category_id' => serialize(array('1')),
            'store_id' => 1, 'availability' => $disponible,
        ));
    }

    // ---------- utilidades de consulta para las aserciones ----------

    protected function stock($productId)
    {
        $row = $this->db->query('SELECT qty FROM products WHERE id = ?', array($productId))->row_array();
        return $row ? (int) $row['qty'] : null;
    }

    protected function contar($tabla)
    {
        return (int) $this->db->count_all($tabla);
    }

    /** Arma el POST que envía el formulario "Agregar venta" (orders/create). */
    protected function postVenta(array $lineas, array $extra = array())
    {
        $productos = $this->db->query('SELECT id, price FROM products')->result_array();
        $precios = array_column($productos, 'price', 'id');
        $post = array('product' => array(), 'qty' => array(), 'rate_value' => array(), 'amount_value' => array());
        $bruto = 0;
        foreach ($lineas as $id => $cantidad) {
            $precio = isset($precios[$id]) ? (float) $precios[$id] : 0;
            $post['product'][] = (string) $id;
            $post['qty'][] = (string) $cantidad;
            $post['rate_value'][] = number_format($precio, 2, '.', '');
            $post['amount_value'][] = number_format($precio * $cantidad, 2, '.', '');
            $bruto += $precio * $cantidad;
        }
        $servicio = round($bruto * 0.13, 2);
        $iva = round($bruto * 0.10, 2);
        return array_merge($post, array(
            'customer_name' => 'Cliente de prueba', 'customer_address' => 'Centro', 'customer_phone' => '7777-7777',
            'gross_amount_value' => number_format($bruto, 2, '.', ''),
            'service_charge_rate' => '13', 'service_charge_value' => number_format($servicio, 2, '.', ''),
            'vat_charge_rate' => '10', 'vat_charge_value' => number_format($iva, 2, '.', ''),
            'net_amount_value' => number_format($bruto + $servicio + $iva, 2, '.', ''),
            'discount' => '0', 'paid_status' => '2',
        ), $extra);
    }
}
