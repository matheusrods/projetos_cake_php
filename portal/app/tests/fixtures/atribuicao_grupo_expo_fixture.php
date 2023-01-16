<?php
class AtribuicaoGrupoExpoFixture extends CakeTestFixture {
	var $name = 'AtribuicaoGrupoExpo';
	var $table = 'atribuicoes_grupos_expo';

	var $fields = array(
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_atribuicao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_exposicao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array(
		array(
		  'codigo' => 1,
		  'codigo_atribuicao' => 1,
		  'codigo_grupo_exposicao' => 15824,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '17/04/2018 12:51:30',
		  'ativo' => 1,
		),
		array(
		  'codigo' => 2,
		  'codigo_atribuicao' => 1,
		  'codigo_grupo_exposicao' => 16269,
		  'codigo_usuario_inclusao' => 67535,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '07/06/2018 10:26:56',
		  'ativo' => 1,
		),
	);

}
?>