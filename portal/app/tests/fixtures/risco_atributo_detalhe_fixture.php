<?php
class RiscoAtributoDetalheFixture extends CakeTestFixture {
	var $name = 'RiscoAtributoDetalhe';
	var $table = 'riscos_atributos_detalhes';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
		'codigo_risco_atributo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'descricao' => 'Ar',
		  'codigo_risco_atributo' => 1,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 2,
		  'descricao' => 'Contato',
		  'codigo_risco_atributo' => 1,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 3,
		  'descricao' => 'Ar / Contato',
		  'codigo_risco_atributo' => 1,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 4,
		  'descricao' => 'Vibração de Corpo Inteiro',
		  'codigo_risco_atributo' => 1,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 0,
		),
		array(
		  'codigo' => 5,
		  'descricao' => 'Vibração de Mãos / Braços',
		  'codigo_risco_atributo' => 1,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 0,
		),
		array(
		  'codigo' => 6,
		  'descricao' => 'Não Aplica',
		  'codigo_risco_atributo' => 2,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 7,
		  'descricao' => 'Leve',
		  'codigo_risco_atributo' => 2,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 8,
		  'descricao' => 'Moderado',
		  'codigo_risco_atributo' => 2,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 9,
		  'descricao' => 'Sério',
		  'codigo_risco_atributo' => 2,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		),
		array(
		  'codigo' => 10,
		  'descricao' => 'Severo',
		  'codigo_risco_atributo' => 2,
		  'data_inclusao' => '25/07/2016',
		  'codigo_usuario_inclusao' => 61648,
		  'codigo_empresa' => 1,
		  'ativo' => 1,
		), 
	);

}
?>