<?php
class GrupoEconomicoLogFixture extends CakeTestFixture {

	Public $name = 'GrupoEconomicoLog';
	Public $table = 'grupos_economicos_log';

	Public $fields = array(
		'codigo' 					=> array('type' => 'integer',  'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary', ),
  		'codigo_grupos_economicos' 	=> array('type' => 'integer',  'null' => false, 'default' => NULL, 'length' => NULL, ),
    	'descricao' 				=> array('type' => 'string',   'null' => false, 'default' => NULL, 'length' => 255, ),
    	'data_inclusao' 			=> array('type' => 'datetime', 'null' => true,  'default' => NULL, 'length' => NULL, ),
    	'codigo_usuario_inclusao' 	=> array('type' => 'integer',  'null' => false, 'default' => NULL, 'length' => NULL, ),
    	'acao_sistema' 				=> array('type' => 'integer',  'null' => true,  'default' => NULL, 'length' => NULL, ),
    	'codigo_cliente' 			=> array('type' => 'integer',  'null' => true,  'default' => NULL, 'length' => NULL, ),
    	'codigo_empresa' 			=> array('type' => 'integer',  'null' => true,  'default' => NULL, 'length' => NULL, ),
    	'vias_aso' 					=> array('type' => 'integer',  'null' => true,  'default' => NULL, 'length' => NULL, ),
  	);
}
?>