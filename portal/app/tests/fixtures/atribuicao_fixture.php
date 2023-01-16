<?php
class AtribuicaoFixture extends CakeTestFixture {
	var $name = 'Atribuicao';
	var $table = 'atribuicao';

	var $fields = array(
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_cliente' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);

	var $records = array(
		array(
		  'codigo' => 1,
		  'codigo_cliente' => 10011,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '17/04/2018 12:48:47',
		  'ativo' => 1,
		  'descricao' => 'Manipulador de alimentos',
		), 
		array(
		  'codigo' => 2,
		  'codigo_cliente' => 20,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '18/07/2018 11:28:15',
		  'ativo' => 0,
		  'descricao' => 'Trabalho em altura',
		), 
		array(
		  'codigo' => 3,
		  'codigo_cliente' => 20,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '18/07/2018 11:28:29',
		  'ativo' => 0,
		  'descricao' => 'Espaço confinado',
		), 
		array(
		  'codigo' => 4,
		  'codigo_cliente' => 20,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '18/07/2018 11:28:43',
		  'ativo' => 0,
		  'descricao' => 'Manipulação de alimentos',
		), 
		array(
		  'codigo' => 5,
		  'codigo_cliente' => 20,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '18/07/2018 11:28:58',
		  'ativo' => 0,
		  'descricao' => 'Vigilante armado',
		), 
		array(
		  'codigo' => 6,
		  'codigo_cliente' => 10011,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '15/08/2018 10:28:11',
		  'ativo' => 1,
		  'descricao' => 'Trabalho em altura',
		), 
		array(
		  'codigo' => 7,
		  'codigo_cliente' => 10011,
		  'codigo_usuario_inclusao' => 63085,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '15/08/2018 10:35:22',
		  'ativo' => 1,
		  'descricao' => 'Espaço confinado',
		), 
	);

}
?>