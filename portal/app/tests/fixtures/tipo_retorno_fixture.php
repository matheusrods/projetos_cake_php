<?php
class TipoRetornoFixture extends CakeTestFixture {

	public $name = 'TipoRetorno';
	public $table = 'tipo_retorno';

	public $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary'
			),
		'descricao' => 
		array (
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
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
		'cliente' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'proprietario' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'profissional' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'usuario_interno' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);

	public $records = array (
		0 => 
		array (
			'codigo' => 11,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 1,
			'descricao' => 'MENSALIDADE',
			),
		1 => 
		array (
			'codigo' => 10,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 1,
			'descricao' => 'Ramal',
			),
		2 => 
		array (
			'codigo' => 9,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 1,
			'descricao' => 'SMS',
			),
		3 => 
		array (
			'codigo' => 8,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 1,
			'descricao' => '3G',
			),
		4 => 
		array (
			'codigo' => 7,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 1,
			'descricao' => 'CELULAR',
			),
		5 => 
		array (
			'codigo' => 6,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 1,
			'profissional' => 0,
			'usuario_interno' => 0,
			'descricao' => 'RADIO',
			),
		6 => 
		array (
			'codigo' => 5,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 1,
			'usuario_interno' => 0,
			'descricao' => 'CELULAR MOTORISTA',
			),
		7 => 
		array (
			'codigo' => 4,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 1,
			'proprietario' => 1,
			'profissional' => 0,
			'usuario_interno' => 0,
			'descricao' => '0800',
			),
		8 => 
		array (
			'codigo' => 3,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 0,
			'proprietario' => 0,
			'profissional' => 0,
			'usuario_interno' => 0,
			'descricao' => 'FAX',
			),
		9 => 
		array (
			'codigo' => 2,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:22:36',
			'cliente' => 1,
			'proprietario' => 1,
			'profissional' => 0,
			'usuario_interno' => 0,
			'descricao' => 'E-MAIL',
			),
		);
}



