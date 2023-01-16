<?php
class CargoLogFixture extends CakeTestFixture {
	var $name = 'CargoLog';
	var $table = 'cargos_log';

	var $fields = array( 
		'requisito' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao_cargo' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'educacao' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'treinamento' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'habilidades' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'experiencias' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao_local' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'observacao_aso' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'material_utilizado' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'mobiliario_utilizado' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'local_trabalho' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'acao_sistema' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_cargos' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_gfip' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_funcao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
		'codigo_cbo' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 7, ),
		'codigo_rh' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 60, ),
		'descricao_ppp' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
	);
}