<?php
/**
 * Tabla 5 del documento (Matriz de roles y permisos) traducida a los permisos
 * que el sistema realmente revisa con in_array(...) en los controladores.
 *
 * C=create, R=view, U=update, D=delete.
 * Nota: el sistema no tiene permiso "viewCompany", por eso Logística (R en Company)
 * no puede representarse; queda registrado como hallazgo de la auditoría.
 */

$crud = function ($entidad) {
    return array('create' . $entidad, 'view' . $entidad, 'update' . $entidad, 'delete' . $entidad);
};

$catalogos = array_merge($crud('Brand'), $crud('Category'), $crud('Attribute'));

return array(
    1 => array(
        'nombre'   => 'Administrador',
        'permisos' => array_merge($crud('User'), $crud('Group'), array('updateSetting', 'viewProfile')),
    ),
    2 => array(
        'nombre'   => 'Gerente',
        'permisos' => array_merge(
            $catalogos, $crud('Store'), $crud('Product'), $crud('Order'),
            array('viewReports', 'updateCompany', 'updateSetting', 'viewProfile')
        ),
    ),
    3 => array(
        'nombre'   => 'Ventas',
        'permisos' => array('viewProduct', 'createOrder', 'viewOrder', 'viewReports', 'updateSetting', 'viewProfile'),
    ),
    4 => array(
        'nombre'   => 'Bodega',
        'permisos' => array_merge($catalogos, array('viewStore'), $crud('Product'), array('viewReports', 'updateSetting', 'viewProfile')),
    ),
    5 => array(
        'nombre'   => 'Logística',
        'permisos' => array_merge($catalogos, array('viewStore'), $crud('Product'), array('viewReports', 'updateSetting', 'viewProfile')),
    ),
);
