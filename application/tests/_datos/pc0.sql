-- PC-0: datos de prueba para stock_test (regla 16). Se carga después de stock.sql con
-- scripts/preparar-stock-test.sh, que define @pc0_hash (password_hash de TEST_USER_PASS).
-- Permisos: Tabla 5 corregida (PR #13) mapeada con el Anexo B de RV-01 y D-07 (b).
-- Ids desde 10: ningún grupo ni usuario con id 1 (H-RV04-09) ni choque con el volcado.
-- Totales: Gerente 28, Ventas 6, Bodega 20, Logística 20, Administrador 10. Dashboard sin
-- fila en la matriz: sin permiso (H-RV01-08).

INSERT INTO `groups` (`id`, `group_name`, `permission`) VALUES
(10, 'Gerente', 'a:28:{i:0;s:13:"updateSetting";i:1;s:11:"viewProfile";i:2;s:11:"createBrand";i:3;s:9:"viewBrand";i:4;s:11:"updateBrand";i:5;s:11:"deleteBrand";i:6;s:14:"createCategory";i:7;s:12:"viewCategory";i:8;s:14:"updateCategory";i:9;s:14:"deleteCategory";i:10;s:15:"createAttribute";i:11;s:13:"viewAttribute";i:12;s:15:"updateAttribute";i:13;s:15:"deleteAttribute";i:14;s:11:"createStore";i:15;s:9:"viewStore";i:16;s:11:"updateStore";i:17;s:11:"deleteStore";i:18;s:13:"createProduct";i:19;s:11:"viewProduct";i:20;s:13:"updateProduct";i:21;s:13:"deleteProduct";i:22;s:11:"createOrder";i:23;s:9:"viewOrder";i:24;s:11:"updateOrder";i:25;s:11:"deleteOrder";i:26;s:11:"viewReports";i:27;s:13:"updateCompany";}'),
(11, 'Ventas', 'a:6:{i:0;s:13:"updateSetting";i:1;s:11:"viewProfile";i:2;s:11:"viewProduct";i:3;s:11:"createOrder";i:4;s:9:"viewOrder";i:5;s:11:"viewReports";}'),
(12, 'Bodega', 'a:20:{i:0;s:13:"updateSetting";i:1;s:11:"viewProfile";i:2;s:11:"createBrand";i:3;s:9:"viewBrand";i:4;s:11:"updateBrand";i:5;s:11:"deleteBrand";i:6;s:14:"createCategory";i:7;s:12:"viewCategory";i:8;s:14:"updateCategory";i:9;s:14:"deleteCategory";i:10;s:15:"createAttribute";i:11;s:13:"viewAttribute";i:12;s:15:"updateAttribute";i:13;s:15:"deleteAttribute";i:14;s:9:"viewStore";i:15;s:13:"createProduct";i:16;s:11:"viewProduct";i:17;s:13:"updateProduct";i:18;s:13:"deleteProduct";i:19;s:11:"viewReports";}'),
(13, 'Logistica', 'a:20:{i:0;s:13:"updateSetting";i:1;s:11:"viewProfile";i:2;s:11:"createBrand";i:3;s:9:"viewBrand";i:4;s:11:"updateBrand";i:5;s:11:"deleteBrand";i:6;s:14:"createCategory";i:7;s:12:"viewCategory";i:8;s:14:"updateCategory";i:9;s:14:"deleteCategory";i:10;s:15:"createAttribute";i:11;s:13:"viewAttribute";i:12;s:15:"updateAttribute";i:13;s:15:"deleteAttribute";i:14;s:9:"viewStore";i:15;s:13:"createProduct";i:16;s:11:"viewProduct";i:17;s:13:"updateProduct";i:18;s:13:"deleteProduct";i:19;s:11:"viewReports";}'),
(14, 'Administrador', 'a:10:{i:0;s:10:"createUser";i:1;s:8:"viewUser";i:2;s:10:"updateUser";i:3;s:10:"deleteUser";i:4;s:11:"createGroup";i:5;s:9:"viewGroup";i:6;s:11:"updateGroup";i:7;s:11:"deleteGroup";i:8;s:13:"updateSetting";i:9;s:11:"viewProfile";}');

INSERT INTO `users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `phone`, `gender`) VALUES
(10, 'gerente_test',   @pc0_hash, 'gerente_test@pc0.local',   'Gerente',       'PC0', '00000000', 1),
(11, 'ventas_test',    @pc0_hash, 'ventas_test@pc0.local',    'Ventas',        'PC0', '00000000', 1),
(12, 'bodega_test',    @pc0_hash, 'bodega_test@pc0.local',    'Bodega',        'PC0', '00000000', 1),
(13, 'logistica_test', @pc0_hash, 'logistica_test@pc0.local', 'Logistica',     'PC0', '00000000', 1),
(14, 'admin_test',     @pc0_hash, 'admin_test@pc0.local',     'Administrador', 'PC0', '00000000', 1);

INSERT INTO `user_group` (`id`, `user_id`, `group_id`) VALUES
(10, 10, 10), (11, 11, 11), (12, 12, 12), (13, 13, 13), (14, 14, 14);

-- Producto con stock N = 5 (D-01), con la marca y la categoría del volcado (id 4).
INSERT INTO `stores` (`id`, `name`, `active`) VALUES (10, 'TIENDA-PC0', 1);
INSERT INTO `products` (`id`, `name`, `sku`, `price`, `qty`, `image`, `description`,
  `attribute_value_id`, `brand_id`, `category_id`, `store_id`, `availability`) VALUES
(10, 'PRODUCTO-PC0', 'PC0-001', '10', '5', '', 'Producto de prueba PC-0', 'null', '["4"]', '["4"]', 10, 1);
