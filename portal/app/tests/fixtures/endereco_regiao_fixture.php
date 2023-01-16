<?php
class EnderecoRegiaoFixture extends CakeTestFixture {

	public $name = 'EnderecoRegiao';
	public $table = 'endereco_regiao';

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
		array (
			'codigo' => 1,
			'codigo_usuario_inclusao' => 1,
			'data_inclusao' => '2017-05-08 08:21:32',
			'descricao' => 'CENTRO-OESTE'
			),
		array (
			'codigo' => 2,
			'codigo_usuario_inclusao' => 1,
			'data_inclusao' => '2017-05-08 08:21:32',
			'descricao' => 'NORDESTE'
			),
		array (
			'codigo' => 3,
			'codigo_usuario_inclusao' => 1,
			'data_inclusao' => '2017-05-08 08:21:32',
			'descricao' => 'NORTE'
			),
		array (
			'codigo' => 4,
			'codigo_usuario_inclusao' => 1,
			'data_inclusao' => '2017-05-08 08:21:32',
			'descricao' => 'SUDESTE'
			),
		array (
			'codigo' => 5,
			'codigo_usuario_inclusao' => 1,
			'data_inclusao' => '2017-05-08 08:21:32',
			'descricao' => 'SUL'
			)

		);
}



