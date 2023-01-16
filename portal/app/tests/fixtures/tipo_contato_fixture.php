<?php
class TipoContatoFixture extends CakeTestFixture {

	public $name = 'TipoContato';
	public $table = 'tipo_contato';

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
		);

	public $records = array (
		0 => 
		array (
			'codigo' => 7,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'REFERENCIA',
			),
		1 => 
		array (
			'codigo' => 6,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'REPRESENTANTE',
			),
		2 => 
		array (
			'codigo' => 5,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'NFE',
			),
		3 => 
		array (
			'codigo' => 4,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'ENTREGA',
			),
		4 => 
		array (
			'codigo' => 3,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'FINANCEIRO',
			),
		5 => 
		array (
			'codigo' => 2,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'COMERCIAL',
			),
		6 => 
		array (
			'codigo' => 1,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2015-11-27 08:21:32',
			'descricao' => 'RESIDENCIAL',
			),
		);
}



