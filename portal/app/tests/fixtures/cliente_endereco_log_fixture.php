<?php
class ClienteEnderecoLogFixture extends CakeTestFixture {
	Public $name = 'ClienteEnderecoLog';
	Public $table = 'cliente_endereco_log';
	
	Public $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_cliente_endereco' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_cliente' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_tipo_contato' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_endereco' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'complemento' => 
		array (
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => 255,
			),
		'numero' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'data_inclusao' => 
		array (
			'type' => 'datetime',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_usuario_inclusao' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'acao_sistema' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_empresa' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	Public $records = array (
		0 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2496,
			'codigo_cliente_endereco' => 2341,
			'codigo_cliente' => 2302,
			'codigo_endereco' => 809876,
			'numero' => 1384,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-05 10:12:49',
			'complemento' => ' ',
			),
		1 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2495,
			'codigo_cliente_endereco' => 2340,
			'codigo_cliente' => 2301,
			'codigo_endereco' => 13873,
			'numero' => 11,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-02 16:15:46',
			'complemento' => '1111111111',
			),
		2 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2494,
			'codigo_cliente_endereco' => 2339,
			'codigo_cliente' => 2300,
			'codigo_endereco' => 393483,
			'numero' => 578,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 15:50:14',
			'complemento' => ' ',
			),
		3 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 3,
			'codigo' => 2493,
			'codigo_cliente_endereco' => 2338,
			'codigo_cliente' => 2299,
			'codigo_endereco' => 444610,
			'numero' => 123,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 11:29:08',
			'complemento' => ' ',
			),
		4 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 4,
			'codigo' => 2492,
			'codigo_cliente_endereco' => 2337,
			'codigo_cliente' => 2299,
			'codigo_endereco' => 349067,
			'numero' => 685,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 10:51:51',
			'complemento' => '4',
			),
		5 => 
		array (
			'acao_sistema' => 2,
			'codigo_tipo_contato' => 4,
			'codigo' => 2491,
			'codigo_cliente_endereco' => 2336,
			'codigo_cliente' => 2299,
			'codigo_endereco' => 349067,
			'numero' => 685,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 10:51:07',
			'complemento' => '4',
			),
		6 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 4,
			'codigo' => 2490,
			'codigo_cliente_endereco' => 2336,
			'codigo_cliente' => 2299,
			'codigo_endereco' => 349067,
			'numero' => 685,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 10:50:16',
			'complemento' => '4',
			),
		7 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2489,
			'codigo_cliente_endereco' => 2335,
			'codigo_cliente' => 2299,
			'codigo_endereco' => 105918,
			'numero' => 2608,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 10:36:53',
			'complemento' => '33',
			),
		8 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2488,
			'codigo_cliente_endereco' => 2334,
			'codigo_cliente' => 2298,
			'codigo_endereco' => 808196,
			'numero' => 911,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:50:31',
			'complemento' => 'ANDAR 18 SALA 1801 ',
			),
		9 => 
		array (
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo' => 2487,
			'codigo_cliente_endereco' => 2333,
			'codigo_cliente' => 2297,
			'codigo_endereco' => 214503,
			'numero' => 191,
			'codigo_usuario_inclusao' => 1,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:47:51',
			'complemento' => '2 ANDAR CONJUNTO 26',
			),
		);
}
?>