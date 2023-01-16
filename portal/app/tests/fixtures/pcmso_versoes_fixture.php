<?php
class PcmsoVersoesFixture extends CakeTestFixture {

	var $name = 'PcmsoVersoes';
	var $table = 'pcmso_versoes';

	var $fields = array( 
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'versao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 14, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'inicio_vigencia_pcmso' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
		'periodo_vigencia_pcmso' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 2, ),
		'codigo_medico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente_alocacao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);
	
}