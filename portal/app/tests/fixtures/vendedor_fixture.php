<?php
class VendedorFixture extends CakeTestFixture {
	var $name    = 'Vendedor';
	var $table   = 'vendedores';
	var $fields = array(
		'codigo' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_empresa' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'data_inclusao' => 
		array(
			'type' => 'datetime',
			'null' => false,
			'default' => '(getdate())',
			'length' => NULL,
			),
		'nome' => 
		array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => 255,
			),
		'codigo_usuario_inclusao' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	var $records = array(
		array(
			'codigo' => 5,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:05:43',
			'nome' => 'Marco Ruas',
			),
		array(
			'codigo' => 4,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:04:58',
			'nome' => 'Davi Albuquerque',
			),
		array(
			'codigo' => 3,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:04:34',
			'nome' => 'Amanda Siqueira',
			),
		array(
			'codigo' => 2,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 10:31:10',
			'nome' => 'Dana White',
			),
		array(
			'codigo' => 6,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 10:31:10',
			'nome' => 'Mauricio Shogun',
			),
		array(
			'codigo' => 7,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 10:31:10',
			'nome' => 'Erick Clapton',
			),
		array(
			'codigo' => 8,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 10:31:10',
			'nome' => 'Wanderlei Silva',
			),
		array(
			'codigo' => 9,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 10:31:10',
			'nome' => 'Anderson Silva',
			),
		);
}