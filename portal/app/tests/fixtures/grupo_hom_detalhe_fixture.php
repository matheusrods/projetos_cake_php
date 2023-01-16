<?php

class GrupoHomDetalheFixture extends CakeTestFixture {

	var $name = 'GrupoHomDetalhe';
	var $table = 'grupos_homogeneos_exposicao_detalhes';

	var $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_grupo_homogeneo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_setor' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_cargo' => 
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
		'codigo_empresa' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);

	var $records = array (
		array (
			'codigo' => 125,
			'codigo_grupo_homogeneo' => 34,
			'codigo_setor' => 2127,
			'codigo_cargo' => 3153,
			'codigo_usuario_inclusao' => 66986,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 11:58:58',
			),
		array (
			'codigo' => 124,
			'codigo_grupo_homogeneo' => 34,
			'codigo_setor' => 2144,
			'codigo_cargo' => 3146,
			'codigo_usuario_inclusao' => 66986,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 11:58:58',
			),
		array (
			'codigo' => 123,
			'codigo_grupo_homogeneo' => 32,
			'codigo_setor' => 2144,
			'codigo_cargo' => 3144,
			'codigo_usuario_inclusao' => 66986,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 11:55:47',
			),
		array (
			'codigo' => 107,
			'codigo_grupo_homogeneo' => 30,
			'codigo_setor' => 2145,
			'codigo_cargo' => 3150,
			'codigo_usuario_inclusao' => 66984,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 11:42:37',
			),
		array (
			'codigo' => 106,
			'codigo_grupo_homogeneo' => 29,
			'codigo_setor' => 2143,
			'codigo_cargo' => 3149,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:01:12',
			),
		array (
			'codigo' => 105,
			'codigo_grupo_homogeneo' => 29,
			'codigo_setor' => 2140,
			'codigo_cargo' => 3148,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:01:12',
			),
		array (
			'codigo' => 104,
			'codigo_grupo_homogeneo' => 27,
			'codigo_setor' => 2128,
			'codigo_cargo' => 3140,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-03 17:10:18',
			),
		array (
			'codigo' => 103,
			'codigo_grupo_homogeneo' => 27,
			'codigo_setor' => 2128,
			'codigo_cargo' => 3137,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-03 17:10:18',
			),
		array (
			'codigo' => 102,
			'codigo_grupo_homogeneo' => 27,
			'codigo_setor' => 2127,
			'codigo_cargo' => 3137,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-03 17:10:18',
			),
		array (
			'codigo' => 98,
			'codigo_grupo_homogeneo' => 18,
			'codigo_setor' => 2114,
			'codigo_cargo' => 3116,
			'codigo_usuario_inclusao' => 2,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-07-27 18:24:53',
			),
		);
}



