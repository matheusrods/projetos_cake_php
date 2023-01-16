<?php
class BairroFixture extends CakeTestFixture {
	var $name    = 'Bairro';
	
    var $fields = array(
        'codigo' => array( 'type' => 'integer',  'null' => false, 'default' => NULL,'length' => NULL,   'key' => 'primary', ),
        'codigo_endereco_cidade' => array( 'type' => 'integer',  'null' => false, 'default' => NULL,'length' => NULL, ),
        'codigo_correio' => array( 'type' => 'integer',  'null' => true, 'default' => NULL,'length' => NULL, ),
        'descricao' => array( 'type' => 'string',  'null' => false, 'default' => NULL,'length' => 255, ),
        'data_inclusao' => array( 'type' => 'datetime',  'null' => false, 'default' => '(getdate())','length' => NULL, ),
        'codigo_usuario_inclusao' => array( 'type' => 'integer',  'null' => false, 'default' => NULL,'length' => NULL, ),
        'codigo_endereco_distrito' => array( 'type' => 'integer',  'null' => true, 'default' => NULL,'length' => NULL, ),
        'abreviacao' => array( 'type' => 'string',  'null' => true, 'default' => NULL,'length' => 255, ),
    );
}

?>