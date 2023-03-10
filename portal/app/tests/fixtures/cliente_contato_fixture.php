<?php
class ClienteContatoFixture extends CakeTestFixture {
	var $name = 'ClienteContato';
	var $table = 'cliente_contato';

	var $fields = array( 
	'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
	'codigo_cliente' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_tipo_contato' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	'codigo_tipo_retorno' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	'ddi' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	'ddd' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 128, ),
	'nome' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 256, ),
	'ramal' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

	var $records = array( 
		array(
		  'codigo' => 127,
		  'codigo_cliente' => 32128,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 11,
		  'descricao' => '50792511',
		  'nome' => 'BUONNY EMPREENDIMENTOS E PARTICIPAÇÕES LTDA',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:52',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 129,
		  'codigo_cliente' => 1,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 21,
		  'descricao' => '25035885',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS ',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:55',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 130,
		  'codigo_cliente' => 2,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 16,
		  'descricao' => '33741177',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (RP-SP)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:55',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 132,
		  'codigo_cliente' => 4,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 21,
		  'descricao' => '47483647',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (SV-BA)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:56',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 134,
		  'codigo_cliente' => 6,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 21,
		  'descricao' => '25031216',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (BH-MG)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:56',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 135,
		  'codigo_cliente' => 7,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 18,
		  'descricao' => '32229433',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (PP-SP)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:56',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 137,
		  'codigo_cliente' => 9,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 21,
		  'descricao' => '47483647',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (CH-SC)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:57',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 138,
		  'codigo_cliente' => 10,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 19,
		  'descricao' => '37351157',
		  'nome' => 'BRADESCO AUTO/RE COMPANHIA DE SEGUROS (SAN-SP)',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:57',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 140,
		  'codigo_cliente' => 12,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 21,
		  'descricao' => '47483647',
		  'nome' => 'SITRACK SERVICOS DE RASTREAMENTO LTDA',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:57',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 143,
		  'codigo_cliente' => 15,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 11,
		  'descricao' => '33666064',
		  'nome' => 'PORTO SEGURO CIA DE SEGUROS GERAIS',
		  'ramal' => NULL,
		  'data_inclusao' => '04/12/2015 11:08:58',
		  'codigo_usuario_inclusao' => 1,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 100,
		  'codigo_cliente' => 10011,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 11,
		  'descricao' => '33666064',
		  'nome' => 'Contato Teste 10011',
		  'ramal' => NULL,
		  'data_inclusao' => '01/01/2018 00:00:00',
		  'codigo_usuario_inclusao' => 67111,
		  'codigo_empresa' => 1,
		),
		array(
		  'codigo' => 100,
		  'codigo_cliente' => 10110,
		  'codigo_tipo_contato' => 2,
		  'codigo_tipo_retorno' => 1,
		  'ddi' => NULL,
		  'ddd' => 11,
		  'descricao' => '33666064',
		  'nome' => 'Contato Teste 10110',
		  'ramal' => NULL,
		  'data_inclusao' => '01/01/2018 00:00:00',
		  'codigo_usuario_inclusao' => 67111,
		  'codigo_empresa' => 1,
		),
	);

}