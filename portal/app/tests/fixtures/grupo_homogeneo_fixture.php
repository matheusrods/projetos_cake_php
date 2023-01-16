<?php

class GrupoHomogeneoFixture extends CakeTestFixture {

	var $name = 'GrupoHomogeneo';
	var $table = 'grupos_homogeneos_exposicao';

	var $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'descricao' => 
		array (
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
			),
		'codigo_cliente' => 
		array (
			'type' => 'integer',
			'null' => false,
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
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'ativo' => 
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
		'numero_versao' => 
		array (
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => 255,
			),
		'data_entrevista' => 
		array (
			'type' => 'date',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_funcionario' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'data_alteracao' => 
		array (
			'type' => 'datetime',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);

	var $records = array (
		0 => 
		array (
			'data_entrevista' => '16/08/2016',
			'codigo' => 34,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 66986,
			'codigo_empresa' => 1,
			'codigo_funcionario' => 2253,
			'data_inclusao' => '2016-08-24 16:10:37',
			'data_alteracao' => '25/08/2016 11:58:58',
			'ativo' => 1,
			'descricao' => 'Nova Exposição',
			'numero_versao' => '8',
			),
		1 => 
		array (
			'data_entrevista' => '10/05/2016',
			'codigo' => 32,
			'codigo_cliente' => 2298,
			'codigo_usuario_inclusao' => 66986,
			'codigo_empresa' => 1,
			'codigo_funcionario' => 2253,
			'data_inclusao' => '2016-08-24 15:18:11',
			'data_alteracao' => '25/08/2016 11:55:46',
			'ativo' => 1,
			'descricao' => 'Exposição Sonora',
			'numero_versao' => '10',
			),
		2 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 30,
			'codigo_cliente' => 2299,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-08-17 11:42:37',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'Grupo',
			'numero_versao' => NULL,
			),
		3 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 29,
			'codigo_cliente' => 2297,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-08-17 09:01:12',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'Adm',
			'numero_versao' => NULL,
			),
		4 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 28,
			'codigo_cliente' => 2293,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-08-16 09:29:29',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'Teste',
			'numero_versao' => NULL,
			),
		5 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 27,
			'codigo_cliente' => 1165,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-08-01 17:25:13',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'ADM',
			'numero_versao' => NULL,
			),
		6 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 26,
			'codigo_cliente' => 2296,
			'codigo_usuario_inclusao' => 66982,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-07-27 15:52:49',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'Primeiro Andar',
			'numero_versao' => NULL,
			),
		7 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 25,
			'codigo_cliente' => 2295,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-07-25 17:05:22',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'COMERCIAL / ATENDIMENTO',
			'numero_versao' => NULL,
			),
		8 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 23,
			'codigo_cliente' => 2294,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-07-25 09:51:20',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'COMERCIAL / ATENDIMENTO',
			'numero_versao' => NULL,
			),
		9 => 
		array (
			'data_entrevista' => NULL,
			'codigo' => 22,
			'codigo_cliente' => 2293,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'codigo_funcionario' => NULL,
			'data_inclusao' => '2016-07-25 09:23:15',
			'data_alteracao' => NULL,
			'ativo' => 1,
			'descricao' => 'Atendimento',
			'numero_versao' => NULL,
			),
		);
}



