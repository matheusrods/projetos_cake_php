<?php
class FornecedorContatoFixture extends CakeTestFixture {
	var $name = 'FornecedorContato';
	var $table = 'fornecedores_contato';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_fornecedor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_tipo_contato' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'codigo_tipo_retorno' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'ddi' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'ddd' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'descricao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 256, ),
		'nome' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 256, ),
		'ramal' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

	var $records = array(
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 4,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 1,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 605,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:01',
		  'descricao' => '1135789225',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 5,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 7,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 605,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:01',
		  'descricao' => '1135789226',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 6,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'faturamento@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 7,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'faturamento@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 8,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'financeiro2@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 9,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'guiaosasco@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 10,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'sac@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 11,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => 'vanessa.ribeiro@prevermed.com.br',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 12,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 1,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 606,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:03',
		  'descricao' => '1136814333',
		  'nome' => 'PERCIO DE OLIVEIRA BUZI',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 13,
		  'codigo_tipo_contato' => 6,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 607,
		  'codigo_usuario_inclusao' => 61608,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '04/05/2016 18:01:05',
		  'descricao' => 'engenhariabrumedczs@gmail.com',
		  'nome' => 'BRUNO ANTONIO ALVES',
		),
		array(
		  'ddi' => NULL,
		  'ddd' => NULL,
		  'codigo' => 100,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 2,
		  'ramal' => NULL,
		  'codigo_fornecedor' => 2883,
		  'codigo_usuario_inclusao' => 67111,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '01/01/2018 00:00:00',
		  'descricao' => 'teste@teste.com',
		  'nome' => 'teste',
		),
	);

}