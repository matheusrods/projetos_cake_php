<?php
class UperfilTipoAlertaFixture extends CakeTestFixture {
    var $name = 'UperfilTipoAlerta';
    var $table = 'uperfis_tipos_alertas';
    var $fields = array (
        'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
        'codigo_alerta_tipo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'codigo_uperfil' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    );
    var $records = array(
        array (
            'codigo_alerta_tipo' => 44,
            'codigo_uperfil' => 7,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
        array (
            'codigo_alerta_tipo' => 43,
            'codigo_uperfil' => 8,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
        array (
            'codigo_alerta_tipo' => 42,
            'codigo_uperfil' => 9,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
        array (
            'codigo_alerta_tipo' => 42,
            'codigo_uperfil' => 10,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
        array (
            'codigo_alerta_tipo' => 40,
            'codigo_uperfil' => 5,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
        array (
            'codigo_alerta_tipo' => 41,
            'codigo_uperfil' => 5,
            'codigo_usuario_inclusao' => 48321,
            'data_inclusao' => '2015-06-11 16:27:58',
        ),
    );
}
?>