<?php
class ClienteContatoLogFixture extends CakeTestFixture {
	Public $name = 'ClienteContatoLog';
	Public $table = 'cliente_contato_log';

	Public $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_cliente_contato' => 
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
		'codigo_tipo_retorno' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'ddi' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'ddd' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'descricao' => 
		array (
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
			),
		'nome' => 
		array (
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
			),
		'ramal' => 
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
			'ddi' => NULL,
			'ddd' => 11,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 6,
			'codigo_tipo_retorno' => 3,
			'ramal' => NULL,
			'codigo' => 20,
			'codigo_cliente_contato' => 258,
			'codigo_cliente' => 2302,
			'codigo_usuario_inclusao' => 66988,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-05 10:15:45',
			'descricao' => '34349595',
			'nome' => 'TIO PATINHAS',
			),
		1 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 3,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 19,
			'codigo_cliente_contato' => 257,
			'codigo_cliente' => 2302,
			'codigo_usuario_inclusao' => 66988,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-05 10:15:07',
			'descricao' => 'GCC@UOL.COM.BR',
			'nome' => 'GABRIELA CRAVO E CANELA',
			),
		2 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 3,
			'codigo_tipo_retorno' => 4,
			'ramal' => NULL,
			'codigo' => 18,
			'codigo_cliente_contato' => 256,
			'codigo_cliente' => 2302,
			'codigo_usuario_inclusao' => 66988,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-05 10:15:07',
			'descricao' => '3225144',
			'nome' => 'GABRIELA CRAVO E CANELA',
			),
		3 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo_tipo_retorno' => 7,
			'ramal' => NULL,
			'codigo' => 17,
			'codigo_cliente_contato' => 255,
			'codigo_cliente' => 2299,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 11:10:47',
			'descricao' => '(11) 1234512345',
			'nome' => 'Priscila',
			),
		4 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 1,
			'codigo_tipo_contato' => 2,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 16,
			'codigo_cliente_contato' => 254,
			'codigo_cliente' => 2297,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:53:01',
			'descricao' => 'buonny@buonny.com.br',
			'nome' => 'Buonny',
			),
		5 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 2,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 15,
			'codigo_cliente_contato' => 254,
			'codigo_cliente' => 2297,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:52:48',
			'descricao' => 'buoony@buonny.com.br',
			'nome' => 'Buonny',
			),
		6 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 6,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 14,
			'codigo_cliente_contato' => 253,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:51:49',
			'descricao' => 'bsat_rota90@buoony.com.br',
			'nome' => 'Rota 90',
			),
		7 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 7,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 13,
			'codigo_cliente_contato' => 252,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:51:48',
			'descricao' => 'bsat_rota90@buoony.com.br',
			'nome' => 'Rota 90',
			),
		8 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 5,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 12,
			'codigo_cliente_contato' => 251,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:51:48',
			'descricao' => 'bsat_rota90@buoony.com.br',
			'nome' => 'Rota 90',
			),
		9 => 
		array (
			'ddi' => NULL,
			'ddd' => NULL,
			'acao_sistema' => 0,
			'codigo_tipo_contato' => 3,
			'codigo_tipo_retorno' => 2,
			'ramal' => NULL,
			'codigo' => 11,
			'codigo_cliente_contato' => 250,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 08:51:48',
			'descricao' => 'bsat_rota90@buoony.com.br',
			'nome' => 'Rota 90',
			),
		);
}