<?
class GrupoRiscoFixture extends CakeTestFixture {
	var $name = 'GrupoRisco';
	var $table = 'grupos_riscos';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 1,
		  'descricao' => 'FÍSICO',
		),
		array(
		  'codigo' => 2,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 1,
		  'descricao' => 'QUÍMICO',
		),
		array(
		  'codigo' => 3,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 1,
		  'descricao' => 'BIOLÓGICO',
		),
		array(
		  'codigo' => 4,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 0,
		  'descricao' => 'AUSÊNCIA DE RISCO',
		),
		array(
		  'codigo' => 5,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 1,
		  'descricao' => 'ERGONÔMICOS',
		),
		array(
		  'codigo' => 6,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '11/08/2016 11:52:09',
		  'ativo' => 0,
		  'descricao' => 'ACIDENTES',
		),
		array(
		  'codigo' => 7,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '27/10/2016 13:03:46',
		  'ativo' => 0,
		  'descricao' => 'MECÂNICO',
		),
		array(
		  'codigo' => 8,
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '27/10/2016 13:04:22',
		  'ativo' => 1,
		  'descricao' => 'OUTROS',
		),
		array(
		  'codigo' => 9,
		  'codigo_usuario_inclusao' => 67111,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '08/05/2018 11:55:57',
		  'ativo' => 1,
		  'descricao' => 'MECÂNICO/ACIDENTES',
		),
		array(
		  'codigo' => 10,
		  'codigo_usuario_inclusao' => 67111,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '08/05/2018 11:55:57',
		  'ativo' => 1,
		  'descricao' => 'PERICULOSOS',
		),
	);

}
?>