<?php
class PpraVersoesFixture extends CakeTestFixture {

	var $name = 'PpraVersoes';
	var $table = 'ppra_versoes';
	
	var $fields = array( 
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'versao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 14, ),
		'inicio_vigencia_ppra' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
		'periodo_vigencia_ppra' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 2, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_medico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente_alocacao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);
	
}